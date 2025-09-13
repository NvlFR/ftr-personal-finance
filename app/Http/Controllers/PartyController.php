<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PartyController extends Controller
{
   // Implementasikan metode index, store, update, destroy
    // Pastikan semua operasi divalidasi dan hanya untuk user yang sedang login.
    // Contoh store:
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('parties')->where('user_id', Auth::id())],
            'contact_info' => 'nullable|string|max:100',
        ]);
        $request->user()->parties()->create($validated);
        return redirect()->back()->with('success', 'Pihak baru berhasil ditambahkan.');
    }
}
