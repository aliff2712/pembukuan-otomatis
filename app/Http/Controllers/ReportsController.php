<?php

namespace App\Http\Controllers;

use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function ledger(LedgerService $ledger)
    {
        $month = request('month') ? intval(request('month')) : now()->month;
        $year = request('year') ? intval(request('year')) : now()->year;

        $rows = $ledger->monthly($year, $month);

        return view('reports.ledger', compact('rows', 'month', 'year'));
    }

    public function incomeStatement(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        // Query untuk revenue dan expense
        $rows = DB::table('journal_lines as jl')
            ->join('journal_entries as je', 'je.id', '=', 'jl.journal_entry_id')
            ->join('chart_of_accounts as coa', 'coa.id', '=', 'jl.coa_id')
            ->whereIn('coa.account_type', ['revenue', 'expense'])
            ->whereDate('je.journal_date', '>=', $from)
            ->whereDate('je.journal_date', '<=', $to)
            ->groupBy('coa.account_type', 'coa.account_code', 'coa.account_name', 'coa.id')
            ->select(
                'coa.id',
                'coa.account_type',
                'coa.account_code',
                'coa.account_name',
                DB::raw('SUM(jl.debit) as total_debit'),
                DB::raw('SUM(jl.credit) as total_credit'),
                // Revenue: credit - debit (saldo normal credit)
                // Expense: debit - credit (saldo normal debit)
                DB::raw("CASE 
                    WHEN coa.account_type = 'revenue' THEN SUM(jl.credit - jl.debit)
                    WHEN coa.account_type = 'expense' THEN SUM(jl.debit - jl.credit)
                    ELSE 0 
                END as amount")
            )
            ->orderBy('coa.account_type')
            ->orderBy('coa.account_code')
            ->get();

        // Kelompokkan berdasarkan tipe
        $revenues = $rows->where('account_type', 'revenue');
        $expenses = $rows->where('account_type', 'expense');

        // Hitung total
        $totalRevenue = $revenues->sum('amount');
        $totalExpense = $expenses->sum('amount');
        $grossProfit = $totalRevenue;
        $operatingProfit = $totalRevenue - $totalExpense;
        $netProfit = $operatingProfit; // Bisa ditambahkan other income/expense

        return view('reports.income-statement', compact(
            'revenues',
            'expenses',
            'totalRevenue',
            'totalExpense',
            'grossProfit',
            'operatingProfit',
            'netProfit',
            'from',
            'to'
        ));
    }

    public function balanceSheet(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $startOfYear = now()->startOfYear()->toDateString();

        // Query untuk Asset, Liability, Equity sampai tanggal tertentu
        $rows = DB::table('chart_of_accounts as coa')
            ->leftJoin('journal_lines as jl', function($join) use ($date) {
                $join->on('jl.coa_id', '=', 'coa.id')
                     ->whereExists(function($query) use ($date) {
                         $query->select(DB::raw(1))
                               ->from('journal_entries as je')
                               ->whereColumn('je.id', 'jl.journal_entry_id')
                               ->whereDate('je.journal_date', '<=', $date);
                     });
            })
            ->leftJoin('journal_entries as je', function($join) use ($date) {
                $join->on('je.id', '=', 'jl.journal_entry_id')
                     ->whereDate('je.journal_date', '<=', $date);
            })
            ->whereIn('coa.account_type', ['asset', 'liability', 'equity'])
            ->groupBy('coa.id', 'coa.account_type', 'coa.account_code', 'coa.account_name')
            ->select(
                'coa.id',
                'coa.account_type',
                'coa.account_code',
                'coa.account_name',
                DB::raw('COALESCE(SUM(jl.debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(jl.credit), 0) as total_credit'),
                // Asset: debit - credit (saldo normal debit)
                // Liability & Equity: credit - debit (saldo normal credit)
                DB::raw("CASE 
                    WHEN coa.account_type = 'asset' THEN COALESCE(SUM(jl.debit - jl.credit), 0)
                    WHEN coa.account_type IN ('liability', 'equity') THEN COALESCE(SUM(jl.credit - jl.debit), 0)
                    ELSE 0 
                END as balance")
            )
            ->orderBy('coa.account_type')
            ->orderBy('coa.account_code')
            ->get();

        // Hitung laba/rugi periode berjalan (YTD)
        $netIncome = $this->calculateNetIncome($startOfYear, $date);

        // Kelompokkan
        $assets = $rows->where('account_type', 'asset')->filter(fn($r) => $r->balance != 0);
        $liabilities = $rows->where('account_type', 'liability')->filter(fn($r) => $r->balance != 0);
        $equity = $rows->where('account_type', 'equity')->filter(fn($r) => $r->balance != 0);

        // Hitung total
        $totalAssets = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity = $equity->sum('balance') + $netIncome;
        $totalLiabilitiesEquity = $totalLiabilities + $totalEquity;

        return view('reports.balance-sheet', compact(
            'assets',
            'liabilities',
            'equity',
            'netIncome',
            'totalAssets',
            'totalLiabilities',
            'totalEquity',
            'totalLiabilitiesEquity',
            'date'
        ));
    }

    /**
     * Hitung Net Income untuk periode tertentu
     */
    private function calculateNetIncome($from, $to)
    {
        $result = DB::table('journal_lines as jl')
            ->join('journal_entries as je', 'je.id', '=', 'jl.journal_entry_id')
            ->join('chart_of_accounts as coa', 'coa.id', '=', 'jl.coa_id')
            ->whereIn('coa.account_type', ['revenue', 'expense'])
            ->whereDate('je.journal_date', '>=', $from)
            ->whereDate('je.journal_date', '<=', $to)
            ->select(
                DB::raw("SUM(CASE WHEN coa.account_type = 'revenue' THEN jl.credit - jl.debit ELSE 0 END) as total_revenue"),
                DB::raw("SUM(CASE WHEN coa.account_type = 'expense' THEN jl.debit - jl.credit ELSE 0 END) as total_expense")
            )
            ->first();

        return ($result->total_revenue ?? 0) - ($result->total_expense ?? 0);
    }

    public function trialBalance(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        $rows = DB::table('chart_of_accounts as coa')
            ->leftJoin('journal_lines as jl', function($join) use ($date) {
                $join->on('jl.coa_id', '=', 'coa.id')
                     ->whereExists(function($query) use ($date) {
                         $query->select(DB::raw(1))
                               ->from('journal_entries as je')
                               ->whereColumn('je.id', 'jl.journal_entry_id')
                               ->whereDate('je.journal_date', '<=', $date);
                     });
            })
            ->leftJoin('journal_entries as je', function($join) use ($date) {
                $join->on('je.id', '=', 'jl.journal_entry_id')
                     ->whereDate('je.journal_date', '<=', $date);
            })
            ->groupBy('coa.id', 'coa.account_type', 'coa.account_code', 'coa.account_name')
            ->select(
                'coa.account_type',
                'coa.account_code',
                'coa.account_name',
                DB::raw('COALESCE(SUM(jl.debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(jl.credit), 0) as total_credit')
            )
            ->orderBy('coa.account_code')
            ->get()
            ->filter(fn($r) => $r->total_debit != 0 || $r->total_credit != 0);

        $grandTotalDebit = $rows->sum('total_debit');
        $grandTotalCredit = $rows->sum('total_credit');

        return view('reports.trial-balance', compact(
            'rows',
            'grandTotalDebit',
            'grandTotalCredit',
            'date'
        ));
    }

    public function cashFlow(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        // Simplified cash flow - seharusnya menggunakan metode direct/indirect
        // Ini contoh sederhana: ambil semua transaksi dari akun kas/bank
        
        $cashAccounts = DB::table('chart_of_accounts')
            ->where('account_code', 'LIKE', '1-1%') // Asumsi 1-1xxx adalah akun kas/bank
            ->pluck('id');

        $transactions = DB::table('journal_lines as jl')
            ->join('journal_entries as je', 'je.id', '=', 'jl.journal_entry_id')
            ->join('chart_of_accounts as coa', 'coa.id', '=', 'jl.coa_id')
            ->whereIn('jl.coa_id', $cashAccounts)
            ->whereDate('je.journal_date', '>=', $from)
            ->whereDate('je.journal_date', '<=', $to)
            ->select(
                'je.journal_date',
                'je.description',
                'coa.account_name',
                'jl.debit',
                'jl.credit'
            )
            ->orderBy('je.journal_date')
            ->orderBy('je.id')
            ->get();

        $beginningBalance = DB::table('journal_lines as jl')
            ->join('journal_entries as je', 'je.id', '=', 'jl.journal_entry_id')
            ->whereIn('jl.coa_id', $cashAccounts)
            ->whereDate('je.journal_date', '<', $from)
            ->select(DB::raw('SUM(jl.debit - jl.credit) as balance'))
            ->first()->balance ?? 0;

        $totalInflow = $transactions->sum('debit');
        $totalOutflow = $transactions->sum('credit');
        $netCashFlow = $totalInflow - $totalOutflow;
        $endingBalance = $beginningBalance + $netCashFlow;

        return view('reports.cash-flow', compact(
            'transactions',
            'beginningBalance',
            'totalInflow',
            'totalOutflow',
            'netCashFlow',
            'endingBalance',
            'from',
            'to'
        ));
    }
}