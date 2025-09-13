<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Party extends Model
{
    protected $fillable = ['user_id', 'name', 'contact_info'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
