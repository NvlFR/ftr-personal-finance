<?php

namespace App\Http\Controllers;

use App\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportController extends Controller
{
    /**
     * Menampilkan halaman utama laporan keuangan.
     */
    public function index(Request $request)
    {
        // --- 1. Filter Tanggal ---
        // Validasi input filter, jika tidak ada, gunakan bulan ini sebagai default.
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))
            : now()->startOfMonth();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : now()->endOfMonth();

        $user = Auth::user();

        // --- 2. Laporan Arus Kas (Cash Flow) ---
        $baseQuery = $user->transactions()->whereBetween('transaction_date', [$startDate, $endDate]);

        $totalIncome = (clone $baseQuery)->where('type', TransactionType::INCOME)->sum('amount');
        $totalExpense = (clone $baseQuery)->where('type', TransactionType::EXPENSE)->sum('amount');
        $netFlow = $totalIncome - $totalExpense;

        // --- 3. Rincian Pengeluaran per Kategori ---
        $expenseByCategory = (clone $baseQuery)
            ->where('type', TransactionType::EXPENSE)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select('categories.name as category_name', 'categories.icon', DB::raw('SUM(transactions.amount) as total'))
            ->groupBy('transactions.category_id', 'categories.name', 'categories.icon')
            ->orderBy('total', 'desc')
            ->get();

        // --- 4. Rincian Pemasukan per Kategori ---
        $incomeByCategory = (clone $baseQuery)
            ->where('type', TransactionType::INCOME)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select('categories.name as category_name', 'categories.icon', DB::raw('SUM(transactions.amount) as total'))
            ->groupBy('transactions.category_id', 'categories.name', 'categories.icon')
            ->orderBy('total', 'desc')
            ->get();

        // --- 5. Mengirim Semua Data ke Inertia ---
        return Inertia::render('App/Reports/Index', [
            'reports' => [
                'cashFlow' => [
                    'income' => $totalIncome,
                    'expense' => $totalExpense,
                    'net' => $netFlow,
                ],
                'expenseByCategory' => $expenseByCategory,
                'incomeByCategory' => $incomeByCategory,
            ],
            'filters' => [
                'startDate' => $startDate->toDateString(),
                'endDate' => $endDate->toDateString(),
            ]
        ]);
    }
}
