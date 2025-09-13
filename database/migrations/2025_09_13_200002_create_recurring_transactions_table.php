<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recurring_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');

            $table->string('type'); // 'income' atau 'expense'
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable();

            // Jadwal
            $table->string('frequency'); // daily, weekly, monthly, yearly
            $table->unsignedTinyInteger('interval')->default(1); // e.g., every '1' month, or every '2' weeks
            $table->date('start_date');
            $table->date('end_date')->nullable(); // Kapan berhenti (opsional)
            $table->date('next_due_date'); // PENTING: Untuk melacak kapan transaksi berikutnya harus dibuat

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_transactions');
    }
};
