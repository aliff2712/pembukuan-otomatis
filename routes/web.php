<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Dashboard
|--------------------------------------------------------------------------
|
| Route untuk halaman dashboard aplikasi pembukuan ISP
|
*/

// Dashboard Route
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

// API endpoint untuk mendapatkan data dashboard (opsional, untuk AJAX refresh)
Route::get('/api/dashboard/data', [DashboardController::class, 'apiData'])->name('dashboard.api');

/*
|--------------------------------------------------------------------------
| Tambahkan route lainnya di bawah ini
|--------------------------------------------------------------------------
*/

// Beat Invoices
Route::prefix('beat-invoices')->name('beat-invoices.')->group(function () {
    Route::get('/', function() { return 'List Beat Invoices'; })->name('index');
    Route::get('/create', function() { return 'Create Invoice'; })->name('create');
    Route::get('/{id}', function($id) { return 'Show Invoice ' . $id; })->name('show');
    Route::get('/{id}/edit', function($id) { return 'Edit Invoice ' . $id; })->name('edit');
});

// Payments
Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/', function() { return 'List Payments'; })->name('index');
    Route::get('/create', function() { return 'Create Payment'; })->name('create');
    Route::get('/{id}', function($id) { return 'Show Payment ' . $id; })->name('show');
});

// Expenses
Route::prefix('expenses')->name('expenses.')->group(function () {
    Route::get('/', function() { return 'List Expenses'; })->name('index');
    Route::get('/create', function() { return 'Create Expense'; })->name('create');
    Route::get('/{id}', function($id) { return 'Show Expense ' . $id; })->name('show');
    Route::get('/{id}/edit', function($id) { return 'Edit Expense ' . $id; })->name('edit');
});

// Voucher Sales
Route::prefix('voucher-sales')->name('voucher-sales.')->group(function () {
    Route::get('/', function() { return 'List Voucher Sales'; })->name('index');
    Route::get('/create', function() { return 'Create Voucher Sale'; })->name('create');
    Route::get('/{id}', function($id) { return 'Show Voucher Sale ' . $id; })->name('show');
});

// Journal Entries
Route::prefix('journal-entries')->name('journal-entries.')->group(function () {
    Route::get('/', function() { return 'List Journal Entries'; })->name('index');
    Route::get('/create', function() { return 'Create Journal Entry'; })->name('create');
    Route::get('/{id}', function($id) { return 'Show Journal Entry ' . $id; })->name('show');
});

// Chart of Accounts
Route::prefix('chart-of-accounts')->name('chart-of-accounts.')->group(function () {
    Route::get('/', function() { return 'List Chart of Accounts'; })->name('index');
    Route::get('/create', function() { return 'Create Account'; })->name('create');
    Route::get('/{id}/edit', function($id) { return 'Edit Account ' . $id; })->name('edit');
});

// Reports
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/ledger', function() { return 'Ledger Report'; })->name('ledger');
    Route::get('/ar-aging', function() { return 'AR Aging Report'; })->name('ar-aging');
    Route::get('/income-statement', function() { return 'Income Statement'; })->name('income-statement');
    Route::get('/balance-sheet', function() { return 'Balance Sheet'; })->name('balance-sheet');
});