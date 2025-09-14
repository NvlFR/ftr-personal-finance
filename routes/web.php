<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// --- Import Semua Controller ---
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FinancialGoalController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\RecurringTransactionController;

/*
|--------------------------------------------------------------------------
| Rute Publik (Bisa diakses tanpa login)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Rute yang Membutuhkan Otentikasi
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // --- Dashboard ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Transaksi & Kategori ---
    // Route::resource('transactions', TransactionController::class);
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');


    Route::resource('categories', CategoryController::class);

    // --- Manajemen Dompet/Akun ---
    Route::resource('accounts', AccountController::class)->except(['show']);

    // --- Budgeting & Laporan ---
    Route::resource('budgets', BudgetController::class);

    // --- Financial Goals ---
    // Route::post('/financial-goals/{financialGoal}/add-savings', [FinancialGoalController::class, 'addSavings'])->name('financial-goals.add-savings');
    Route::resource('financial-goals', FinancialGoalController::class);

    // --- Utang & Piutang ---
    Route::post('/debts/{debt}/payments', [DebtController::class, 'recordPayment'])->name('debts.record-payment');
    Route::resource('debts', DebtController::class);
    Route::resource('parties', PartyController::class);

    // --- Transaksi Berulang ---
    Route::resource('recurring-transactions', RecurringTransactionController::class);

});

/*
|--------------------------------------------------------------------------
| Memuat File Rute Tambahan
|--------------------------------------------------------------------------
*/
// File ini mungkin berisi rute untuk pengaturan profil, dll.
require __DIR__ . '/settings.php';

// File ini berisi rute untuk login, register, logout, dll.
require __DIR__ . '/auth.php';
