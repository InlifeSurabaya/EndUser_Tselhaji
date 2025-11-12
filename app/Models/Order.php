<?php

namespace App\Models;

use App\Traits\GenerateNumberTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Rinvex\Country\Country;

class Order extends Model
{
    use GenerateNumberTrait, HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'uuid',
        'product_id',
        'order_number',
        'voucher_id',
        'category_country_product_id',
        'original_price',
        'discount_amount',
        'final_price',
        'status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'url_midtrans',
        'notes',
        'expired_at',
        'settlement_time',
    ];

    /**
     * Override prefix default dari trait.
     */
    protected string $invoicePrefix = 'ORD';

    /**
     * Override nama kolom db
     */
    protected string $invoiceNumberField = 'order_number';

    protected int $randomLength = 8;

    /**
     * Generate uuid otomatis
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }

            if (empty($model->{$model->getInvoiceNumberField()})) {
                $model->{$model->getInvoiceNumberField()} = $model->generateInvoiceNumber();
            }
        });
    }

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
