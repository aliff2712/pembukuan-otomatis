<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Journal;
use App\Models\JournalLine;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    public function record(array $data): Expense
    {
        return DB::transaction(function () use ($data) {

            /**
             * 1. Simpan transaksi bisnis (expense)
             */
            $expense = Expense::create([
                'expense_date'   => $data['expense_date'],
                'expense_coa_id' => $data['expense_coa_id'],
                'cash_coa_id'    => $data['cash_coa_id'],
                'amount'         => $data['amount'],
                'description'    => $data['description'] ?? null,
            ]);

            /**
             * 2. Buat journal header
             */
            $journal = Journal::create([
                'journal_date' => $data['expense_date'],
                'description'  => $data['description'] 
                                  ?? 'Pengeluaran operasional',
                'source_type'  => 'expense',
                'source_id'    => $expense->id,
            ]);

            /**
             * 3. Debit → Beban
             */
            JournalLine::create([
                'journal_id' => $journal->id,
                'coa_id'     => $data['expense_coa_id'],
                'debit'      => $data['amount'],
                'credit'     => 0,
            ]);

            /**
             * 4. Kredit → Kas
             */
            JournalLine::create([
                'journal_id' => $journal->id,
                'coa_id'     => $data['cash_coa_id'],
                'debit'      => 0,
                'credit'     => $data['amount'],
            ]);

            /**
             * 5. Post ke ledger
             * (kalau lu pakai event / observer, bagian ini bisa kosong)
             */
            // LedgerService::postFromJournal($journal);

            return $expense;
        });
    }
}
