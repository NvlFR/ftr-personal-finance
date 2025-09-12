<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Enums\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    /**
     * Menampilkan daftar semua transaksi.
     */
    public function index(Request $request): Response
    {
        $transactions = Auth::user()->transactions()
            ->with(['category', 'account']) // Eager loading untuk performa
            ->latest('transaction_date')
            ->paginate(15);

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
        ]);
    }



    /**
     * Menampilkan form untuk membuat transaksi baru.
     */
    public function create(): Response
    {
        // Ambil data kategori dan akun milik user untuk dropdown di form
        $user = Auth::user();
        return Inertia::render('Transactions/Create', [
            'categories' => $user->categories()->orderBy('name')->get(),
            'accounts' => $user->accounts()->orderBy('name')->get(),
        ]);
    }

    /**
     * Menyimpan transaksi baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::enum(TransactionType::class)],
            'amount' => 'required|numeric|gt:0',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
            'category_id' => ['required', Rule::exists('categories', 'id')->where('user_id', Auth::id())],
            'account_id' => ['required', Rule::exists('accounts', 'id')->where('user_id', Auth::id())],
        ]);

        // Gunakan DB Transaction untuk memastikan integritas data
        // Jika salah satu gagal, semua akan di-rollback
        DB::transaction(function () use ($validated, $request) {
            // 1. Buat Transaksi
            $transaction = $request->user()->transactions()->create($validated);

            // 2. Update Saldo Akun/Dompet
            $account = $transaction->account;
            if ($transaction->type === TransactionType::INCOME) {
                $account->balance += $transaction->amount;
            } else {
                $account->balance -= $transaction->amount;
            }
            $account->save();
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    // Metode lain seperti show(), edit(), update(), destroy() akan mengikuti pola yang sama
    // dengan validasi, otorisasi, dan logika update saldo yang sesuai.
}
