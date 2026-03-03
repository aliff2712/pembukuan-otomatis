<?php
namespace App\Exports\Sheets;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransaksiSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function __construct(protected ?int $bulan, protected int $tahun) {}

    public function collection()
    {
        $q = Transaksi::whereYear('tanggal', $this->tahun);
        if ($this->bulan) $q->whereMonth('tanggal', $this->bulan);

        return $q->latest()->get()->map(fn($t, $i) => [
            'No'             => $i + 1,
            'Kode'           => $t->kode_transaksi,
            'Customer'       => $t->nama_customer,
            'Tanggal'        => $t->tanggal->format('d/m/Y'),
            'Jatuh Tempo'    => $t->jatuh_tempo?->format('d/m/Y') ?? '-',
            'Total'          => $t->total,
            'Status'         => strtoupper($t->status),
            'Dibayar Pada'   => $t->paid_at?->format('d/m/Y H:i') ?? '-',
        ]);
    }

    public function headings(): array
    {
        return ['No', 'Kode Transaksi', 'Customer', 'Tanggal', 'Jatuh Tempo', 'Total', 'Status', 'Dibayar Pada'];
    }

    public function title(): string { return 'Member'; }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}