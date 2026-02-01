<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Payment;
use App\Models\BeatInvoice;
use App\Models\JournalLine;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments with eager loading
     */
    public function index(Request $request)
    {
        // Eager load invoice with payments to calculate status and avoid N+1
        $query = Payment::with([
            'invoice' => function($q) {
                $q->select('id', 'customer_name', 'pppoe', 'package_name', 'period_month', 'period_year', 'total_amount', 'status');
            },
            'invoice.payments' => function($q) {
                $q->select('id', 'invoice_id', 'amount');
            }
        ]);

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        // // Filter by payment method
        // if ($request->filled('method')) {
        //     $query->where('method', $request->method);
        // }

        // Filter by month & year
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('payment_date', $request->month)
                  ->whereYear('payment_date', $request->year);
        }

        // Search by reference or invoice
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('invoice', function($iq) use ($search) {
                      $iq->where('customer_name', 'like', "%{$search}%")
                         ->orWhere('pppoe', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->appends($request->except('page')); // Maintain filter parameters in pagination

        // Summary statistics
        $stats = $this->getStatistics($request);
        
        // Cache cash accounts for 1 hour to reduce queries
        $cashAccounts = Cache::remember('cash_accounts', 3600, function() {
            return ChartOfAccount::where('account_type', 'asset')
                ->where('is_cash', true)
                ->orderBy('account_code')
                ->get(['id', 'account_code', 'account_name']);
        });

        return view('payments.index', compact('payments', 'cashAccounts', 'stats'));
    }

    /**
     * Show the form for creating a new payment
     */
    public function create(Request $request)
    {
        // Get unpaid/partial invoices with eager loading
        $invoices = BeatInvoice::with(['payments' => function($q) {
            $q->select('id', 'invoice_id', 'amount');
        }])
            ->select('id', 'customer_name', 'pppoe', 'package_name', 'period_month', 'period_year', 'total_amount', 'billing_day')
            ->whereRaw('
                (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE invoice_id = beat_invoices.id) < total_amount
            ')
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get()
            ->map(function($invoice) {
                $paidAmount = $invoice->payments->sum('amount');
                $invoice->paid_amount = $paidAmount;
                $invoice->outstanding = max(0, $invoice->total_amount - $paidAmount);
                return $invoice;
            });

        // Get cash/bank accounts for payment method
        $cashAccounts = Cache::remember('cash_accounts', 3600, function() {
            return ChartOfAccount::where('account_type', 'asset')
                ->where('is_cash', true)
                ->orderBy('account_code')
                ->get(['id', 'account_code', 'account_name']);
        });

        // Pre-select invoice if passed via query param
        $selectedInvoiceId = $request->get('invoice_id');
        $selectedInvoice = null;
        
        if ($selectedInvoiceId) {
            $selectedInvoice = $invoices->firstWhere('id', $selectedInvoiceId);
        }

        return view('payments.create', compact('invoices', 'cashAccounts', 'selectedInvoiceId', 'selectedInvoice'));
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:beat_invoices,id',
            'payment_date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:cash,bank',
            'cash_account_id' => 'required|exists:chart_of_accounts,id',
            'reference' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Get invoice with payments (eager load)
            $invoice = BeatInvoice::with(['payments' => function($q) {
                $q->select('id', 'invoice_id', 'amount');
            }])->findOrFail($validated['invoice_id']);

            // Validate invoice not already fully paid
            if ($invoice->status === 'paid') {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['invoice_id' => 'Invoice ini sudah lunas.']);
            }

            // Calculate outstanding
            $paidAmount = $invoice->payments->sum('amount');
            $outstanding = max(0, $invoice->total_amount - $paidAmount);

            // Validate amount tidak melebihi outstanding
            if ($validated['amount'] > $outstanding) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['amount' => "Jumlah pembayaran tidak boleh melebihi outstanding: Rp " . number_format($outstanding, 0, ',', '.')]);
            }

            // Validate cash account type
            $cashAccount = ChartOfAccount::findOrFail($validated['cash_account_id']);
            if (!$cashAccount->is_cash || $cashAccount->account_type !== 'asset') {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['cash_account_id' => 'Akun yang dipilih bukan akun kas/bank.']);
            }

            // 1. Create payment record
            $payment = Payment::create([
                'invoice_id' => $validated['invoice_id'],
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'method' => $validated['method'],
                'reference' => $validated['reference'],
                'note' => $validated['note'],
            ]);

            // 2. Create journal entry
            $this->createJournalEntry($payment, $validated['cash_account_id']);

            // 3. Update invoice status
            $this->updateInvoiceStatus($invoice);

            // Clear cache
            Cache::forget('cash_accounts');

            DB::commit();

            return redirect()
                ->route('payments.show', $payment->id)
                ->with('success', 'Pembayaran berhasil dicatat sebesar Rp ' . number_format($payment->amount, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified payment with eager loading
     */
    public function show($id)
    {
        $payment = Payment::with([
            'invoice' => function($q) {
                $q->select('id', 'customer_name', 'pppoe', 'package_name', 'period_month', 'period_year', 'billing_day', 'total_amount', 'status');
            },
            'invoice.staging' => function($q) {
                $q->select('id', 'customer_name', 'pppoe');
            },
            'invoice.payments' => function($q) {
                $q->select('id', 'invoice_id', 'payment_date', 'amount', 'method', 'reference')
                  ->orderBy('payment_date');
            }
        ])->findOrFail($id);

        // Get journal entry with lines (eager load)
        $journalEntry = JournalEntry::with(['lines' => function($q) {
            $q->select('id', 'journal_entry_id', 'coa_id', 'debit', 'credit')
              ->orderBy('debit', 'desc');
        }])
            ->where('source_type', 'payment')
            ->where('source_id', $payment->id)
            ->first();

        // Calculate invoice payment summary
        $invoice = $payment->invoice;
        $totalPaid = $invoice->payments->sum('amount');
        $outstanding = max(0, $invoice->total_amount - $totalPaid);

        return view('payments.show', compact('payment', 'journalEntry', 'totalPaid', 'outstanding'));
    }

    /**
     * Void/Cancel a payment
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $payment = Payment::with('invoice')->findOrFail($id);
            $invoice = $payment->invoice;

            // Check if payment can be voided
            $daysSincePayment = Carbon::parse($payment->payment_date)->diffInDays(now());
            
            if ($daysSincePayment > 30) {
                return redirect()
                    ->back()
                    ->withErrors(['void' => 'Tidak dapat membatalkan pembayaran yang sudah lebih dari 30 hari.']);
            }

            // 1. Delete journal entry and lines
            $journalEntry = JournalEntry::where('source_type', 'payment')
                ->where('source_id', $payment->id)
                ->first();

            if ($journalEntry) {
                JournalLine::where('journal_entry_id', $journalEntry->id)->delete();
                $journalEntry->delete();
            }

            // 2. Delete payment
            $paymentAmount = $payment->amount;
            $payment->delete();

            // 3. Update invoice status
            $this->updateInvoiceStatus($invoice);

            DB::commit();

            return redirect()
                ->route('payments.index')
                ->with('success', 'Pembayaran sebesar Rp ' . number_format($paymentAmount, 0, ',', '.') . ' berhasil dibatalkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withErrors(['error' => 'Gagal membatalkan pembayaran: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate payment receipt PDF
     */
    public function receipt($id)
    {
        $payment = Payment::with([
            'invoice' => function($q) {
                $q->select('id', 'customer_name', 'pppoe', 'package_name', 'period_month', 'period_year', 'billing_day', 'total_amount');
            },
            'invoice.staging' => function($q) {
                $q->select('id', 'customer_name', 'pppoe');
            },
            'invoice.payments' => function($q) {
                $q->select('id', 'invoice_id', 'payment_date', 'amount', 'method', 'reference')
                  ->orderBy('payment_date');
            }
        ])->findOrFail($id);
        
        $invoice = $payment->invoice;
        $totalPaid = $invoice->payments->sum('amount');
        $outstanding = max(0, $invoice->total_amount - $totalPaid);

        // Company info - you can move this to config file
        $company = [
            'name' => config('app.company_name', 'DHS FINANCE'),
            'address' => config('app.company_address', 'Jl. ISP Provider No. 123'),
            'phone' => config('app.company_phone', '(021) 1234-5678'),
            'email' => config('app.company_email', 'admin@dhsfinance.com'),
            'website' => config('app.company_website', 'www.dhsfinance.com'),
            // 'logo' => public_path('images/logo.png'), // Uncomment if you have logo
        ];

        $pdf = Pdf::loadView('payments.receipt', compact('payment', 'invoice', 'totalPaid', 'outstanding', 'company'));
        
        return $pdf->download('Receipt-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT) . '-' . now()->format('Ymd') . '.pdf');
    }

    /**
     * Get invoice details API
     */
    public function getInvoiceDetails($invoiceId)
    {
        $invoice = BeatInvoice::with([
            'payments' => function($q) {
                $q->select('id', 'invoice_id', 'payment_date', 'amount', 'method');
            },
            'staging' => function($q) {
                $q->select('id', 'customer_name', 'pppoe');
            }
        ])->findOrFail($invoiceId);
        
        $paidAmount = $invoice->payments->sum('amount');
        $outstanding = max(0, $invoice->total_amount - $paidAmount);

        return response()->json([
            'id' => $invoice->id,
            'customer_name' => $invoice->customer_name,
            'pppoe' => $invoice->pppoe,
            'package_name' => $invoice->package_name,
            'period' => $invoice->period_month . '/' . $invoice->period_year,
            'billing_day' => $invoice->billing_day,
            'total_amount' => $invoice->total_amount,
            'paid_amount' => $paidAmount,
            'outstanding' => $outstanding,
            'status' => $invoice->status,
            'payments' => $invoice->payments->map(function($p) {
                return [
                    'id' => $p->id,
                    'payment_date' => $p->payment_date,
                    'amount' => $p->amount,
                    'method' => $p->method,
                ];
            }),
        ]);
    }

    /**
     * Create journal entry for payment
     */
    private function createJournalEntry(Payment $payment, $cashAccountId)
    {
        $invoice = $payment->invoice;
        $cashAccount = ChartOfAccount::findOrFail($cashAccountId);

        // Create journal entry header
        $journalEntry = JournalEntry::create([
            'journal_date' => $payment->payment_date,
            'description' => "Pembayaran dari {$invoice->customer_name} - Invoice {$invoice->pppoe} - Period {$invoice->period_month}/{$invoice->period_year}",
            'source_type' => 'payment',
            'source_id' => $payment->id,
            'reference_no' => $payment->reference,
            'total_debit' => $payment->amount,
            'total_credit' => $payment->amount,
        ]);

        // Debit: Cash/Bank (asset increases)
        JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
           'coa_id' => $cashAccount->id,
            'debit' => $payment->amount,
            'credit' => 0,
        ]);

        // Credit: Accounts Receivable (asset decreases)
        $arAccount = ChartOfAccount::where('account_code', '1201')->first();
        
        if (!$arAccount) {
            throw new \Exception('Akun Piutang Usaha (1201) tidak ditemukan. Silakan buat akun terlebih dahulu.');
        }

        JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'coa_id' => $arAccount->id,
            'debit' => 0,
            'credit' => $payment->amount,
        ]);

        return $journalEntry;
    }

    /**
     * Update invoice payment status based on payments
     */
    private function updateInvoiceStatus(BeatInvoice $invoice)
    {
        $invoice->refresh(); // Refresh to get latest payments
        $totalPaid = $invoice->payments()->sum('amount');

        if ($totalPaid <= 0) {
            $invoice->update(['status' => 'issued']);
        } elseif ($totalPaid < $invoice->total_amount) {
            $invoice->update(['status' => 'partial']);
        } else {
            $invoice->update(['status' => 'paid']);
        }
    }

    /**
     * Get payment statistics with optimized queries
     */
    private function getStatistics(Request $request)
    {
        $query = Payment::query();

        // Apply same filters
        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        // if ($request->filled('method')) {
        //     $query->where('method', $request->method);
        // }

        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('payment_date', $request->month)
                  ->whereYear('payment_date', $request->year);
        }

        // Get aggregated stats in single query
        $stats = DB::table('payments')
            ->when($request->filled('date_from'), function($q) use ($request) {
                return $q->where('payment_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function($q) use ($request) {
                return $q->where('payment_date', '<=', $request->date_to);
            })
            // ->when($request->filled('method'), function($q) use ($request) {
            //     return $q->where('method', $request->method);
            // })
            ->when($request->filled('month') && $request->filled('year'), function($q) use ($request) {
                return $q->whereMonth('payment_date', $request->month)
                         ->whereYear('payment_date', $request->year);
            })
            ->selectRaw('
                COUNT(*) as total_payments,
                SUM(amount) as total_amount,
                SUM(CASE WHEN method = "cash" THEN amount ELSE 0 END) as cash_payments,
                SUM(CASE WHEN method = "bank" THEN amount ELSE 0 END) as bank_payments
            ')
            ->first();

        // This month stats
        $thisMonth = Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        return [
            'total_payments' => $stats->total_payments ?? 0,
            'total_amount' => $stats->total_amount ?? 0,
            'cash_payments' => $stats->cash_payments ?? 0,
            'bank_payments' => $stats->bank_payments ?? 0,
            'this_month' => $thisMonth,
            'today' => Payment::whereDate('payment_date', now()->toDateString())
                ->sum('amount'),
        ];
    }

    /**
     * Export payments to CSV
     */
    public function export(Request $request)
    {
        $query = Payment::with(['invoice' => function($q) {
            $q->select('id', 'customer_name', 'pppoe', 'period_month', 'period_year');
        }]);

        // Apply filters
        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        // if ($request->filled('method')) {
        //     $query->where('method', $request->method);
        // }

        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('payment_date', $request->month)
                  ->whereYear('payment_date', $request->year);
        }

        $payments = $query->orderBy('payment_date')->get();

        // Prepare CSV
        $csvData = [];
        $csvData[] = [
            'Payment ID',
            'Payment Date',
            'Customer Name',
            'PPPoE',
            'Invoice Period',
            'Amount',
            'Method',
            'Reference',
            'Note'
        ];

        foreach ($payments as $payment) {
            $invoice = $payment->invoice;
            $csvData[] = [
                $payment->id,
                $payment->payment_date,
                $invoice->customer_name,
                $invoice->pppoe,
                $invoice->period_month . '/' . $invoice->period_year,
                $payment->amount,
                $payment->method,
                $payment->reference,
                $payment->note,
            ];
        }

        // Generate CSV
        $filename = 'payments_' . now()->format('Y-m-d_His') . '.csv';
        
        $handle = fopen('php://temp', 'r+');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}