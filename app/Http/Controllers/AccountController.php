<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AccountController extends Controller
{
    /**
     * Menampilkan daftar akun milik user.
     */
    public function index()
    {
        return Inertia::render('Accounts/Index', [
            'accounts' => Auth::user()->accounts()->orderBy('name')->get(),
        ]);
    }

    /**
     * Menyimpan akun baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('accounts')->where('user_id', Auth::id())
            ],
            'balance' => 'required|numeric', // Ini adalah saldo awal
            'icon' => 'nullable|string|max:50',
            'color' => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
        ]);

        $request->user()->accounts()->create($validated);

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    /**
     * Memperbarui data akun.
     */
    public function update(Request $request, Account $account)
    {
        // Otorisasi: Pastikan user hanya bisa mengedit akunnya sendiri
        $this->authorize('update', $account);

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('accounts')->where('user_id', Auth::id())->ignore($account->id)
            ],
            'icon' => 'nullable|string|max:50',
            'color' => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
            'is_active' => 'required|boolean',
        ]);

        // PENTING: Kita tidak mengizinkan update 'balance' secara langsung dari form ini.
        // Saldo hanya boleh berubah melalui pencatatan transaksi untuk menjaga integritas data.
        $account->update($validated);

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Menghapus sebuah akun.
     */
    public function destroy(Account $account)
    {
        // Otorisasi: Pastikan user hanya bisa menghapus akunnya sendiri
        $this->authorize('delete', $account);

        // PENTING: Jangan izinkan hapus jika akun masih punya riwayat transaksi.
        // Sarankan untuk menonaktifkan akun saja.
        if ($account->transactions()->exists()) {
            return redirect()->back()->with('error', 'Akun tidak dapat dihapus karena memiliki riwayat transaksi. Anda bisa menonaktifkannya.');
        }

        $account->delete();

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil dihapus.');
    }
}
