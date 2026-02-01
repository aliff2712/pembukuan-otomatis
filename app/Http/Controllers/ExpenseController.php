<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Services\ExpenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    /**
     * Display a listing of expenses
     */
    public function index(Request $request)
    {
        $query = Expense::with(['expenseAccount', 'cashAccount']);

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        // Filter by month & year
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('expense_date', $request->month)
                  ->whereYear('expense_date', $request->year);
        }

        // Filter by expense account
        if ($request->filled('expense_account_id')) {
            $query->where('expense_coa_id', $request->expense_account_id);
        }

        // Filter by cash/bank account
        if ($request->filled('cash_account_id')) {
            $query->where('cash_coa_id', $request->cash_account_id);
        }

        // Search by description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $expenses = $query->orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        // Summary statistics
        $stats = $this->getStatistics($request);

        // Get accounts for filter dropdowns
        $expenseAccounts = ChartOfAccount::where('account_type', 'expense')
            ->orderBy('account_code')
            ->get();

        $cashAccounts = ChartOfAccount::where('is_cash', true)
            ->orderBy('account_code')
            ->get();

        return view('expenses.index', compact('expenses', 'stats', 'expenseAccounts', 'cashAccounts'));
    }

    /**
     * Show the form for creating a new expense
     */
    public function create()
    {
        // Get expense accounts (account_type = 'expense')
        $expenseAccounts = ChartOfAccount::where('account_type', 'expense')
            ->orderBy('account_code')
            ->get();

        // Get cash/bank accounts (is_cash = true)
         $cashAccounts = ChartOfAccount::where('account_type', 'asset')
        ->where('is_cash', true)
        ->orderBy('account_code')
        ->get();

        // Check if accounts exist
        if ($expenseAccounts->isEmpty()) {
            return redirect()
                ->route('chart-of-accounts.index')
                ->withErrors(['error' => 'Belum ada akun beban (expense). Silakan buat akun terlebih dahulu.']);
        }

        if ($cashAccounts->isEmpty()) {
            return redirect()
                ->route('chart-of-accounts.index')
                ->withErrors(['error' => 'Belum ada akun kas/bank. Silakan buat akun terlebih dahulu.']);
        }

        return view('expenses.create', compact('expenseAccounts', 'cashAccounts'));
    }

    /**
     * Store a newly created expense
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'expense_coa_id' => 'required|exists:chart_of_accounts,id',
            'cash_coa_id' => 'required|exists:chart_of_accounts,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:1000',
        ]);

        try {
            // Use ExpenseService to create expense + journal entry
            $expense = $this->expenseService->record($validated);

            return redirect()
                ->route('expenses.show', $expense->id)
                ->with('success', 'Expense berhasil dicatat sebesar Rp ' . number_format($expense->amount, 0, ',', '.'));

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified expense
     */
    public function show($id)
    {
        $expense = Expense::with(['expenseAccount', 'cashAccount'])->findOrFail($id);

        // Get related journal entry
        $journalEntry = JournalEntry::where('source_type', 'expense')
            ->where('source_id', $expense->id)
            ->with('lines')
            ->first();

        return view('expenses.show', compact('expense', 'journalEntry'));
    }

    /**
     * Show the form for editing the specified expense
     */
    public function edit($id)
    {
        $expense = Expense::findOrFail($id);

        // Get expense accounts
        $expenseAccounts = ChartOfAccount::where('account_type', 'expense')
            ->orderBy('account_code')
            ->get();

        // Get cash/bank accounts
        $cashAccounts = ChartOfAccount::where('is_cash', true)
            ->orderBy('account_code')
            ->get();

        return view('expenses.edit', compact('expense', 'expenseAccounts', 'cashAccounts'));
    }

    /**
     * Update the specified expense
     */
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $validated = $request->validate([
            'expense_date' => 'required|date',
            'expense_coa_id' => 'required|exists:chart_of_accounts,id',
            'cash_coa_id' => 'required|exists:chart_of_accounts,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Update expense
            $expense->update($validated);

            // Update related journal entry
            $journalEntry = JournalEntry::where('source_type', 'expense')
                ->where('source_id', $expense->id)
                ->first();

            if ($journalEntry) {
                // Update journal entry header
                $journalEntry->update([
                    'journal_date' => $validated['expense_date'],
                    'description' => $validated['description'],
                    'total_debit' => $validated['amount'],
                    'total_credit' => $validated['amount'],
                ]);

                // Delete old lines
                JournalLine::where('journal_entry_id', $journalEntry->id)->delete();

                // Get account info
                $expenseAccount = ChartOfAccount::findOrFail($validated['expense_coa_id']);
                $cashAccount = ChartOfAccount::findOrFail($validated['cash_coa_id']);

                // Create new lines - Debit: Expense
                JournalLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_code' => $expenseAccount->account_code,
                    'account_name' => $expenseAccount->account_name,
                    'debit' => $validated['amount'],
                    'credit' => 0,
                ]);

                // Create new lines - Credit: Cash/Bank
                JournalLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_code' => $cashAccount->account_code,
                    'account_name' => $cashAccount->account_name,
                    'debit' => 0,
                    'credit' => $validated['amount'],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('expenses.show', $expense->id)
                ->with('success', 'Expense berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified expense
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $expense = Expense::findOrFail($id);

            // Delete related journal entry and lines
            $journalEntry = JournalEntry::where('source_type', 'expense')
                ->where('source_id', $expense->id)
                ->first();

            if ($journalEntry) {
                JournalLine::where('journal_entry_id', $journalEntry->id)->delete();
                $journalEntry->delete();
            }

            // Delete expense
            $amount = $expense->amount;
            $description = $expense->description;
            $expense->delete();

            DB::commit();

            return redirect()
                ->route('expenses.index')
                ->with('success', 'Expense sebesar Rp ' . number_format($amount, 0, ',', '.') . ' berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withErrors(['error' => 'Gagal menghapus expense: ' . $e->getMessage()]);
        }
    }

    /**
     * Get statistics for summary cards
     */
    private function getStatistics(Request $request)
    {
        $query = Expense::query();

        // Apply same filters as index
        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('expense_date', $request->month)
                  ->whereYear('expense_date', $request->year);
        }

        return [
            'total_expenses' => $query->count(),
            'total_amount' => $query->sum('amount'),
            'average_amount' => $query->avg('amount') ?? 0,
            'this_month' => Expense::whereMonth('expense_date', now()->month)
                ->whereYear('expense_date', now()->year)
                ->sum('amount'),
            'last_month' => Expense::whereMonth('expense_date', now()->subMonth()->month)
                ->whereYear('expense_date', now()->subMonth()->year)
                ->sum('amount'),
            'today' => Expense::whereDate('expense_date', now()->toDateString())
                ->sum('amount'),
        ];
    }

    /**
     * Export expenses to CSV
     */
    public function export(Request $request)
    {
        $query = Expense::with(['expenseAccount', 'cashAccount']);

        // Apply filters
        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->orderBy('expense_date')->get();

        // Prepare CSV
        $csvData = [];
        $csvData[] = [
            'Expense ID',
            'Date',
            'Expense Account',
            'Cash/Bank Account',
            'Amount',
            'Description'
        ];

        foreach ($expenses as $expense) {
            $csvData[] = [
                $expense->id,
                $expense->expense_date,
                $expense->expenseAccount->account_code . ' - ' . $expense->expenseAccount->account_name,
                $expense->cashAccount->account_code . ' - ' . $expense->cashAccount->account_name,
                $expense->amount,
                $expense->description,
            ];
        }

        // Generate CSV
        $filename = 'expenses_' . now()->format('Y-m-d_His') . '.csv';

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

    /**
     * Get expense summary by account (for reports)
     */
    public function summaryByAccount(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->endOfMonth()->toDateString());

        $summary = Expense::with('expenseAccount')
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->selectRaw('expense_coa_id, SUM(amount) as total_amount, COUNT(*) as count')
            ->groupBy('expense_coa_id')
            ->orderByDesc('total_amount')
            ->get();

        return view('expenses.summary', compact('summary', 'dateFrom', 'dateTo'));
    }
}