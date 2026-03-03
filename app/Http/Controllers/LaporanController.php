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
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        // 3 query saja
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

        $summary = $this->summarize($transaksis, $vouchers, $otherIncomes);
        $label   = Carbon::create($tahun, $bulan)->translatedFormat('F Y');

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
            $tahun = (int) ($request->tahun ?? now()->year);

            /*
            |--------------------------------------------------------------------------
            | AGGREGATION DI DATABASE
            |--------------------------------------------------------------------------
            */

            // MEMBER PAID
            $memberPaid = Transaksi::selectRaw("
            MONTH(tanggal) as bulan,
            SUM(total) as total,
            COUNT(*) as jumlah
            ")
            ->whereYear('tanggal', $tahun)
            ->where('status', 'paid')
            ->groupByRaw("MONTH(tanggal)")
            ->get()
            ->keyBy('bulan');

            // MEMBER UNPAID
            $memberUnpaid = Transaksi::selectRaw("
            MONTH(tanggal) as bulan,
            SUM(total) as total,
            COUNT(*) as jumlah
            ")
            ->whereYear('tanggal', $tahun)
            ->where('status', 'unpaid')
            ->groupByRaw("MONTH(tanggal)")
            ->get()
            ->keyBy('bulan');

            // VOUCHER
            $voucher = DailyVoucherSale::selectRaw("
            MONTH(sale_date) as bulan,
            SUM(total_amount) as total,
            SUM(total_transactions) as transaksi
            ")
            ->whereYear('sale_date', $tahun)
            ->groupByRaw("MONTH(sale_date)")
            ->get()
            ->keyBy('bulan');

            // OTHER INCOME
            $other = OtherIncome::selectRaw("
            MONTH(income_date) as bulan,
            SUM(amount) as total,
            COUNT(*) as jumlah
            ")
            ->whereYear('income_date', $tahun)
            ->groupByRaw("MONTH(income_date)")
            ->get()
            ->keyBy('bulan');


            /*
            |--------------------------------------------------------------------------
            | BUILD RESULT PER BULAN (12 ROW )
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | SUMMARY TAHUNAN (TANPA LOAD DATA BESAR)
            |--------------------------------------------------------------------------
            */

            $summary = [
            'memberPaid'        => $memberPaid->sum('total'),
            'memberUnpaid'      => $memberUnpaid->sum('total'),
            'memberPaidCount'   => $memberPaid->sum('jumlah'),
            'memberUnpaidCount' => $memberUnpaid->sum('jumlah'),
            'voucherTotal'      => $voucher->sum('total'),
            'voucherTransaksi'  => $voucher->sum('transaksi'),
            'otherTotal'        => $other->sum('total'),
            'otherCount'        => $other->sum('jumlah'),
            'totalPendapatan'   =>
            $memberPaid->sum('total') +
            $voucher->sum('total') +
            $other->sum('total'),
            ];

            return view('finance.laporan.tahunan', compact(
            'summary',
            'perBulan',
            'tahun'
            ));
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
    // EXPORT PDF
    // =========================================================

    public function exportPdfBulanan(Request $request)
    {
        try {
            $bulan = (int) ($request->bulan ?? now()->month);
            $tahun = (int) ($request->tahun ?? now()->year);

            $transaksis = Transaksi::whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)->latest()->get();

            $vouchers = DailyVoucherSale::whereMonth('sale_date', $bulan)
                ->whereYear('sale_date', $tahun)->orderBy('sale_date', 'desc')->get();

            $otherIncomes = OtherIncome::whereMonth('income_date', $bulan)
                ->whereYear('income_date', $tahun)->orderBy('income_date', 'desc')->get();

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

    public function exportPdfTahunan(Request $request)
    {
        try {
            $tahun = (int) ($request->tahun ?? now()->year);

            $allTransaksi = Transaksi::whereYear('tanggal', $tahun)->get();
            $allVoucher   = DailyVoucherSale::whereYear('sale_date', $tahun)->get();
            $allOther     = OtherIncome::whereYear('income_date', $tahun)->get();

            $summary = $this->summarize($allTransaksi, $allVoucher, $allOther);

            $perBulan = [];
            for ($i = 1; $i <= 12; $i++) {
                $s = $this->summarize(
                    $allTransaksi->filter(fn($x) => $x->tanggal->month === $i),
                    $allVoucher->filter(fn($x) => $x->sale_date->month === $i),
                    $allOther->filter(fn($x) => $x->income_date->month === $i)
                );
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

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }
}