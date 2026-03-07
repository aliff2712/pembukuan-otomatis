<?php

namespace App\Exports\Sheets;

use App\Models\DailyVoucherSale;
use App\Models\OtherIncome;
use App\Models\Transaksi;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LaporanSummarySheet implements FromArray, WithTitle, WithStyles, WithColumnFormatting, ShouldAutoSize
{
    public function __construct(
        protected string $type,
        protected ?int $bulan,
        protected int $tahun
    ) {}

    public function array(): array
    {
        $label = $this->type === 'bulanan'
            ? Carbon::create($this->tahun, $this->bulan)->translatedFormat('F Y')
            : (string) $this->tahun;

        // =========================
        // TRANSAKSI MEMBER
        // =========================
        $qT = Transaksi::whereYear('tanggal', $this->tahun);
        if ($this->bulan) $qT->whereMonth('tanggal', $this->bulan);

        $paid   = (clone $qT)->where('status','paid')->sum('total');
        $unpaid = (clone $qT)->where('status','unpaid')->sum('total');

        // =========================
        // VOUCHER
        // =========================
        $qV = DailyVoucherSale::whereYear('sale_date', $this->tahun);
        if ($this->bulan) $qV->whereMonth('sale_date', $this->bulan);

        $voucher = (clone $qV)->sum('total_amount');

        // =========================
        // OTHER INCOME
        // =========================
        $qO = OtherIncome::whereYear('income_date', $this->tahun);
        if ($this->bulan) $qO->whereMonth('income_date', $this->bulan);

        $other = (clone $qO)->sum('amount');

        // =========================
        // TOTAL
        // =========================
        $total = $paid + $voucher + $other;

        return [
            ['LAPORAN KEUANGAN DHS FINANCE - ' . strtoupper($label)],
            [''],
            ['RINGKASAN PENDAPATAN'],
            ['Sumber', 'Nominal'],

            ['Member - Paid',   $paid],
            ['Member - Unpaid', $unpaid],
            ['Voucher',         $voucher],
            ['Other Income',    $other],

            [''],
            ['TOTAL PENDAPATAN BERSIH', $total],
        ];
    }

    public function title(): string
    {
        return 'Summary';
    }

    // =========================
    // AUTO FORMAT CURRENCY
    // =========================
    public function columnFormats(): array
    {
        return [
            'B' => '"Rp"#,##0',
        ];
    }

    // =========================
    // STYLE EXCEL
    // =========================
    public function styles(Worksheet $sheet): array
    {
        return [

            // Judul
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 14
                ]
            ],

            // Header section
            3 => [
                'font' => [
                    'bold' => true
                ]
            ],

            // Header tabel
            4 => [
                'font' => [
                    'bold' => true
                ]
            ],

            // Total
            10 => [
                'font' => [
                    'bold' => true
                ]
            ],

        ];
    }
}