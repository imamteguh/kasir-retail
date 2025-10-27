<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCategory extends Model
{
    protected $fillable = [
        'store_id',
        'name'
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
