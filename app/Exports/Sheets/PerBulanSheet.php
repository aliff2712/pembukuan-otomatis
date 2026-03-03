<?php
namespace App\Exports\Sheets;

use App\Models\DailyVoucherSale;
use App\Models\OtherIncome;
use App\Models\Transaksi;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PerBulanSheet implements FromArray, WithHeadings, WithTitle, WithStyles
{
    public function __construct(protected int $tahun) {}

    public function array(): array
    {
        $rows = [];
        for ($i = 1; $i <= 12; $i++) {
            $paid   = Transaksi::whereYear('tanggal', $this->tahun)->whereMonth('tanggal', $i)->where('status','paid')->sum('total');
            $unpaid = Transaksi::whereYear('tanggal', $this->tahun)->whereMonth('tanggal', $i)->where('status','unpaid')->sum('total');
            $voucher= DailyVoucherSale::whereYear('sale_date', $this->tahun)->whereMonth('sale_date', $i)->sum('total_amount');
            $other  = OtherIncome::whereYear('income_date', $this->tahun)->whereMonth('income_date', $i)->sum('amount');
            $total  = $paid + $voucher + $other;

            $rows[] = [
                Carbon::create($this->tahun, $i)->translatedFormat('F'),
                $paid, $unpaid, $voucher, $other, $total
            ];
        }
        return $rows;
    }

    public function headings(): array
    {
        return ['Bulan', 'Member Paid', 'Member Unpaid', 'Voucher', 'Other Income', 'Total Pendapatan'];
    }

    public function title(): string { return 'Per Bulan'; }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}

