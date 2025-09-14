<?php

namespace App\Models;

use App\GoalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'target_amount',
        'current_amount',
        'target_date',
        'status',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'target_date' => 'date',
        'status' => GoalStatus::class,
    ];

    // Tambahkan baris ini
    protected $appends = ['percentage'];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor: Menghitung persentase progres.
     */
    protected function percentage(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->target_amount > 0
                ? round(($this->current_amount / $this->target_amount) * 100, 2)
                : 0,
        );
    }

    /**
     * Accessor: Menghitung sisa uang yang dibutuhkan.
     */
    protected function remainingAmount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->target_amount - $this->current_amount,
        );
    }
}