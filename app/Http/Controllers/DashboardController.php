<?php

namespace App\Http\Controllers;

use App\Models\BeatInvoice;
use App\Models\DailyVoucherSale;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\JournalLine;
use App\Models\OtherIncome;
use Carbon\Carbon;
use App\Models\Transaksi;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. SALDO KAS (Account Code: 1101)
        $cashBalance = $this->calculateBalance('1101');

        // 2. SALDO BANK (Account Code: 1102)
        $bankBalance = $this->calculateBalance('1102');

        // 3. SALDO VOUCHER HARIAN (bulan ini)
        $voucherBalance = $this->voucherBalance();

        // 4. PIUTANG USAHA
        $arBalance = Transaksi::where('status', 'unpaid')->sum('total');

        $paid = Transaksi::where('status', 'paid')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('total');

        // 5. PENDAPATAN BULAN INI
        $revenueThisMonth = $this->getRevenueThisMonth();

        // 6. OTHER INCOME BULAN INI
        $otherIncomeThisMonth = OtherIncome::whereMonth('income_date', now()->month)
            ->whereYear('income_date', now()->year)
            ->sum('amount');

        // 7. BEBAN BULAN INI
        $expenseThisMonth = $this->getExpenseThisMonth();

        // 8. DATA GRAFIK 6 BULAN TERAKHIR
        $monthlyStats = $this->getMonthlyStats();

        return view('dashboard', compact(
            'cashBalance',
            'bankBalance',
            'voucherBalance',
            'arBalance',
            'revenueThisMonth',
            'otherIncomeThisMonth',
            'expenseThisMonth',
            'monthlyStats',
            'paid'
        ));
    }

    /**
     * Menghitung saldo penjualan voucher harian
     * Mengembalikan data: total bulan ini, hari ini, rata-rata/hari, dan trend
     */
    private function voucherBalance(): array
    {
        $currentMonth = now()->month;
        $currentYear  = now()->year;
        $today        = now()->toDateString();

        // Total penjualan voucher bulan ini
        $thisMonthTotal = DailyVoucherSale::whereMonth('sale_date', $currentMonth)
            ->whereYear('sale_date', $currentYear)
            ->sum('total_amount');

        // Total transaksi voucher bulan ini
        $thisMonthTransactions = DailyVoucherSale::whereMonth('sale_date', $currentMonth)
            ->whereYear('sale_date', $currentYear)
            ->sum('total_transactions');

        // Penjualan hari ini
        $todayTotal = DailyVoucherSale::where('sale_date', $today)
            ->sum('total_amount');

        $todayTransactions = DailyVoucherSale::where('sale_date', $today)
            ->sum('total_transactions');

        // Rata-rata penjualan per hari bulan ini
        $daysWithData = DailyVoucherSale::whereMonth('sale_date', $currentMonth)
            ->whereYear('sale_date', $currentYear)
            ->count();

        $averagePerDay = $daysWithData > 0
            ? round($thisMonthTotal / $daysWithData, 0)
            : 0;

        // Total bulan lalu (untuk perbandingan trend)
        $lastMonth      = now()->subMonth();
        $lastMonthTotal = DailyVoucherSale::whereMonth('sale_date', $lastMonth->month)
            ->whereYear('sale_date', $lastMonth->year)
            ->sum('total_amount');

        // Persentase perubahan dari bulan lalu
        $growthPercent = 0;
        if ($lastMonthTotal > 0) {
            $growthPercent = round((($thisMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100, 1);
        }

        // Data 7 hari terakhir (untuk mini chart)
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date       = now()->subDays($i)->toDateString();
            $dayData    = DailyVoucherSale::where('sale_date', $date)->first();
            $last7Days[] = [
                'date'         => $date,
                'label'        => now()->subDays($i)->format('d M'),
                'total_amount' => $dayData->total_amount ?? 0,
                'transactions' => $dayData->total_transactions ?? 0,
            ];
        }

        return [
            'this_month_total'        => $thisMonthTotal,
            'this_month_transactions' => $thisMonthTransactions,
            'today_total'             => $todayTotal,
            'today_transactions'      => $todayTransactions,
            'average_per_day'         => $averagePerDay,
            'last_month_total'        => $lastMonthTotal,
            'growth_percent'          => $growthPercent,
            'last_7_days'             => $last7Days,
            'days_with_data'          => $daysWithData,
        ];
    }

    /**
     * Menghitung saldo akun berdasarkan account_code
     * Saldo = Total Debit - Total Credit
     */
    private function calculateBalance(string $accountCode): float
    {
        $result = JournalLine::join(
                'chart_of_accounts as coa',
                'coa.id',
                '=',
                'journal_lines.coa_id'
            )
            ->where('coa.account_code', $accountCode)
            ->selectRaw('
                COALESCE(SUM(journal_lines.debit), 0) -
                COALESCE(SUM(journal_lines.credit), 0) as saldo
            ')
            ->value('saldo');

        return (float) $result;
    }

    /**
     * Menghitung pendapatan bulan ini
     * Sumber: Transaksi (paid) + DailyVoucherSale + OtherIncome
     */
    private function getRevenueThisMonth(): float
    {
        $currentMonth = now()->month;
        $currentYear  = now()->year;

        $paymentRevenue = Transaksi::whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->where('status', 'paid')
            ->sum('total');

        $voucherRevenue = DailyVoucherSale::whereMonth('sale_date', $currentMonth)
            ->whereYear('sale_date', $currentYear)
            ->sum('total_amount');

        $otherIncomeRevenue = OtherIncome::whereMonth('income_date', $currentMonth)
            ->whereYear('income_date', $currentYear)
            ->sum('amount');

        return $paymentRevenue + $voucherRevenue + $otherIncomeRevenue;
    }

    /**
     * Menghitung beban bulan ini
     */
    private function getExpenseThisMonth(): float
    {
        return Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');
    }

    /**
     * Mendapatkan data statistik bulanan untuk 6 bulan terakhir
     */
    private function getMonthlyStats(): array
    {
        $months      = [];
        $revenueData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $month = $date->month;
            $year  = $date->year;

            $months[] = $date->isoFormat('MMM YYYY');

            $paymentRevenue = Payment::whereMonth('payment_date', $month)
                ->whereYear('payment_date', $year)
                ->sum('amount');

            $voucherRevenue = DailyVoucherSale::whereMonth('sale_date', $month)
                ->whereYear('sale_date', $year)
                ->sum('total_amount');

            $otherIncome = OtherIncome::whereMonth('income_date', $month)
                ->whereYear('income_date', $year)
                ->sum('amount');

            $revenueData[] = $paymentRevenue + $voucherRevenue + $otherIncome;

            $expenseData[] = Expense::whereMonth('expense_date', $month)
                ->whereYear('expense_date', $year)
                ->sum('amount');
        }

        return [
            'labels'  => $months,
            'revenue' => $revenueData,
            'expense' => $expenseData,
        ];
    }

    /**
     * API endpoint untuk mendapatkan data dashboard
     */
    public function apiData()
    {
        return response()->json([
            'cashBalance'          => $this->calculateBalance('1101'),
            'bankBalance'          => $this->calculateBalance('1102'),
            'voucherBalance'       => $this->voucherBalance(),
            'arBalance'            => Transaksi::where('status', 'unpaid')->sum('total'),
            'revenueThisMonth'     => $this->getRevenueThisMonth(),
            'otherIncomeThisMonth' => OtherIncome::whereMonth('income_date', now()->month)
                ->whereYear('income_date', now()->year)
                ->sum('amount'),
            'expenseThisMonth'     => $this->getExpenseThisMonth(),
            'monthlyStats'         => $this->getMonthlyStats(),
        ]);
    }
}