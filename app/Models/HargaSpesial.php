<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HargaSpesial extends Model
{
    use SoftDeletes;

    protected $fillable = ['kategori_harga_spesial', 'potongan_product'];

}
