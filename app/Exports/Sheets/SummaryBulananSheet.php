<?php

namespace App\Exports\Sheets;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SummaryBulananSheet implements FromArray, WithTitle, WithStyles
{
    public function __construct(
        protected Collection $transaksis,
        protected Collection $vouchers,
        protected Collection $otherIncomes,
        protected Collection $expenses,
        protected int $bulan,
        protected int $tahun
    ) {}

    public function array(): array
    {
        $label = Carbon::create($this->tahun, $this->bulan)->translatedFormat('F Y');

        $memberPaid   = $this->transaksis->where('status', 'paid')->sum('total');
        $memberUnpaid = $this->transaksis->where('status', 'unpaid')->sum('total');
        $voucher      = $this->vouchers->sum('total_amount');
        $other        = $this->otherIncomes->sum('amount');
        $expense      = $this->expenses->sum('amount');

        $totalPendapatan = $memberPaid + $voucher + $other;
        $labaKotor       = $totalPendapatan - $expense;

        return [
            ['LAPORAN KEUANGAN DHS FINANCE'],
            ['Periode', $label],
            ['Dicetak', now()->format('d/m/Y H:i')],
            [''],

            ['RINGKASAN PENDAPATAN'],
            ['Keterangan', 'Nominal'],
            ['Member - Paid',   $memberPaid],
            ['Member - Unpaid', $memberUnpaid],
            ['Voucher',         $voucher],
            ['Other Income',    $other],
            ['TOTAL PENDAPATAN', $totalPendapatan],
            [''],

            ['RINGKASAN PENGELUARAN'],
            ['Keterangan', 'Nominal'],
            ['Total Expense', $expense],
            [''],

            ['LABA / RUGI', $labaKotor],
        ];
    }

    public function title(): string
    {
        return 'Summary';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1  => ['font' => ['bold' => true, 'size' => 14]],
            5  => ['font' => ['bold' => true]],
            6  => ['font' => ['bold' => true]],
            11 => ['font' => ['bold' => true]],
            13 => ['font' => ['bold' => true]],
            14 => ['font' => ['bold' => true]],
            17 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}