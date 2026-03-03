<?php
namespace App\Exports\Sheets;
use App\Models\DailyVoucherSale;
use App\Models\OtherIncome;
use App\Models\Transaksi;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanSummarySheet implements FromArray, WithTitle, WithStyles
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

        // Transaksi
        $qT = Transaksi::whereYear('tanggal', $this->tahun);
        if ($this->bulan) $qT->whereMonth('tanggal', $this->bulan);
        $paid   = (clone $qT)->where('status','paid')->sum('total');
        $unpaid = (clone $qT)->where('status','unpaid')->sum('total');

        // Voucher
        $qV = DailyVoucherSale::whereYear('sale_date', $this->tahun);
        if ($this->bulan) $qV->whereMonth('sale_date', $this->bulan);
        $voucher = (clone $qV)->sum('total_amount');

        // Other
        $qO = OtherIncome::whereYear('income_date', $this->tahun);
        if ($this->bulan) $qO->whereMonth('income_date', $this->bulan);
        $other = (clone $qO)->sum('amount');

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

    public function title(): string { return 'Summary'; }

    public function styles(Worksheet $sheet): array
    {
        return [
            1  => ['font' => ['bold' => true, 'size' => 14]],
            3  => ['font' => ['bold' => true]],
            4  => ['font' => ['bold' => true]],
            10 => ['font' => ['bold' => true]],
        ];
    }
}

