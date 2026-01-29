<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\BeatInvoice;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $query = Payment::with('invoice');

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
            ->paginate(20);

        // Summary statistics
        $stats = $this->getStatistics($request);

        return view('payments.index', compact('payments', 'stats'));
    }

    /**
     * Show the form for creating a new payment
     */
    public function create(Request $request)
    {
        // Get unpaid/partial invoices
        $invoices = BeatInvoice::whereRaw('
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
        $cashAccounts = ChartOfAccount::where('is_cash', true)
            ->orderBy('account_code')
            ->get();

        // Pre-select invoice if passed via query param
        $selectedInvoiceId = $request->get('invoice_id');

        return view('payments.create', compact('invoices', 'cashAccounts', 'selectedInvoiceId'));
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:beat_invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,bank',
            'cash_account_id' => 'required|exists:chart_of_accounts,id',
            'reference' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Get invoice
            $invoice = BeatInvoice::findOrFail($validated['invoice_id']);

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
     * Display the specified payment
     */
    public function show($id)
    {
        $payment = Payment::with(['invoice.staging'])->findOrFail($id);

        // Get journal entry
        $journalEntry = JournalEntry::where('source_type', 'payment')
            ->where('source_id', $payment->id)
            ->with('lines')
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

            $payment = Payment::findOrFail($id);
            $invoice = $payment->invoice;

            // Check if payment can be voided (misalnya tidak boleh void payment yang sudah lebih dari 30 hari)
            $daysSincePayment = Carbon::parse($payment->payment_date)->diffInDays(now());
            
            if ($daysSincePayment > 30) {
                return redirect()
                    ->back()
                    ->withErrors(['void' => 'Tidak dapat membatalkan pembayaran yang sudah lebih dari 30 hari.']);
            }

            // 1. Delete journal entry
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
     * Create journal entry for payment
     */
    private function createJournalEntry(Payment $payment, $cashAccountId)
    {
        $invoice = $payment->invoice;
        $cashAccount = ChartOfAccount::findOrFail($cashAccountId);

        // Create journal entry header
        $journalEntry = JournalEntry::create([
            'journal_date' => $payment->payment_date,
            'description' => "Pembayaran dari {$invoice->customer_name} - Invoice {$invoice->pppoe}",
            'source_type' => 'payment',
            'source_id' => $payment->id,
            'reference_no' => $payment->reference,
            'total_debit' => $payment->amount,
            'total_credit' => $payment->amount,
        ]);

        // Debit: Cash/Bank (asset increases)
        JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_code' => $cashAccount->account_code,
            'account_name' => $cashAccount->account_name,
            'debit' => $payment->amount,
            'credit' => 0,
        ]);

        // Credit: Accounts Receivable (asset decreases)
        // Assuming AR account code is 1103
        $arAccount = ChartOfAccount::where('account_code', '1103')->first();
        
        if (!$arAccount) {
            throw new \Exception('Akun Piutang Usaha (1103) tidak ditemukan.');
        }

        JournalLine::create([
            'journal_entry_id' => $journalEntry->id,
            'account_code' => $arAccount->account_code,
            'account_name' => $arAccount->account_name,
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
        $totalPaid = $invoice->payments()->sum('amount');

        if ($totalPaid <= 0) {
            $invoice->update(['status' => 'issued']); // or 'unpaid'
        } elseif ($totalPaid < $invoice->total_amount) {
            $invoice->update(['status' => 'partial']);
        } else {
            $invoice->update(['status' => 'paid']);
        }
    }

    /**
     * Get payment statistics
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

        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('payment_date', $request->month)
                  ->whereYear('payment_date', $request->year);
        }

        return [
            'total_payments' => $query->count(),
            'total_amount' => $query->sum('amount'),
            'cash_payments' => Payment::where('method', 'cash')->sum('amount'),
            'bank_payments' => Payment::where('method', 'bank')->sum('amount'),
            'this_month' => Payment::whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),
            'today' => Payment::whereDate('payment_date', now()->toDateString())
                ->sum('amount'),
        ];
    }

    /**
     * Export payments to CSV
     */
    public function export(Request $request)
    {
        $query = Payment::with('invoice');

        // Apply filters
        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
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