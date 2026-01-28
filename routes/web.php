<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ARAgingController;
use App\Http\Controllers\ExpenseController;

// HOME
Route::get('/', function () {
    return view('welcome');
});

// REPORTS
Route::get('/reports/ar-aging', [ARAgingController::class, 'index'])
    ->name('reports.ar-aging');

// EXPENSES
Route::get('/expenses/create', [ExpenseController::class, 'create'])
    ->name('expenses.create');
Route::post('/expenses', [ExpenseController::class, 'store'])
    ->name('expenses.store');
Route::get('/expenses/{expense}', [ExpenseController::class, 'show'])
    ->name('expenses.show');
