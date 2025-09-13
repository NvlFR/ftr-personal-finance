<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\TransactionType; 
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
            ->latest('id') // Tambahkan urutan berdasarkan ID untuk konsistensi
            ->paginate(15);

        // Pastikan path Inertia sesuai dengan struktur folder Anda
        return Inertia::render('App/Transactions/Index', [
            'transactions' => $transactions,
        ]);
    }

    /**
     * Menampilkan form untuk membuat transaksi baru.
     */
    public function create(): Response
    {
        $user = Auth::user();
        return Inertia::render('App/Transactions/Create', [
            'categories' => $user->categories()->orderBy('name')->get(),
            'accounts' => $user->accounts()->where('is_active', true)->orderBy('name')->get(),
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

        DB::transaction(function () use ($validated, $request) {
            $transaction = $request->user()->transactions()->create($validated);
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

    /**
     * Menampilkan detail satu transaksi.
     */
    public function show(Transaction $transaction): Response
    {
        // Pastikan user hanya bisa melihat transaksinya sendiri
        $this->authorize('view', $transaction);

        return Inertia::render('App/Transactions/Show', [
            'transaction' => $transaction->load(['category', 'account']),
        ]);
    }

    /**
     * Menampilkan form untuk mengedit transaksi.
     */
    public function edit(Transaction $transaction): Response
    {
        // Pastikan user hanya bisa mengedit transaksinya sendiri
        $this->authorize('update', $transaction);

        $user = Auth::user();
        return Inertia::render('Transactions/Edit', [
            'transaction' => $transaction,
            'categories' => $user->categories()->orderBy('name')->get(),
            'accounts' => $user->accounts()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Memperbarui transaksi di database.
     */
    public function update(Request $request, Transaction $transaction)
    {
        // Pastikan user hanya bisa mengupdate transaksinya sendiri
        $this->authorize('update', $transaction);

        $validated = $request->validate([
            'type' => ['required', Rule::enum(TransactionType::class)],
            'amount' => 'required|numeric|gt:0',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
            'category_id' => ['required', Rule::exists('categories', 'id')->where('user_id', Auth::id())],
            'account_id' => ['required', Rule::exists('accounts', 'id')->where('user_id', Auth::id())],
        ]);

        DB::transaction(function () use ($validated, $transaction) {
            // --- Langkah 1: Simpan data lama & Batalkan efek transaksi lama ---
            $oldAccount = $transaction->account;
            if ($transaction->type === TransactionType::INCOME) {
                $oldAccount->balance -= $transaction->amount; // Kurangi pemasukan lama
            } else {
                $oldAccount->balance += $transaction->amount; // Tambahkan kembali pengeluaran lama
            }
            $oldAccount->save();

            // --- Langkah 2: Update data transaksi ---
            $transaction->update($validated);
            
            // --- Langkah 3: Terapkan efek transaksi baru ---
            // Kita perlu me-refresh relasi jika account_id berubah
            $newAccount = $transaction->fresh()->account; 
            if ($transaction->type === TransactionType::INCOME) {
                $newAccount->balance += $transaction->amount; // Tambah pemasukan baru
            } else {
                $newAccount->balance -= $transaction->amount; // Kurangi pengeluaran baru
            }
            $newAccount->save();
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Menghapus transaksi dari database.
     */
    public function destroy(Transaction $transaction)
    {
        // Pastikan user hanya bisa menghapus transaksinya sendiri
        $this->authorize('delete', $transaction);

        DB::transaction(function () use ($transaction) {
            // --- Langkah 1: Batalkan efek transaksi pada saldo akun ---
            $account = $transaction->account;
            if ($transaction->type === TransactionType::INCOME) {
                $account->balance -= $transaction->amount; // Kurangi pemasukan yang dihapus
            } else {
                $account->balance += $transaction->amount; // Kembalikan uang pengeluaran yang dihapus
            }
            $account->save();
            
            // --- Langkah 2: Hapus transaksi ---
            $transaction->delete();
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}