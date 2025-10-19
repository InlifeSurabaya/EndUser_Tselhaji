<?php

namespace App\Models;

use App\Traits\GenerateNumberTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rinvex\Country\Country;

class Order extends Model
{
    use HasFactory, SoftDeletes, GenerateNumberTrait;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_number', 'voucher_id', 'category_country_product_id', 'original_price', 'discount_amount', 'final_price', 'status', 'customer_name', 'customer_email', 'customer_phone', 'notes', 'expired_at', 'settlement_time',
    ];

    /**
     * Override prefix default dari trait.
     * @var string
     */
    protected string $invoicePrefix = 'ORD';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }
}
