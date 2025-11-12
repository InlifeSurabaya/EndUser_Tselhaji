<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    /** @use HasFactory<\Database\Factories\VoucherFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['code', 'discount_value', 'start_date', 'end_date', 'discount_type', 'usage_limit', 'used_count', 'is_active', 'user_can_see'];
}
