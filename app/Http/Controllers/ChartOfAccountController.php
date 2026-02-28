<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartOfAccountController extends Controller
{
    /**
     * Display a listing of chart of accounts
     */
    public function index(Request $request)
    {
        $query = ChartOfAccount::query();

        // Filter by account type
        if ($request->filled('account_type')) {
            $query->where('account_type', $request->account_type);
        }

        // Search by code or name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('account_code', 'like', "%{$search}%")
                  ->orWhere('account_name', 'like', "%{$search}%");
            });
        }

        // Filter cash accounts only
        if ($request->filled('is_cash')) {
            $query->where('is_cash', $request->is_cash);
        }

        $accounts = $query->orderBy('account_code')->paginate(20);

        // Summary statistics with balance calculation
        // Accounting equation: Assets = Liabilities + Equity
        $assetBalance = JournalLine::join('chart_of_accounts', 'journal_lines.coa_id', '=', 'chart_of_accounts.account_code')
            ->where('chart_of_accounts.account_type', 'asset')
            ->selectRaw('SUM(debit) - SUM(credit) as balance')
            ->value('balance') ?? 0;

        $liabilityBalance = JournalLine::join('chart_of_accounts', 'journal_lines.coa_id', '=', 'chart_of_accounts.account_code')
            ->where('chart_of_accounts.account_type', 'liability')
            ->selectRaw('SUM(credit) - SUM(debit) as balance')
            ->value('balance') ?? 0;

        $equityBalance = JournalLine::join('chart_of_accounts', 'journal_lines.coa_id', '=', 'chart_of_accounts.account_code')
            ->where('chart_of_accounts.account_type', 'equity')
            ->selectRaw('SUM(credit) - SUM(debit) as balance')
            ->value('balance') ?? 0;

        $revenueBalance = JournalLine::join('chart_of_accounts', 'journal_lines.coa_id', '=', 'chart_of_accounts.account_code')
            ->where('chart_of_accounts.account_type', 'revenue')
            ->selectRaw('COALESCE(SUM(credit), 0) - COALESCE(SUM(debit), 0) as balance')
            ->value('balance') ?? 0;

        $expenseBalance = JournalLine::join('chart_of_accounts', 'journal_lines.coa_id', '=', 'chart_of_accounts.account_code')
            ->where('chart_of_accounts.account_type', 'expense')
            ->selectRaw('COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) as balance')
            ->value('balance') ?? 0;

        $stats = [
            'total' => ChartOfAccount::count(),
            'asset_count' => ChartOfAccount::where('account_type', 'asset')->count(),
            'liability_count' => ChartOfAccount::where('account_type', 'liability')->count(),
            'equity_count' => ChartOfAccount::where('account_type', 'equity')->count(),
            'revenue_count' => ChartOfAccount::where('account_type', 'revenue')->count(),
            'expense_count' => ChartOfAccount::where('account_type', 'expense')->count(),

            'asset_balance' => $assetBalance,
            // 'liability_balance' => $liabilityBalance,
            'equity_balance' => $equityBalance,
            'revenue_balance' => $revenueBalance,
            'expense_balance' => $expenseBalance,
        ];

        return view('chart-of-accounts.index', compact('accounts', 'stats'));
    }

    /**
     * Show the form for creating a new account
     */
    public function create()
    {
        $accountTypes = [
            'asset' => 'Asset (Aset)',
            'liability' => 'Liability (Kewajiban)',
            'equity' => 'Equity (Modal)',
            'revenue' => 'Revenue (Pendapatan)',
            'expense' => 'Expense (Beban)',
        ];

        return view('chart-of-accounts.create', compact('accountTypes'));
    }

    /**
     * Store a newly created account
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_code' => 'required|string|max:20|unique:chart_of_accounts,account_code',
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:asset,liability,equity,revenue,expense',
            'is_cash' => 'boolean',
        ]);

        $validated['is_cash'] = $request->has('is_cash') ? true : false;

        $account = ChartOfAccount::create($validated);

        return redirect()
            ->route('chart-of-accounts.index')
            ->with('success', 'Account berhasil ditambahkan: ' . $account->account_code . ' - ' . $account->account_name);
    }

    /**
     * Display the specified account
     */
    public function show($id)
    {
        $account = ChartOfAccount::findOrFail($id);

        // Get usage count in journal lines
        $usageCount = JournalLine::where('coa_id', $account->account_code)->count();

        // Get recent transactions
        $recentTransactions = DB::table('journal_lines')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id')
            ->where('journal_lines.coa_id', $account->account_code)
            ->select(
                'journal_entries.journal_date',
                'journal_entries.description',
                'journal_lines.debit',
                'journal_lines.credit'
            )
            ->orderBy('journal_entries.journal_date', 'desc')
            ->limit(10)
            ->get();

        // Calculate balance
        $balance = JournalLine::where('coa_id', $account->account_code)
            ->selectRaw('SUM(debit) - SUM(credit) as balance')
            ->value('balance') ?? 0;

        return view('chart-of-accounts.show', compact('account', 'usageCount', 'recentTransactions', 'balance'));
    }

    /**
     * Show the form for editing the specified account
     */
    public function edit($id)
    {
        $account = ChartOfAccount::findOrFail($id);

        $accountTypes = [
            'asset' => 'Asset (Aset)',
            'liability' => 'Liability (Kewajiban)',
            'equity' => 'Equity (Modal)',
            'revenue' => 'Revenue (Pendapatan)',
            'expense' => 'Expense (Beban)',
        ];

        // Check if account has transactions
        $hasTransactions = JournalLine::where('coa_id', $account->account_code)->exists();

        return view('chart-of-accounts.edit', compact('account', 'accountTypes', 'hasTransactions'));
    }

    /**
     * Update the specified account
     */
    public function update(Request $request, $id)
    {
        $account = ChartOfAccount::findOrFail($id);

        $validated = $request->validate([
            'account_code' => 'required|string|max:20|unique:chart_of_accounts,account_code,' . $id,
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:asset,liability,equity,revenue,expense',
            'is_cash' => 'boolean',
        ]);

        $validated['is_cash'] = $request->has('is_cash') ? true : false;

        // Check if account_code changed and has transactions
        if ($account->account_code !== $validated['account_code']) {
            $hasTransactions = JournalLine::where('account_code', $account->account_code)->exists();
            
            if ($hasTransactions) {
                return redirect()
                    ->back()
                    ->withErrors(['account_code' => 'Tidak dapat mengubah kode akun yang sudah memiliki transaksi.'])
                    ->withInput();
            }
        }

        $account->update($validated);

        return redirect()
            ->route('chart-of-accounts.index')
            ->with('success', 'Account berhasil diupdate: ' . $account->account_code . ' - ' . $account->account_name);
    }

    /**
     * Remove the specified account
     */
    public function destroy($id)
    {
        $account = ChartOfAccount::findOrFail($id);

        // Check if account has been used in any transactions
        $usageCount = JournalLine::where('account_code', $account->account_code)->count();

        if ($usageCount > 0) {
            return redirect()
                ->back()
                ->withErrors(['delete' => "Tidak dapat menghapus akun yang sudah memiliki {$usageCount} transaksi."]);
        }

        $accountInfo = $account->account_code . ' - ' . $account->account_name;
        $account->delete();

        return redirect()
            ->route('chart-of-accounts.index')
            ->with('success', 'Account berhasil dihapus: ' . $accountInfo);
    }

    /**
     * Get accounts by type (AJAX endpoint)
     */
    public function getByType(Request $request)
    {
        $type = $request->get('type');
        
        $accounts = ChartOfAccount::where('account_type', $type)
            ->orderBy('account_code')
            ->get(['id', 'account_code', 'account_name']);

        return response()->json($accounts);
    }

    /**
     * Get cash/bank accounts only (AJAX endpoint)
     */
    public function getCashAccounts()
    {
        $accounts = ChartOfAccount::where('is_cash', true)
            ->orderBy('account_code')
            ->get(['id', 'account_code', 'account_name']);

        return response()->json($accounts);
    }
}