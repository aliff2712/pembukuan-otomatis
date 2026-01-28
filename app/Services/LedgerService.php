<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LedgerService
{
    /**
     * Ledger harian
     */
    public function daily(Carbon $date)
    {
        return DB::table('journal_entries')
            ->join('journals', 'journals.id', '=', 'journal_entries.journal_id')
            ->selectRaw('
                journals.journal_date as date,
                journal_entries.account_code,
                journal_entries.account_name,
                SUM(CASE WHEN journal_entries.type = "debit" THEN journal_entries.amount ELSE 0 END) as debit,
                SUM(CASE WHEN journal_entries.type = "credit" THEN journal_entries.amount ELSE 0 END) as credit
            ')
            ->whereDate('journals.journal_date', $date->toDateString())
            ->groupBy(
                'journals.journal_date',
                'journal_entries.account_code',
                'journal_entries.account_name'
            )
            ->orderBy('journal_entries.account_code')
            ->get();
    }

    /**
     * Ledger bulanan
     */
    public function monthly(int $year, int $month)
    {
        return DB::table('journal_entries')
            ->join('journals', 'journals.id', '=', 'journal_entries.journal_id')
            ->selectRaw('
                journal_entries.account_code,
                journal_entries.account_name,
                SUM(CASE WHEN journal_entries.type = "debit" THEN journal_entries.amount ELSE 0 END) as debit,
                SUM(CASE WHEN journal_entries.type = "credit" THEN journal_entries.amount ELSE 0 END) as credit
            ')
            ->whereYear('journals.journal_date', $year)
            ->whereMonth('journals.journal_date', $month)
            ->groupBy(
                'journal_entries.account_code',
                'journal_entries.account_name'
            )
            ->orderBy('journal_entries.account_code')
            ->get();
    }
   
    public function kasLedger()
    {
        return DB::table('journal_lines as jl')
            ->join('journal_entries as je', 'je.id', '=', 'jl.journal_entry_id')
            ->where('jl.account_code', '1101')
            ->orderBy('je.journal_date')
            ->select(
                'je.journal_date',
                'je.description',
                'jl.debit',
                'jl.credit',
                DB::raw('SUM(jl.debit - jl.credit) OVER (ORDER BY je.journal_date, jl.id) as balance')
            )
            ->get();
    }
}


