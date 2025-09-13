<?php
namespace App\Http\Controllers;

use App\DebtStatus;
use App\DebtType;
use App\TransactionType;
use App\Models\Debt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class DebtController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return Inertia::render('Debts/Index', [
            'debts' => $user->debts()->with('party')->latest()->get(),
            'parties' => $user->parties()->orderBy('name')->get(['id', 'name']),
            'accounts' => $user->accounts()->where('is_active', true)->get(['id', 'name', 'balance']),
        ]);
    }

    // Pembuatan Utang/Piutang Baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'party_id' => ['required', Rule::exists('parties', 'id')->where('user_id', Auth::id())],
            'account_id' => ['required', Rule::exists('accounts', 'id')->where('user_id', Auth::id())],
            'type' => ['required', Rule::enum(DebtType::class)],
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|gt:0',
            'due_date' => 'nullable|date',
        ]);

        $user = Auth::user();
        $account = $user->accounts()->findOrFail($validated['account_id']);
        $type = DebtType::from($validated['type']);

        // Logika Transaksi Awal
        $transactionType = ($type === DebtType::DEBT) ? TransactionType::INCOME : TransactionType::EXPENSE;
        $categoryName = ($type === DebtType::DEBT) ? 'Penerimaan Utang' : 'Pemberian Pinjaman';

        if ($transactionType === TransactionType::EXPENSE && $account->balance < $validated['amount']) {
            return redirect()->back()->with('error', 'Saldo tidak cukup untuk memberikan pinjaman.');
        }

        $category = $user->categories()->firstOrCreate(
            ['name' => $categoryName, 'type' => $transactionType],
            ['icon' => '🤝']
        );

        DB::transaction(function () use ($user, $validated, $account, $type, $transactionType, $category) {
            // 1. Buat catatan Utang/Piutang
            $debt = $user->debts()->create($validated);

            // 2. Update saldo akun
            if ($type === DebtType::DEBT) { // Saya berutang, uang masuk
                $account->balance += $validated['amount'];
            } else { // Saya meminjamkan, uang keluar
                $account->balance -= $validated['amount'];
            }
            $account->save();

            // 3. Buat transaksi awal
            $account->transactions()->create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'type' => $transactionType,
                'amount' => $validated['amount'],
                'description' => $validated['description'],
                'transaction_date' => now(),
            ]);
        });

        return redirect()->route('debts.index')->with('success', 'Catatan berhasil dibuat.');
    }

    // Pencatatan Pembayaran/Cicilan
    public function recordPayment(Request $request, Debt $debt)
    {
        $this->authorize('update', $debt);
        $validated = $request->validate([
            'account_id' => ['required', Rule::exists('accounts', 'id')->where('user_id', Auth::id())],
            'amount' => ['required', 'numeric', 'gt:0', 'lte:' . $debt->remaining_amount],
            'payment_date' => 'required|date',
        ]);

        $user = Auth::user();
        $account = $user->accounts()->findOrFail($validated['account_id']);
        $amount = $validated['amount'];

        // Logika Transaksi Pembayaran
        $transactionType = ($debt->type === DebtType::DEBT) ? TransactionType::EXPENSE : TransactionType::INCOME;
        $categoryName = ($debt->type === DebtType::DEBT) ? 'Pembayaran Utang' : 'Penerimaan Piutang';

        if ($transactionType === TransactionType::EXPENSE && $account->balance < $amount) {
            return redirect()->back()->with('error', 'Saldo tidak cukup untuk membayar utang.');
        }

        $category = $user->categories()->firstOrCreate(
            ['name' => $categoryName, 'type' => $transactionType],
            ['icon' => '💸']
        );

        DB::transaction(function () use ($debt, $account, $amount, $validated, $transactionType, $category, $user) {
            // 1. Buat transaksi pembayaran
            $transaction = $account->transactions()->create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'type' => $transactionType,
                'amount' => $amount,
                'description' => 'Pembayaran untuk: ' . $debt->description,
                'transaction_date' => $validated['payment_date'],
            ]);

            // 2. Buat catatan DebtPayment
            $debt->payments()->create([
                'account_id' => $account->id,
                'transaction_id' => $transaction->id,
                'amount' => $amount,
                'payment_date' => $validated['payment_date'],
            ]);

            // 3. Update saldo akun
            if ($transactionType === TransactionType::EXPENSE) {
                $account->balance -= $amount;
            } else {
                $account->balance += $amount;
            }
            $account->save();

            // 4. Update status utang
            $debt->paid_amount += $amount;
            if ($debt->paid_amount >= $debt->amount) {
                $debt->status = DebtStatus::PAID;
            } else {
                $debt->status = DebtStatus::PARTIALLY_PAID;
            }
            $debt->save();
        });

        return redirect()->route('debts.index')->with('success', 'Pembayaran berhasil dicatat.');
    }
}
