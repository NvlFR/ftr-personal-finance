<?php

namespace App\Http\Controllers;

use App\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Menampilkan data utama untuk halaman dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // --- 1. Ringkasan Keuangan Bulan Ini ---
        $monthlySummaryQuery = $user->transactions()
            ->whereYear('transaction_date', $today->year)
            ->whereMonth('transaction_date', $today->month);

        $totalIncomeThisMonth = (clone $monthlySummaryQuery)->where('type', TransactionType::INCOME)->sum('amount');
        $totalExpenseThisMonth = (clone $monthlySummaryQuery)->where('type', TransactionType::EXPENSE)->sum('amount');

        // --- 2. Saldo Total di Semua Akun Aktif ---
        $totalBalance = $user->accounts()->where('is_active', true)->sum('balance');
        $accounts = $user->accounts()->where('is_active', true)->get(['name', 'balance', 'icon', 'color']);

        // --- 3. Transaksi Terbaru ---
        $latestTransactions = $user->transactions()
            ->with(['category', 'account'])
            ->latest('transaction_date')
            ->limit(5)
            ->get();

        // --- 4. Progres Financial Goals yang Sedang Berjalan ---
        $activeGoals = $user->financialGoals()
            ->where('status', \App\GoalStatus::IN_PROGRESS)
            ->orderBy('target_date')
            ->get();

        // --- 5. Data untuk Grafik Pengeluaran (Contoh: 7 hari terakhir) ---
        $expenseChartData = $user->transactions()
            ->where('type', TransactionType::EXPENSE)
            ->whereBetween('transaction_date', [now()->subDays(6), now()])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get([
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(amount) as total')
            ])
            ->pluck('total', 'date');

        // Isi tanggal yang kosong dengan 0 agar grafik tidak putus
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[$date] = $expenseChartData->get($date, 0);
        }

        // --- Mengirim Semua Data ke Inertia ---
        return Inertia::render('Dashboard', [
            'summary' => [
                'totalBalance' => $totalBalance,
                'incomeThisMonth' => $totalIncomeThisMonth,
                'expenseThisMonth' => $totalExpenseThisMonth,
            ],
            'accounts' => $accounts,
            'latestTransactions' => $latestTransactions,
            'activeGoals' => $activeGoals,
            'expenseChartData' => [
                'labels' => $dates->keys(),
                'data' => $dates->values(),
            ],
        ]);
    }
}
