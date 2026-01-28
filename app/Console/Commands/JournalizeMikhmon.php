<?php

namespace App\Console\Commands;

use App\Models\JournalEntry;
use Illuminate\Console\Command;
use App\Models\DailyVoucherSale;
use Illuminate\Support\Facades\DB;

class JournalizeMikhmon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'journal:mikhmon {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
       protected $description = 'Generate journal from daily voucher sales';

    /**
     * Execute the console command.
     */
      public function handle(): int
    {
        $date = $this->argument('date');

        $query = DailyVoucherSale::query();
        if ($date) {
            $query->where('sale_date', $date);
        }

        $sales = $query->get();

        if ($sales->isEmpty()) {
            $this->info('No daily voucher sales found.');
            return Command::SUCCESS;
        }

        DB::beginTransaction();

        try {
            foreach ($sales as $sale) {

                // === anti double journal ===
                $exists = JournalEntry::where('source_type', 'mikhmon')
                    ->where('source_id', $sale->id)
                    ->exists();

                if ($exists) {
                    $this->line("Skipped {$sale->sale_date} (already journaled)");
                    continue;
                }

                $entry = JournalEntry::create([
                    'journal_date'  => $sale->sale_date,
                    'description'   => 'Penjualan voucher harian',
                    'source_type'   => 'mikhmon',
                    'source_id'     => $sale->id,
                    'reference_no'  => null,
                    'total_debit'   => $sale->total_amount,
                    'total_credit' => $sale->total_amount,
                ]);

                $entry->lines()->createMany([
                    [
                        'account_code' => '1101',
                        'account_name' => 'Kas',
                        'debit'  => $sale->total_amount,
                        'credit' => 0,
                    ],
                    [
                        'account_code' => '4101',
                        'account_name' => 'Pendapatan Voucher',
                        'debit'  => 0,
                        'credit' => $sale->total_amount,
                    ],
                ]);

                $this->info("Journal created for {$sale->sale_date}");
            }

            DB::commit();
            return Command::SUCCESS;

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
