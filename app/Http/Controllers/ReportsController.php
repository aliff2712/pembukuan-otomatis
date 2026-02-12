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
        $from = $request->input('from');
        $to = $request->input('to');

        $q = DB::table('journal_lines as jl')
            ->join('journal_entries as je', 'je.id', '=', 'jl.journal_entry_id')
            ->join('chart_of_accounts as coa', 'coa.id', '=', 'jl.coa_id')
            ->whereIn('coa.account_type', ['revenue', 'expense']);

        if ($from) $q->whereDate('je.journal_date', '>=', $from);
        if ($to) $q->whereDate('je.journal_date', '<=', $to);

        $rows = $q->groupBy('coa.account_type', 'coa.account_code', 'coa.account_name')
            ->select(
                'coa.account_type',
                'coa.account_code',
                'coa.account_name',
                DB::raw('SUM(jl.debit) as debit'),
                DB::raw('SUM(jl.credit) as credit'),
                DB::raw('SUM(jl.debit - jl.credit) as balance')
            )
            ->orderBy('coa.account_code')
            ->get();

        $totals = ['revenue' => 0, 'expense' => 0];
        foreach ($rows as $r) {
            $totals[$r->account_type] += $r->balance;
        }

        $net = ($totals['revenue'] ?? 0) - ($totals['expense'] ?? 0);

        return view('reports.income-statement', compact('rows', 'totals', 'net', 'from', 'to'));
    }

    public function balanceSheet(Request $request)
    {
        $date = $request->input('date') ?: now()->toDateString();

        $q = DB::table('chart_of_accounts as coa')
            ->leftJoin('journal_lines as jl', 'jl.coa_id', '=', 'coa.id')
            ->leftJoin('journal_entries as je', 'je.id', '=', 'jl.journal_entry_id')
            ->whereIn('coa.account_type', ['asset', 'liability', 'equity'])
            ->whereDate('je.journal_date', '<=', $date)
            ->groupBy('coa.account_type', 'coa.account_code', 'coa.account_name')
            ->select(
                'coa.account_type',
                'coa.account_code',
                'coa.account_name',
                DB::raw('COALESCE(SUM(jl.debit),0) as debit'),
                DB::raw('COALESCE(SUM(jl.credit),0) as credit'),
                DB::raw('COALESCE(SUM(jl.debit - jl.credit),0) as balance')
            )
            ->orderBy('coa.account_code')
            ->get();

        $totals = ['asset' => 0, 'liability' => 0, 'equity' => 0];
        foreach ($q as $r) {
            $totals[$r->account_type] += $r->balance;
        }

        return view('reports.balance-sheet', ['rows' => $q, 'totals' => $totals, 'date' => $date]);
    }
}
