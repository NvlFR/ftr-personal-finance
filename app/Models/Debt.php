<?php

namespace App\Models;

use App\DebtStatus;
use App\DebtType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Debt extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'party_id', 'type', 'description', 'amount', 'paid_amount', 'due_date', 'status'];
    protected $casts = ['type' => DebtType::class, 'status' => DebtStatus::class, 'amount' => 'decimal:2', 'paid_amount' => 'decimal:2', 'due_date' => 'date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
    public function payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class);
    }

    protected function remainingAmount(): Attribute
    {
        return Attribute::make(get: fn() => $this->amount - $this->paid_amount);
    }
}
