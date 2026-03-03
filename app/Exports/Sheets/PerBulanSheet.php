<?php

namespace App\Exports\Sheets;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PerBulanSheet implements FromArray, WithHeadings, WithTitle, WithStyles
{
    public function __construct(
        protected Collection $allTransaksi,
        protected Collection $allVoucher,
        protected Collection $allOther,
        protected int        $tahun
    ) {}

    public function array(): array
    {
        $rows = [];

        for ($i = 1; $i <= 12; $i++) {
            // Filter dari collection — no query
            $tBulan = $this->allTransaksi->filter(fn($t) => $t->tanggal->month === $i);
            $vBulan = $this->allVoucher->filter(fn($v) => $v->sale_date->month === $i);
            $oBulan = $this->allOther->filter(fn($o) => $o->income_date->month === $i);

            $paid   = $tBulan->where('status', 'paid')->sum('total');
            $unpaid = $tBulan->where('status', 'unpaid')->sum('total');
            $voucher= $vBulan->sum('total_amount');
            $other  = $oBulan->sum('amount');
            $total  = $paid + $voucher + $other;

            $rows[] = [
                Carbon::create($this->tahun, $i)->translatedFormat('F'),
                $paid,
                $unpaid,
                $voucher,
                $other,
                $total,
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['Bulan', 'Member Paid', 'Member Unpaid', 'Voucher', 'Other Income', 'Total Pendapatan'];
    }

    public function title(): string { return 'Per Bulan - ' . $this->tahun; }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}