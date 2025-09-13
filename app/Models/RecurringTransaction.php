<?php

namespace App\Models;

use App\RecurrenceFrequency;
use App\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'type',
        'amount',
        'description',
        'frequency',
        'interval',
        'start_date',
        'end_date',
        'next_due_date',
        'is_active',
    ];

    protected $casts = [
        'type' => TransactionType::class,
        'frequency' => RecurrenceFrequency::class,
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'next_due_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
