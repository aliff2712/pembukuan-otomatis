<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LedgerService;
use Carbon\Carbon;

class LedgerDaily extends Command
{
    protected $signature = 'ledger:daily {date}';
    protected $description = 'Show daily ledger';

    public function handle(LedgerService $ledger): int
    {
        $date = Carbon::parse($this->argument('date'));

        $rows = $ledger->daily($date);

        if ($rows->isEmpty()) {
            $this->warn('No ledger data');
            return Command::SUCCESS;
        }

        $this->table(
            ['Date', 'Account', 'Debit', 'Credit'],
            $rows->map(fn ($r) => [
                $r->date,
                "{$r->account_code} - {$r->account_name}",
                number_format($r->debit),
                number_format($r->credit),
            ])->toArray()
        );

        return Command::SUCCESS;
    }
}
