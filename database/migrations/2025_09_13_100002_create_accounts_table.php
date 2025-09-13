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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('balance', 15, 2)->default(0.00); // Saldo saat ini
            $table->string('icon')->nullable(); // Ikon untuk UI
            $table->string('color', 7)->nullable(); // Kode Hex warna (e.g., #FFFFFF)
            $table->boolean('is_active')->default(true); // Untuk menonaktifkan akun lama
            $table->timestamps();

            // Nama akun harus unik untuk setiap user
            $table->unique(['user_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
