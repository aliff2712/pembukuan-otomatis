<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Journal;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;

class PaymentPostingService
{
    public function post(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {

            $invoice = $payment->invoice;

            if (! $invoice) {
                throw new \Exception('Invoice not found');
            }

            if ($invoice->status === 'paid') {
                throw new \Exception('Invoice already paid');
            }

            /**
             * 1. Create journal
             */
            $journal = Journal::create([
                'journal_date' => $payment->payment_date,
                'description'  => 'Payment invoice #' . $invoice->id,
                'source_type'  => Payment::class,
                'source_id'    => $payment->id,
            ]);

            /**
             * 2. Debit Cash / Bank
             */
            JournalEntry::create([
                'journal_id' => $journal->id,
                'account_code' => '101', // Cash
                'debit'  => $payment->amount,
                'credit' => 0,
            ]);

            /**
             * 3. Credit Accounts Receivable
             */
            JournalEntry::create([
                'journal_id' => $journal->id,
                'account_code' => '110', // AR
                'debit'  => 0,
                'credit' => $payment->amount,
            ]);

            /**
             * 4. Update invoice
             */
            $invoice->update([
                'status' => 'paid'
            ]);
        });
    }
}
