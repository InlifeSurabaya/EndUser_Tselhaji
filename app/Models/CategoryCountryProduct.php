<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryCountryProduct extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryCountryProductFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'country_code'];

    public function product(): HasMany
    {
        return $this->hasMany(Product::class, 'country_id');
    }
}
