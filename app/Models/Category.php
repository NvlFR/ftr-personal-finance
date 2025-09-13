<?php

namespace App\Models;

use App\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'icon',
        'type',
    ];

    protected $casts = [
        // Kita gunakan lagi Enum yang sama dari fitur sebelumnya
        'type' => TransactionType::class,
    ];

    /**
     * Relasi ke User: Kategori ini dimiliki oleh siapa.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Transaction: Satu kategori bisa memiliki banyak transaksi.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
