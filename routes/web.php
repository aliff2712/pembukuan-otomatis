<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BeatInvoiceController;
use App\Http\Controllers\VoucherSaleController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\ChartOfAccountController;

/*
|--------------------------------------------------------------------------
| Web Routes - DHS FINANCE
|--------------------------------------------------------------------------
|
| Route untuk aplikasi pembukuan ISP
|
*/

// =====================================================================
// PUBLIC ROUTES (Guest only - redirect jika sudah login)
// =====================================================================
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
});

// =====================================================================
// AUTH ROUTES (dari Breeze - sudah include di auth.php)
// =====================================================================
require __DIR__.'/auth.php';

// =====================================================================
// PROTECTED ROUTES (Harus login)
// =====================================================================
Route::middleware(['auth', 'verified'])->group(function () {
    
    // =====================================================================
    // DASHBOARD
    // =====================================================================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // API endpoint untuk data dashboard (AJAX refresh)
    Route::get('/api/dashboard/data', [DashboardController::class, 'apiData'])->name('dashboard.api');

    // =====================================================================
    // CHART OF ACCOUNTS
    // =====================================================================
    Route::resource('chart-of-accounts', ChartOfAccountController::class);

    // API endpoints untuk dropdown
    Route::get('/api/chart-of-accounts/by-type', [ChartOfAccountController::class, 'getByType'])
        ->name('chart-of-accounts.by-type');
    Route::get('/api/chart-of-accounts/cash', [ChartOfAccountController::class, 'getCashAccounts'])
        ->name('chart-of-accounts.cash');

    // =====================================================================
    // JOURNAL ENTRIES
    // =====================================================================
    Route::prefix('journal-entries')->name('journal-entries.')->group(function () {
        Route::get('/', [JournalEntryController::class, 'index'])->name('index');
        Route::get('/daily', [JournalEntryController::class, 'daily'])->name('daily');
        Route::get('/summary', [JournalEntryController::class, 'summaryByAccount'])->name('summary');
        Route::get('/export', [JournalEntryController::class, 'export'])->name('export');
        Route::get('/{id}', [JournalEntryController::class, 'show'])->name('show');
    });

    // API endpoint
    Route::get('/api/journal-entries', [JournalEntryController::class, 'api'])
        ->name('journal-entries.api');

    // =====================================================================
    // VOUCHER SALES (Mikhmon)
    // =====================================================================
    Route::prefix('voucher-sales')->name('voucher-sales.')->group(function () {
        Route::get('/', [VoucherSaleController::class, 'index'])->name('index');
        Route::get('/reimport/form', [VoucherSaleController::class, 'reimportForm'])->name('reimport-form');
        Route::post('/reimport', [VoucherSaleController::class, 'reimport'])->name('reimport');
        Route::get('/export', [VoucherSaleController::class, 'export'])->name('export');
        Route::get('/{id}', [VoucherSaleController::class, 'show'])->name('show');
        Route::delete('/{id}', [VoucherSaleController::class, 'void'])->name('void');
    });

    // API endpoint untuk chart
    Route::get('/api/voucher-sales/chart', [VoucherSaleController::class, 'chartData'])
        ->name('voucher-sales.chart');

    // =====================================================================
    // BEAT INVOICES
    // =====================================================================
    Route::prefix('beat-invoices')->name('beat-invoices.')->group(function () {
        Route::get('/', [BeatInvoiceController::class, 'index'])->name('index');
        Route::get('/export', [BeatInvoiceController::class, 'export'])->name('export');
        Route::get('/{id}', [BeatInvoiceController::class, 'show'])->name('show');
        Route::get('/{id}/pdf', [BeatInvoiceController::class, 'exportPDF'])->name('pdf');
        Route::get('/{id}/preview', [BeatInvoiceController::class, 'previewPDF'])->name('preview');
    });

    // API endpoint untuk payment form
    Route::get('/api/invoices/unpaid', [BeatInvoiceController::class, 'getUnpaid'])
        ->name('beat-invoices.unpaid');

    // =====================================================================
    // PAYMENTS
    // =====================================================================
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/receipt/{id}', [PaymentController::class, 'receipt'])->name('receipt');
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/create', [PaymentController::class, 'create'])->name('create');
        Route::post('/', [PaymentController::class, 'store'])->name('store');
        Route::get('/export', [PaymentController::class, 'export'])->name('export');
        Route::get('/{id}', [PaymentController::class, 'show'])->name('show');
        Route::delete('/{id}', [PaymentController::class, 'destroy'])->name('destroy');
    });

    // =====================================================================
    // EXPENSES
    // =====================================================================
    Route::resource('expenses', ExpenseController::class);

    // Export expenses
    Route::get('/expenses-export', [ExpenseController::class, 'export'])->name('expenses.export');

    // Summary by account
    Route::get('/expenses-summary', [ExpenseController::class, 'summaryByAccount'])->name('expenses.summary');

    // =====================================================================
    // REPORTS
    // =====================================================================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/ledger', function() { return 'Ledger Report - Coming Soon'; })->name('ledger');
        Route::get('/ar-aging', function() { return 'AR Aging Report - Coming Soon'; })->name('ar-aging');
        Route::get('/income-statement', function() { return 'Income Statement - Coming Soon'; })->name('income-statement');
        Route::get('/balance-sheet', function() { return 'Balance Sheet - Coming Soon'; })->name('balance-sheet');
    });

    // =====================================================================
    // PROFILE
    // =====================================================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});