<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\RawBeatImport;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BeatImportRaw extends Command
{
    protected $signature = 'beat:import-raw {file}';
    protected $description = 'Import RAW Beat CSV/XLSX';

  public function handle(): int
{
    $file = $this->argument('file');

    if (! file_exists($file)) {
        $this->error("File not found: {$file}");
        return Command::FAILURE;
    }

    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $batchId = now()->format('Ymd_His');

    if ($extension === 'csv') {
        $rows = array_map('str_getcsv', file($file));
    } elseif (in_array($extension, ['xlsx', 'xls'])) {
        $spreadsheet = IOFactory::load($file);
        $rows = $spreadsheet->getActiveSheet()->toArray();
    } else {
        $this->error('Unsupported file format');
        return Command::FAILURE;
    }

    foreach ($rows as $index => $row) {
        // skip header
        if ($index === 0) {
            continue;
        }

        RawBeatImport::create([
            'import_batch_id' => $batchId,
            'row_number'      => $index + 1,
            'raw_payload'     => $row,
            'imported_at'     => now(),
        ]);
    }

    $this->info('RAW Beat import completed');
    return Command::SUCCESS;
}

}
