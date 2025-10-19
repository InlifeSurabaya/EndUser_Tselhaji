<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'detail', 'harga', 'quota_amount', 'quota_type', 'validity_days', 'discount', 'is_active', 'country_id'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(CategoryCountryProduct::class);
    }

    public function order(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
