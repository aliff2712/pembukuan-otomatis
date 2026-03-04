<?php

namespace App\Exports;

use App\Exports\Sheets\LaporanSummarySheet;
use App\Exports\Sheets\TransaksiSheet;
use App\Exports\Sheets\PerBulanSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanExport implements WithMultipleSheets
{
    public function __construct(
        protected string $type,
        protected ?int $bulan,
        protected int $tahun
    ) {}

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new LaporanSummarySheet(
            $this->type,
            $this->bulan,
            $this->tahun
        );

        if ($this->type === 'tahunan') {
            $sheets[] = new PerBulanSheet($this->tahun);
        }

        // ✅ Sheet Detail Transaksi
        $sheets[] = new TransaksiSheet(
            $this->bulan,
            $this->tahun
        );

        return $sheets;
    }
}