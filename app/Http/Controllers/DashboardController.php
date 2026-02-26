<?php

namespace App\Http\Controllers;

use App\Models\BeatInvoice;
use App\Models\DailyVoucherSale;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\JournalLine;
use App\Models\OtherIncome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // 3. PIUTANG USAHA (Accounts Receivable)
        $arBalance = BeatInvoice::whereNotIn('status', ['paid', 'void'])
             ->sum('total_amount') - 
             Payment::whereHas('invoice', function($q) {
                 $q->whereNotIn('status', ['paid', 'void']);
             })->sum('amount');
        //statistik Transaksi
        $arBalance = Transaksi::where('status', 'unpaid')->sum('total');


        // 4. PENDAPATAN BULAN INI
        $revenueThisMonth = $this->getRevenueThisMonth();

        // 4b. OTHER INCOME BULAN INI
        $otherIncomeThisMonth = OtherIncome::whereMonth('income_date', now()->month)
            ->whereYear('income_date', now()->year)
            ->sum('amount');

        // 5. BEBAN BULAN INI
        $expenseThisMonth = $this->getExpenseThisMonth();

        // 6. DATA GRAFIK 6 BULAN TERAKHIR
        $monthlyStats = $this->getMonthlyStats();

        return view('dashboard', compact(
            'cashBalance',
            'bankBalance',
            'arBalance',
            'revenueThisMonth',
            'otherIncomeThisMonth',
            'expenseThisMonth',
            'monthlyStats'
        ));
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

        return $result;
    }


    /**
     * Menghitung pendapatan bulan ini
     * Sumber: Payment (invoice beat) + DailyVoucherSale (voucher)
     */
    private function getRevenueThisMonth(): float
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Pendapatan dari pembayaran invoice
        $paymentRevenue = Payment::whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->sum('amount');

        // Pendapatan dari penjualan voucher
        $voucherRevenue = DailyVoucherSale::whereMonth('sale_date', $currentMonth)
            ->whereYear('sale_date', $currentYear)
            ->sum('total_amount');

        // Pendapatan dari sumber lain (OtherIncome)
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
        $months = [];
        $revenueData = [];
        $expenseData = [];

        // Loop 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;

            // Label bulan (format: Jan 2025)
            $months[] = $date->isoFormat('MMM YYYY');

            // Pendapatan bulan tersebut
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

            // Beban bulan tersebut
            $expenseData[] = Expense::whereMonth('expense_date', $month)
                ->whereYear('expense_date', $year)
                ->sum('amount');
        }

        return [
            'labels' => $months,
            'revenue' => $revenueData,
            'expense' => $expenseData,
        ];
    }

    /**
     * API endpoint untuk mendapatkan data dashboard (opsional, jika pakai AJAX)
     */
    public function apiData()
    {
        return response()->json([
            'cashBalance' => $this->calculateBalance('1101'),
            'bankBalance' => $this->calculateBalance('1102'),
            'arBalance' => BeatInvoice::whereNotIn('status', ['paid', 'void'])
                ->sum('total_amount') - 
                Payment::whereHas('invoice', function($q) {
                    $q->whereNotIn('status', ['paid', 'void']);
                })->sum('amount'),
            'revenueThisMonth' => $this->getRevenueThisMonth(),
            'otherIncomeThisMonth' => OtherIncome::whereMonth('income_date', now()->month)
                ->whereYear('income_date', now()->year)
                ->sum('amount'),
            'expenseThisMonth' => $this->getExpenseThisMonth(),
            'monthlyStats' => $this->getMonthlyStats(),
        ]);
    }
}