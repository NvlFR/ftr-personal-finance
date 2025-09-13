<?php

namespace App\Console\Commands;

use App\Models\RecurringTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateRecurringTransactions extends Command
{
    protected $signature = 'app:generate-recurring-transactions';
    protected $description = 'Generate transactions from active recurring transaction schedules';

    public function handle()
    {
        $this->info('Mulai membuat transaksi berulang...');

        // Cari semua jadwal yang aktif dan sudah jatuh tempo (next_due_date <= hari ini)
        $recurringTransactions = RecurringTransaction::where('is_active', true)
            ->whereDate('next_due_date', '<=', today())
            ->get();

        foreach ($recurringTransactions as $recurring) {
            DB::transaction(function () use ($recurring) {
                // 1. Buat Transaksi Nyata
                $recurring->account->transactions()->create([
                    'user_id' => $recurring->user_id,
                    'category_id' => $recurring->category_id,
                    'type' => $recurring->type,
                    'amount' => $recurring->amount,
                    'description' => $recurring->description,
                    'transaction_date' => $recurring->next_due_date,
                ]);

                // 2. Update Saldo Akun
                if ($recurring->type === \App\TransactionType::INCOME) {
                    $recurring->account->balance += $recurring->amount;
                } else {
                    $recurring->account->balance -= $recurring->amount;
                }
                $recurring->account->save();

                // 3. Hitung dan Update Tanggal Jatuh Tempo Berikutnya
                $newDueDate = $this->calculateNextDueDate($recurring->next_due_date, $recurring->frequency->value, $recurring->interval);

                // 4. Nonaktifkan jika sudah melewati end_date
                if ($recurring->end_date && $newDueDate->gt($recurring->end_date)) {
                    $recurring->is_active = false;
                } else {
                    $recurring->next_due_date = $newDueDate;
                }

                $recurring->save();

                $this->info("Transaksi dibuat untuk: {$recurring->description}");
            });
        }

        $this->info('Selesai.');
        return 0;
    }

    private function calculateNextDueDate(Carbon $currentDueDate, string $frequency, int $interval): Carbon
    {
        return match ($frequency) {
            'daily' => $currentDueDate->addDays($interval),
            'weekly' => $currentDueDate->addWeeks($interval),
            'monthly' => $currentDueDate->addMonths($interval),
            'yearly' => $currentDueDate->addYears($interval),
        };
    }
}
