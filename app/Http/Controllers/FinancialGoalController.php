<?php

namespace App\Http\Controllers;

use App\GoalStatus;
use App\TransactionType;
use App\Models\FinancialGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class FinancialGoalController extends Controller
{
    /**
     * Menampilkan daftar semua financial goals.
     */
    public function index()
    {
        $user = Auth::user();
        return Inertia::render('App/FinancialGoals/Index', [
            'goals' => $user->financialGoals()->orderBy('target_date')->get(),
            // Kirim juga daftar akun untuk form "Tambah Tabungan"
            'accounts' => $user->accounts()->where('is_active', true)->get(['id', 'name', 'balance']),
        ]);
    }

    /**
     * Menyimpan goal baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|gt:0',
            'target_date' => 'nullable|date|after_or_equal:today',
        ]);

        $request->user()->financialGoals()->create($validated);

        return redirect()->route('financial-goals.index')->with('success', 'Goal berhasil dibuat.');
    }

        /**
     * Menampilkan form untuk membuat goal baru.
     */
    public function create()
    {
        $user = Auth::user();
        return Inertia::render('App/FinancialGoals/Create', [
            // Tambahkan baris ini untuk mengirim data akun
            'accounts' => $user->accounts()->where('is_active', true)->get(['id', 'name']),
        ]);
    }

    /**
     * Menambahkan tabungan ke sebuah goal.
     */
    public function addSavings(Request $request, FinancialGoal $financialGoal)
    {
        // Otorisasi: Pastikan goal ini milik user yang sedang login
        $this->authorize('update', $financialGoal);

        $validated = $request->validate([
            'amount' => 'required|numeric|gt:0',
            'account_id' => ['required', Rule::exists('accounts', 'id')->where('user_id', Auth::id())],
        ]);

        $user = Auth::user();
        $account = $user->accounts()->findOrFail($validated['account_id']);
        $amount = $validated['amount'];

        if ($account->balance < $amount) {
            return redirect()->back()->with('error', 'Saldo di akun tidak mencukupi.');
        }

        // Cari atau buat kategori "Tabungan"
        $savingCategory = $user->categories()->firstOrCreate(
            ['name' => 'Tabungan Tujuan', 'type' => TransactionType::EXPENSE],
            ['icon' => '🎯']
        );

        DB::transaction(function () use ($financialGoal, $account, $amount, $savingCategory, $user) {
            // 1. Tambah jumlah tabungan di goal
            $financialGoal->current_amount += $amount;

            // Cek apakah goal sudah tercapai
            if ($financialGoal->current_amount >= $financialGoal->target_amount) {
                $financialGoal->status = GoalStatus::COMPLETED;
            }
            $financialGoal->save();

            // 2. Kurangi saldo di akun/dompet
            $account->balance -= $amount;
            $account->save();

            // 3. Catat sebagai transaksi PENGELUARAN
            $account->transactions()->create([
                'user_id' => $user->id,
                'category_id' => $savingCategory->id,
                'type' => TransactionType::EXPENSE,
                'amount' => $amount,
                'description' => 'Tabungan untuk: ' . $financialGoal->name,
                'transaction_date' => now(),
            ]);
        });

        return redirect()->route('financial-goals.index')->with('success', 'Tabungan berhasil ditambahkan.');
    }
}
