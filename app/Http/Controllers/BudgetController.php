<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BudgetController extends Controller
{
    /**
     * Menampilkan halaman budgeting.
     */
    public function index(Request $request)
    {
        // Tentukan bulan dan tahun yang akan ditampilkan
        $currentDate = Carbon::now();
        $month = $request->input('month', $currentDate->month);
        $year = $request->input('year', $currentDate->year);

        $user = Auth::user();

        // Ambil semua kategori PENGELUARAN milik user
        $categories = $user->categories()
            ->where('type', TransactionType::EXPENSE)
            ->orderBy('name')
            ->get();

        // Ambil data budget yang sudah ada untuk bulan & tahun terpilih
        $budgets = $user->budgets()
            ->where('month', $month)
            ->where('year', $year)
            ->pluck('amount', 'category_id'); // Format: [category_id => amount]

        // Hitung total pengeluaran aktual per kategori untuk bulan & tahun terpilih
        $actualSpending = $user->transactions()
            ->where('type', TransactionType::EXPENSE)
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->groupBy('category_id')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->pluck('total', 'category_id'); // Format: [category_id => total_spending]

        // Gabungkan semua data untuk dikirim ke Inertia
        $budgetData = $categories->map(function ($category) use ($budgets, $actualSpending) {
            $budgetAmount = $budgets->get($category->id, 0);
            $spendingAmount = $actualSpending->get($category->id, 0);
            return [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'category_icon' => $category->icon,
                'budgeted_amount' => $budgetAmount,
                'actual_spending' => $spendingAmount,
                'remaining' => $budgetAmount - $spendingAmount,
            ];
        });

        return Inertia::render('App/Budgets/Index', [
            'budgetData' => $budgetData,
            'filters' => [
                'month' => (int)$month,
                'year' => (int)$year,
            ],
        ]);
    }

    public function create()
{
    // Anda bisa menambahkan logic untuk mengambil data yang diperlukan form
    // Misal: daftar kategori
    $categories = Auth::user()->categories()
        ->where('type', \App\TransactionType::EXPENSE)
        ->orderBy('name')
        ->get();

    return Inertia::render('App/Budgets/Create', [
        'categories' => $categories,
    ]);
}


    /**
     * Menyimpan atau memperbarui data budget.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'budgets' => 'required|array',
            'budgets.*.category_id' => 'required|exists:categories,id',
            'budgets.*.amount' => 'required|numeric|min:0',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
        ]);

        $user = Auth::user();

        foreach ($validated['budgets'] as $budget) {
            $user->budgets()->updateOrCreate(
                [
                    // Kriteria untuk mencari (match by)
                    'category_id' => $budget['category_id'],
                    'month' => $validated['month'],
                    'year' => $validated['year'],
                ],
                [
                    // Data untuk di-update atau di-create
                    'amount' => $budget['amount']
                ]
            );
        }

        return redirect()->route('budgets.index')->with('success', 'Budget berhasil disimpan.');
    }
}
