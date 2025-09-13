<?php

namespace Database\Seeders;

use App\DebtType;
use App\TransactionType;
use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Debt;
use App\Models\FinancialGoal;
use App\Models\Party;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Buat satu user utama untuk development
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // 1. Buat Akun/Dompet
        $accounts = Account::factory(3)->create(['user_id' => $user->id]);

        // 2. Buat Kategori
        $expenseCategories = Category::factory(8)->create(['user_id' => $user->id, 'type' => TransactionType::EXPENSE]);
        $incomeCategories = Category::factory(3)->create(['user_id' => $user->id, 'type' => TransactionType::INCOME]);

        // 3. Buat Transaksi
        // Kita buat 100 transaksi random dalam 3 bulan terakhir
        for ($i = 0; $i < 100; $i++) {
            $account = $accounts->random();
            $type = (rand(1, 10) > 8) ? TransactionType::INCOME : TransactionType::EXPENSE;
            $category = ($type === TransactionType::INCOME) ? $incomeCategories->random() : $expenseCategories->random();
            
            Transaction::factory()->create([
                'user_id' => $user->id,
                'account_id' => $account->id,
                'category_id' => $category->id,
                'type' => $type,
            ]);
        }
        // NOTE: Seeder ini tidak mengkalkulasi ulang saldo akun,
        // karena saldo di factory sudah diisi random. Untuk aplikasi nyata, Anda bisa menambahkan logika kalkulasi.

        // 4. Buat Pihak Ketiga (Parties)
        $parties = Party::factory(5)->create(['user_id' => $user->id]);

        // 5. Buat Utang & Piutang
        Debt::factory()->create([
            'user_id' => $user->id,
            'party_id' => $parties->random()->id,
            'type' => DebtType::DEBT,
            'amount' => 5000000,
            'paid_amount' => 1000000,
            'status' => 'partially_paid',
        ]);
        Debt::factory()->create([
            'user_id' => $user->id,
            'party_id' => $parties->random()->id,
            'type' => DebtType::RECEIVABLE,
            'amount' => 750000,
            'status' => 'unpaid',
        ]);

        // 6. Buat Financial Goals
        FinancialGoal::factory()->create([
            'user_id' => $user->id,
            'name' => 'Dana Darurat',
            'target_amount' => 15000000,
            'current_amount' => 3500000,
        ]);
        FinancialGoal::factory()->create([
            'user_id' => $user->id,
            'name' => 'Liburan ke Jepang',
            'target_amount' => 30000000,
            'current_amount' => 5000000,
        ]);

        // 7. Buat Budget untuk bulan ini
        foreach ($expenseCategories->take(5) as $category) {
            Budget::factory()->create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'amount' => rand(500000, 2000000),
                'month' => now()->month,
                'year' => now()->year,
            ]);
        }
        
        // 8. Buat Transaksi Berulang
        RecurringTransaction::factory()->create([
            'user_id' => $user->id,
            'account_id' => $accounts->first()->id,
            'category_id' => $expenseCategories->first()->id,
            'type' => TransactionType::EXPENSE,
            'amount' => 500000,
            'description' => 'Bayar Tagihan Internet',
            'frequency' => 'monthly',
            'start_date' => now()->startOfMonth(),
            'next_due_date' => now()->startOfMonth()->day(5),
        ]);
    }
}