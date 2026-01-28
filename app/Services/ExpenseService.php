<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\JournalEntry;
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
             * 2. Journal header (journal_entries)
             */
            $journalEntry = JournalEntry::create([
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
                'journal_entry_id' => $journalEntry->id,
                'coa_id'           => $data['expense_coa_id'],
                'debit'            => $data['amount'],
                'credit'           => 0,
            ]);

            /**
             * 4. Kredit → Kas / Bank
             */
            JournalLine::create([
                'journal_entry_id' => $journalEntry->id,
                'coa_id'           => $data['cash_coa_id'],
                'debit'            => 0,
                'credit'           => $data['amount'],
            ]);

            /**
             * 5. Ledger posting
             * (boleh via observer / event)
             */
            // LedgerService::postFromJournal($journalEntry);

            return $expense;
        });
    }
}
