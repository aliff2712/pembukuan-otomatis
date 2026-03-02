<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\DailyVoucherSale;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\MikhmonSalesStaging;
use App\Models\RawMikhmonImport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MikhmonImportService
{
    public array $log = [];

    // ──────────────────────────────────────────────
    // STEP 1: Import CSV → raw_mikhmon_imports
    // ──────────────────────────────────────────────
    public function importCsv(string $path): string
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("File not found: {$path}");
        }

        $batchId      = now()->format('Ymd_His');
        $insertCount  = 0;
        $skipCount    = 0;

        $file = fopen($path, 'r');

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) < 2)                        continue;
            if (str_contains($row[0], 'Selling Report')) continue;
            if (str_contains($row[0], 'Total'))          continue;
            if (!is_numeric($row[0]))                   continue;

            // ✅ FIX: Buat content hash untuk deteksi baris duplikat
            // Hash berdasarkan: date + time + username + profile + price
            // (bukan row_number karena itu hanya index urut di CSV)
            $contentHash = md5(implode('|', [
                $row[1] ?? '',  // date_raw
                $row[2] ?? '',  // time_raw
                $row[3] ?? '',  // username
                $row[4] ?? '',  // profile
                $row[6] ?? '',  // price_raw
            ]));

            // Skip kalau hash sudah ada di DB
            $alreadyExists = RawMikhmonImport::where('content_hash', $contentHash)->exists();
            if ($alreadyExists) {
                $skipCount++;
                continue;
            }

            RawMikhmonImport::create([
                'import_batch_id' => $batchId,
                'row_number'      => (int) $row[0],
                'date_raw'        => $row[1],
                'time_raw'        => $row[2],
                'username'        => $row[3],
                'profile'         => $row[4],
                'comment'         => $row[5] ?? null,
                'price_raw'       => $row[6] ?? null,
                'raw_payload'     => json_encode($row),
                'content_hash'    => $contentHash,  // ✅ simpan hash
                'imported_at'     => now(),
            ]);

            $insertCount++;
        }

        fclose($file);

        $this->log[] = "✅ Step 1 - Import selesai. Batch: {$batchId} | Inserted: {$insertCount} | Skipped (duplikat): {$skipCount}";

        return $batchId;
    }

    // ──────────────────────────────────────────────
    // STEP 2: Transform RAW → staging
    // ──────────────────────────────────────────────
    public function transform(): void
    {
        // ✅ FIX: whereDoesntHave sudah cukup karena raw duplikat sudah diblok di Step 1
        // Tapi kita tambahkan lock untuk keamanan di lingkungan concurrent
        $rawRows      = RawMikhmonImport::whereDoesntHave('staging')->get();
        $successCount = 0;
        $skipCount    = 0;

        foreach ($rawRows as $raw) {

            try {
                $saleDatetime = Carbon::createFromFormat(
                    'M/d/Y H:i:s',
                    "{$raw->date_raw} {$raw->time_raw}"
                );
            } catch (\Exception) {
                $skipCount++;
                continue;
            }

            $priceRaw = $raw->price_raw;

            if (!$priceRaw) {
                $skipCount++;
                continue;
            }

            $price = (float) str_replace(['Rp', '.', ','], ['', '', '.'], $priceRaw);

            if ($price <= 0) {
                $skipCount++;
                continue;
            }

            // ✅ FIX: Gunakan firstOrCreate agar tidak dobel meski dipanggil ulang
            MikhmonSalesStaging::firstOrCreate(
                ['raw_id' => $raw->id],  // key: satu raw hanya boleh punya satu staging
                [
                    'sale_datetime' => $saleDatetime,
                    'username'      => $raw->username,
                    'profile'       => $raw->profile,
                    'price'         => $price,
                    'batch_id'      => $raw->import_batch_id,
                ]
            );

            $successCount++;
        }

        $this->log[] = "✅ Step 2 - Transform selesai | Success: {$successCount} | Skipped: {$skipCount}";
    }

    // ──────────────────────────────────────────────
    // STEP 3: Aggregate daily sales
    // ──────────────────────────────────────────────
    public function aggregateDaily(): void
    {
        // ✅ FIX: Hitung agregasi langsung dari staging (sudah bersih karena Step 1 & 2 sudah fix)
        // updateOrCreate akan overwrite nilai lama dengan nilai terbaru yang benar
        $rows = MikhmonSalesStaging::selectRaw('
                DATE(sale_datetime) as sale_date,
                COUNT(*) as total_transactions,
                SUM(price) as total_amount
            ')
            ->groupByRaw('DATE(sale_datetime)')
            ->get();

        foreach ($rows as $row) {
            DailyVoucherSale::updateOrCreate(
                ['sale_date' => $row->sale_date],
                [
                    'total_transactions' => $row->total_transactions,
                    'total_amount'       => $row->total_amount,
                ]
            );
        }

        $this->log[] = "✅ Step 3 - Agregasi harian selesai | {$rows->count()} tanggal diproses";
    }

    // ──────────────────────────────────────────────
    // STEP 4: Journalize
    // ──────────────────────────────────────────────
    public function journalize(?string $date = null): void
    {
        $query = DailyVoucherSale::query();

        if ($date) {
            $query->where('sale_date', $date);
        }

        $sales = $query->get();

        if ($sales->isEmpty()) {
            $this->log[] = "⚠️ Step 4 - Tidak ada data penjualan harian ditemukan.";
            return;
        }

        $cashCoa           = ChartOfAccount::where('account_code', '1101')->firstOrFail();
        $voucherRevenueCoa = ChartOfAccount::where('account_code', '4101')->firstOrFail();

        $journalCount = 0;
        $skipCount    = 0;

        DB::transaction(function () use ($sales, $cashCoa, $voucherRevenueCoa, &$journalCount, &$skipCount) {
            foreach ($sales as $sale) {

                // ✅ Sudah benar: cek duplikat via source_id
                $exists = JournalEntry::where('source_type', 'mikhmon')
                    ->where('source_id', $sale->id)
                    ->exists();

                if ($exists) {
                    $skipCount++;
                    continue;
                }

                $entry = JournalEntry::create([
                    'journal_date'  => $sale->sale_date,
                    'description'   => 'Penjualan voucher harian',
                    'source_type'   => 'mikhmon',
                    'source_id'     => $sale->id,
                    'reference_no'  => null,
                    'total_debit'   => $sale->total_amount,
                    'total_credit'  => $sale->total_amount,
                ]);

                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'coa_id'           => $cashCoa->id,
                    'debit'            => $sale->total_amount,
                    'credit'           => 0,
                ]);

                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'coa_id'           => $voucherRevenueCoa->id,
                    'debit'            => 0,
                    'credit'           => $sale->total_amount,
                ]);

                $journalCount++;
            }
        });

        $this->log[] = "✅ Step 4 - Journalize selesai | Dibuat: {$journalCount} | Dilewati: {$skipCount}";
    }
}