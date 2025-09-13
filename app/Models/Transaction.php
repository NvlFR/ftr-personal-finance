<?php

namespace App\Models;

use App\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'account_id',
        'type',
        'amount',
        'description',
        'transaction_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => TransactionType::class, // Menggunakan Enum yang kita buat
        'amount' => 'decimal:2',         // Menyimpan nilai uang dengan 2 angka desimal
        'transaction_date' => 'date',    // Otomatis di-cast ke objek Carbon
    ];

    /**
     * Relasi ke model User: Setiap transaksi dimiliki oleh satu user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model Category: Setiap transaksi masuk dalam satu kategori.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke model Account (Dompet): Setiap transaksi berasal dari/masuk ke satu akun.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
