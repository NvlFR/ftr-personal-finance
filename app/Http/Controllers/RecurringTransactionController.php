<?php

namespace App\Http\Controllers;

use App\Models\RecurringTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class RecurringTransactionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return Inertia::render('App/RecurringTransactions/Index', [
            'recurringTransactions' => $user->recurringTransactions()->with(['account', 'category'])->latest()->get(),
            'accounts' => $user->accounts()->where('is_active', true)->get(['id', 'name']),
            'categories' => $user->categories()->orderBy('name')->get(['id', 'name', 'type']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => ['required', Rule::exists('accounts', 'id')->where('user_id', Auth::id())],
            'category_id' => ['required', Rule::exists('categories', 'id')->where('user_id', Auth::id())],
            'type' => ['required', Rule::enum(\App\TransactionType::class)],
            'amount' => 'required|numeric|gt:0',
            'description' => 'nullable|string|max:255',
            'frequency' => ['required', Rule::enum(\App\RecurrenceFrequency::class)],
            'interval' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Tanggal jatuh tempo pertama adalah tanggal mulai
        $validated['next_due_date'] = $validated['start_date'];

        $request->user()->recurringTransactions()->create($validated);

        return redirect()->route('recurring-transactions.index')->with('success', 'Transaksi berulang berhasil dibuat.');
    }

    // Metode update dan destroy bisa dibuat dengan pola yang sama,
    // misalnya untuk mengubah status `is_active` (pause/resume).
}
