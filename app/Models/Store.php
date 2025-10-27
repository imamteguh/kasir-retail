<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Store extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'phone',
        'logo'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
