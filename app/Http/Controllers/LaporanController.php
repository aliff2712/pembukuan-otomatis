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
    // HELPER: Build Summary dari 3 sumber
    // =========================================================

    private function buildSummary($bulan = null, $tahun = null): array
    {
        // === TRANSAKSI (MEMBER) ===
        $qTransaksi = Transaksi::query();
        if ($bulan) $qTransaksi->whereMonth('tanggal', $bulan);
        if ($tahun) $qTransaksi->whereYear('tanggal', $tahun);

        $memberPaid   = (clone $qTransaksi)->where('status', 'paid')->sum('total');
        $memberUnpaid = (clone $qTransaksi)->where('status', 'unpaid')->sum('total');
        $memberPaidCount   = (clone $qTransaksi)->where('status', 'paid')->count();
        $memberUnpaidCount = (clone $qTransaksi)->where('status', 'unpaid')->count();

        // === VOUCHER ===
        $qVoucher = DailyVoucherSale::query();
        if ($bulan) $qVoucher->whereMonth('sale_date', $bulan);
        if ($tahun) $qVoucher->whereYear('sale_date', $tahun);

        $voucherTotal      = (clone $qVoucher)->sum('total_amount');
        $voucherTransaksi  = (clone $qVoucher)->sum('total_transactions');

        // === OTHER INCOME ===
        $qOther = OtherIncome::query();
        if ($bulan) $qOther->whereMonth('income_date', $bulan);
        if ($tahun) $qOther->whereYear('income_date', $tahun);

        $otherTotal = (clone $qOther)->sum('amount');
        $otherCount = (clone $qOther)->count();

        // === TOTAL PENDAPATAN (hanya paid + voucher + other) ===
        $totalPendapatan = $memberPaid + $voucherTotal + $otherTotal;

        return compact(
            'memberPaid', 'memberUnpaid',
            'memberPaidCount', 'memberUnpaidCount',
            'voucherTotal', 'voucherTransaksi',
            'otherTotal', 'otherCount',
            'totalPendapatan'
        );
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
    // =========================================================

    public function bulanan(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $summary = $this->buildSummary($bulan, $tahun);

        // Data tabel
        $transaksis = Transaksi::whereMonth('tanggal', $bulan)
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

        $label = Carbon::create($tahun, $bulan)->translatedFormat('F Y');

        return view('finance.laporan.bulanan', compact(
            'summary', 'transaksis', 'vouchers', 'otherIncomes',
            'bulan', 'tahun', 'label'
        ));
    }


    // =========================================================
    // LAPORAN TAHUNAN
    // =========================================================

    public function tahunan(Request $request)
    {
        $tahun = $request->tahun ?? now()->year;

        $summary = $this->buildSummary(null, $tahun);

        // Data per bulan
        $perBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $s = $this->buildSummary($i, $tahun);
            $perBulan[] = [
                'bulan'           => Carbon::create($tahun, $i)->translatedFormat('F'),
                'bulan_num'       => $i,
                'member_paid'     => $s['memberPaid'],
                'member_unpaid'   => $s['memberUnpaid'],
                'voucher'         => $s['voucherTotal'],
                'other'           => $s['otherTotal'],
                'total'           => $s['totalPendapatan'],
            ];
        }

        return view('finance.laporan.tahunan', compact(
            'summary', 'perBulan', 'tahun'
        ));
    }


    // =========================================================
    // EXPORT EXCEL
    // =========================================================

    public function exportExcelBulanan(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;
        $label = Carbon::create($tahun, $bulan)->translatedFormat('F_Y');

        return Excel::download(
            new LaporanExport('bulanan', $bulan, $tahun),
            "laporan_bulanan_{$label}.xlsx"
        );
    }

    public function exportExcelTahunan(Request $request)
    {
        $tahun = $request->tahun ?? now()->year;

        return Excel::download(
            new LaporanExport('tahunan', null, $tahun),
            "laporan_tahunan_{$tahun}.xlsx"
        );
    }


    // =========================================================
    // EXPORT PDF
    // =========================================================

    public function exportPdfBulanan(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $summary = $this->buildSummary($bulan, $tahun);

        $transaksis = Transaksi::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)->latest()->get();

        $vouchers = DailyVoucherSale::whereMonth('sale_date', $bulan)
            ->whereYear('sale_date', $tahun)->orderBy('sale_date', 'desc')->get();

        $otherIncomes = OtherIncome::whereMonth('income_date', $bulan)
            ->whereYear('income_date', $tahun)->orderBy('income_date', 'desc')->get();

        $label = Carbon::create($tahun, $bulan)->translatedFormat('F Y');

        $pdf = Pdf::loadView('finance.laporan.pdf.bulanan', compact(
            'summary', 'transaksis', 'vouchers', 'otherIncomes', 'label'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("laporan_bulanan_{$label}.pdf");
    }

    public function exportPdfTahunan(Request $request)
    {
        $tahun = $request->tahun ?? now()->year;

        $summary = $this->buildSummary(null, $tahun);

        $perBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $s = $this->buildSummary($i, $tahun);
            $perBulan[] = [
                'bulan'         => Carbon::create($tahun, $i)->translatedFormat('F'),
                'member_paid'   => $s['memberPaid'],
                'member_unpaid' => $s['memberUnpaid'],
                'voucher'       => $s['voucherTotal'],
                'other'         => $s['otherTotal'],
                'total'         => $s['totalPendapatan'],
            ];
        }

        $pdf = Pdf::loadView('finance.laporan.pdf.tahunan', compact(
            'summary', 'perBulan', 'tahun'
        ))->setPaper('a4', 'portrait');

        return $pdf->download("laporan_tahunan_{$tahun}.pdf");
    }
}