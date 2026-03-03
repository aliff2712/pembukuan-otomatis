<?php

namespace App\Exports;

use App\Models\DailyVoucherSale;
use App\Models\OtherIncome;
use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanExport implements WithMultipleSheets
{
    protected string $type;
    protected ?int   $bulan;
    protected int    $tahun;

    public function __construct(string $type, ?int $bulan, int $tahun)
    {
        $this->type  = $type;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function sheets(): array
    {
        if ($this->type === 'bulanan') {

            // 3 query sekali, dilempar ke semua sheet
            $transaksis   = Transaksi::whereMonth('tanggal', $this->bulan)
                ->whereYear('tanggal', $this->tahun)->latest()->get();

            $vouchers     = DailyVoucherSale::whereMonth('sale_date', $this->bulan)
                ->whereYear('sale_date', $this->tahun)->orderBy('sale_date', 'desc')->get();

            $otherIncomes = OtherIncome::whereMonth('income_date', $this->bulan)
                ->whereYear('income_date', $this->tahun)->orderBy('income_date', 'desc')->get();

            return [
                new Sheets\SummaryBulananSheet($transaksis, $vouchers, $otherIncomes, $this->bulan, $this->tahun),
                new Sheets\TransaksiSheet($transaksis),
                new Sheets\VoucherSheet($this->bulan, $this->tahun),
                new Sheets\OtherIncomeSheet( $this->bulan, $this->tahun)
            ];

        } else {

            // 3 query untuk seluruh tahun
            $allTransaksi = Transaksi::whereYear('tanggal', $this->tahun)->get();
            $allVoucher   = DailyVoucherSale::whereYear('sale_date', $this->tahun)->get();
            $allOther     = OtherIncome::whereYear('income_date', $this->tahun)->get();

            return [
                new Sheets\PerBulanSheet($allTransaksi, $allVoucher, $allOther, $this->tahun),
            ];
        }
    }
}