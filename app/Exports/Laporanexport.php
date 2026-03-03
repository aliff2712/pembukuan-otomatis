<?php

namespace App\Exports;

use App\Models\DailyVoucherSale;
use App\Models\OtherIncome;
use App\Models\Transaksi;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanExport implements WithMultipleSheets
{
    protected string $type;
    protected ?int $bulan;
    protected int $tahun;

    public function __construct(string $type, ?int $bulan, int $tahun)
    {
        $this->type  = $type;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function sheets(): array
    {
        $sheets = [];

        if ($this->type === 'bulanan') {
            $sheets[] = new Sheets\LaporanSummarySheet($this->type, $this->bulan, $this->tahun);
            $sheets[] = new Sheets\TransaksiSheet($this->bulan, $this->tahun);
            $sheets[] = new Sheets\VoucherSheet($this->bulan, $this->tahun);
            $sheets[] = new Sheets\OtherIncomeSheet($this->bulan, $this->tahun);
        } else {
            $sheets[] = new Sheets\LaporanSummarySheet($this->type, null, $this->tahun);
            $sheets[] = new Sheets\PerBulanSheet($this->tahun);
        }

        return $sheets;
    }
}