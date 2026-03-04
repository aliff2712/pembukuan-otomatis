<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\DailyVoucherSale;
use App\Models\OtherIncome;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    // =========================================================
    // HELPER: Hitung summary dari collection (0 query tambahan)
    // =========================================================

    private function summarize($transaksis, $vouchers, $others): array
    {
        $memberPaid   = $transaksis->where('status', 'paid')->sum('total');
        $memberUnpaid = $transaksis->where('status', 'unpaid')->sum('total');
        $voucherTotal = $vouchers->sum('total_amount');
        $otherTotal   = $others->sum('amount');

        return [
            'memberPaid'        => $memberPaid,
            'memberUnpaid'      => $memberUnpaid,
            'memberPaidCount'   => $transaksis->where('status', 'paid')->count(),
            'memberUnpaidCount' => $transaksis->where('status', 'unpaid')->count(),
            'voucherTotal'      => $voucherTotal,
            'voucherTransaksi'  => $vouchers->sum('total_transactions'),
            'otherTotal'        => $otherTotal,
            'otherCount'        => $others->count(),
            'totalPendapatan'   => $memberPaid + $voucherTotal + $otherTotal,
        ];
    }

    // =========================================================
    // HELPER: Load data bulanan (shared antara view & export PDF)
    // FIX: Tambah with() untuk eager load relasi agar tidak N+1
    //      Ganti $relations sesuai relasi yang diakses di blade
    // =========================================================

    private function getBulananData(int $bulan, int $tahun): array
    {
        
        $transaksis = Transaksi::with(['member'])
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->latest()
            ->get();

        $vouchers = DailyVoucherSale::whereMonth('sale_date', $bulan)
            ->whereYear('sale_date', $tahun)
            ->orderBy('sale_date', 'desc')
            ->get();

        $otherIncomes = OtherIncome::whereMonth('income_date', $bulan)
            ->whereYear('income_date', $tahun)
            ->orderBy('income_date', 'desc')
            ->get();

        return [$transaksis, $vouchers, $otherIncomes];
    }

    // =========================================================
    // HELPER: Aggregasi tahunan per bulan via DB (shared antara
    //         tahunan() dan exportPdfTahunan())

    private function getTahunanAggregates(int $tahun): array
    {
        $memberPaid = Transaksi::selectRaw("
                MONTH(tanggal) as bulan,
                SUM(total)     as total,
                COUNT(*)       as jumlah
            ")
            ->whereYear('tanggal', $tahun)
            ->where('status', 'paid')
            ->groupByRaw('MONTH(tanggal)')
            ->get()
            ->keyBy('bulan');

        $memberUnpaid = Transaksi::selectRaw("
                MONTH(tanggal) as bulan,
                SUM(total)     as total,
                COUNT(*)       as jumlah
            ")
            ->whereYear('tanggal', $tahun)
            ->where('status', 'unpaid')
            ->groupByRaw('MONTH(tanggal)')
            ->get()
            ->keyBy('bulan');

        $voucher = DailyVoucherSale::selectRaw("
                MONTH(sale_date)        as bulan,
                SUM(total_amount)       as total,
                SUM(total_transactions) as transaksi
            ")
            ->whereYear('sale_date', $tahun)
            ->groupByRaw('MONTH(sale_date)')
            ->get()
            ->keyBy('bulan');

        $other = OtherIncome::selectRaw("
                MONTH(income_date) as bulan,
                SUM(amount)        as total,
                COUNT(*)           as jumlah
            ")
            ->whereYear('income_date', $tahun)
            ->groupByRaw('MONTH(income_date)')
            ->get()
            ->keyBy('bulan');

        return [$memberPaid, $memberUnpaid, $voucher, $other];
    }

    // =========================================================
    // HELPER: Build array perBulan dari hasil aggregat
    // =========================================================

    private function buildPerBulan(int $tahun, $memberPaid, $memberUnpaid, $voucher, $other): array
    {
        $perBulan = [];

        for ($i = 1; $i <= 12; $i++) {
            $paid   = $memberPaid[$i]->total   ?? 0;
            $unpaid = $memberUnpaid[$i]->total ?? 0;
            $v      = $voucher[$i]->total      ?? 0;
            $o      = $other[$i]->total        ?? 0;

            $perBulan[] = [
                'bulan'         => Carbon::create($tahun, $i)->translatedFormat('F'),
                'bulan_num'     => $i,
                'member_paid'   => $paid,
                'member_unpaid' => $unpaid,
                'voucher'       => $v,
                'other'         => $o,
                'total'         => $paid + $v + $o,
            ];
        }

        return $perBulan;
    }

    // =========================================================
    // HELPER: Build summary tahunan dari hasil aggregat
    // =========================================================

    private function buildSummaryTahunan($memberPaid, $memberUnpaid, $voucher, $other): array
    {
        return [
            'memberPaid'        => $memberPaid->sum('total'),
            'memberUnpaid'      => $memberUnpaid->sum('total'),
            'memberPaidCount'   => $memberPaid->sum('jumlah'),
            'memberUnpaidCount' => $memberUnpaid->sum('jumlah'),
            'voucherTotal'      => $voucher->sum('total'),
            'voucherTransaksi'  => $voucher->sum('transaksi'),
            'otherTotal'        => $other->sum('total'),
            'otherCount'        => $other->sum('jumlah'),
            'totalPendapatan'   => $memberPaid->sum('total')
                                 + $voucher->sum('total')
                                 + $other->sum('total'),
        ];
    }


    // =========================================================
    // INDEX
    // =========================================================

    public function index()
    {
        return view('finance.laporan.index');
    }


    // =========================================================
    // LAPORAN BULANAN
    // FIX: Pakai getBulananData() — tidak duplikasi dengan exportPdfBulanan
    // =========================================================

    public function bulanan(Request $request)
    {
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        [$transaksis, $vouchers, $otherIncomes] = $this->getBulananData($bulan, $tahun);

        $summary = $this->summarize($transaksis, $vouchers, $otherIncomes);
        $label   = Carbon::create($tahun, $bulan)->translatedFormat('F Y');

        return view('finance.laporan.bulanan', compact(
            'summary', 'transaksis', 'vouchers', 'otherIncomes',
            'bulan', 'tahun', 'label'
        ));
    }


    // =========================================================
    // LAPORAN TAHUNAN
    // FIX: Pakai shared helpers (getTahunanAggregates, buildPerBulan, buildSummaryTahunan)
    // =========================================================

    public function tahunan(Request $request)
    {
        $tahun = (int) ($request->tahun ?? now()->year);

        [$memberPaid, $memberUnpaid, $voucher, $other] = $this->getTahunanAggregates($tahun);

        $perBulan = $this->buildPerBulan($tahun, $memberPaid, $memberUnpaid, $voucher, $other);
        $summary  = $this->buildSummaryTahunan($memberPaid, $memberUnpaid, $voucher, $other);

        return view('finance.laporan.tahunan', compact('summary', 'perBulan', 'tahun'));
    }


    // =========================================================
    // EXPORT EXCEL
    // =========================================================

    public function exportExcelBulanan(Request $request)
    {
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);
        $label = Carbon::create($tahun, $bulan)->translatedFormat('F_Y');

        return Excel::download(
            new LaporanExport('bulanan', $bulan, $tahun),
            "laporan_bulanan_{$label}.xlsx"
        );
    }

    public function exportExcelTahunan(Request $request)
    {
        $tahun = (int) ($request->tahun ?? now()->year);

        return Excel::download(
            new LaporanExport('tahunan', null, $tahun),
            "laporan_tahunan_{$tahun}.xlsx"
        );
    }


    // =========================================================
    // EXPORT PDF BULANAN
    // FIX: Sebelumnya duplikasi 3 query dari bulanan()
    //      Sekarang pakai getBulananData() yang sama
    // =========================================================

    public function exportPdfBulanan(Request $request)
    {
        try {
            $bulan = (int) ($request->bulan ?? now()->month);
            $tahun = (int) ($request->tahun ?? now()->year);

            [$transaksis, $vouchers, $otherIncomes] = $this->getBulananData($bulan, $tahun);

            $summary = $this->summarize($transaksis, $vouchers, $otherIncomes);
            $label   = Carbon::create($tahun, $bulan)->translatedFormat('F Y');

            $pdf = Pdf::loadView('finance.laporan.pdf.bulanan', compact(
                'summary', 'transaksis', 'vouchers', 'otherIncomes', 'label'
            ))->setPaper('a4', 'landscape');

            return $pdf->download("laporan_bulanan_{$label}.pdf");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }


    // =========================================================
    // EXPORT PDF TAHUNAN
    // FIX UTAMA: Sebelumnya load semua raw row setahun ke PHP
    //   $allTransaksi = Transaksi::whereYear(...)->get() ← bisa ribuan row
    //   lalu filter() 12x di PHP ← boros memory & CPU
    // Sekarang: pakai DB aggregation (getTahunanAggregates)
    //   = 4 query ringan, sama seperti tahunan()
    // =========================================================

    public function exportPdfTahunan(Request $request)
    {
        try {
            $tahun = (int) ($request->tahun ?? now()->year);

            [$memberPaid, $memberUnpaid, $voucher, $other] = $this->getTahunanAggregates($tahun);

            $perBulan = $this->buildPerBulan($tahun, $memberPaid, $memberUnpaid, $voucher, $other);
            $summary  = $this->buildSummaryTahunan($memberPaid, $memberUnpaid, $voucher, $other);

            $pdf = Pdf::loadView('finance.laporan.pdf.tahunan', compact(
                'summary', 'perBulan', 'tahun'
            ))->setPaper('a4', 'portrait');

            return $pdf->download("laporan_tahunan_{$tahun}.pdf");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }
}