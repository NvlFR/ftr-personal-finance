<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar kategori milik user.
     */
    public function index()
    {
        return Inertia::render('App/Categories/Index', [
            'categories' => Auth::user()->categories()
                ->withCount('transactions') // Menghitung jumlah transaksi terkait
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Menampilkan form tambah kategori.
     */
    public function create()
    {
        return Inertia::render('App/Categories/Create');
    }


    /**
     * Menyimpan kategori baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                // Pastikan nama unik per user per tipe
                Rule::unique('categories')->where(function ($query) use ($request) {
                    return $query->where('user_id', Auth::id())
                        ->where('type', $request->type);
                }),
            ],
            'icon' => 'nullable|string|max:50',
            'type' => ['required', Rule::enum(TransactionType::class)],
        ]);

        $request->user()->categories()->create($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Memperbarui data kategori.
     */
    public function update(Request $request, Category $category)
    {
        // Otorisasi: Pastikan user hanya bisa mengedit kategorinya sendiri
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories')->where(function ($query) use ($request) {
                    return $query->where('user_id', Auth::id())
                        ->where('type', $request->type);
                })->ignore($category->id), // Abaikan ID kategori saat ini
            ],
            'icon' => 'nullable|string|max:50',
            'type' => ['required', Rule::enum(TransactionType::class)],
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Menghapus kategori.
     */
    public function destroy(Category $category)
    {
        // Otorisasi: Pastikan user hanya bisa menghapus kategorinya sendiri
        $this->authorize('delete', $category);

        // PENTING: Jangan izinkan hapus jika kategori masih punya transaksi.
        if ($category->transactions()->exists()) {
            return redirect()->back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki transaksi terkait.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
