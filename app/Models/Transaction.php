<?php

namespace App\Models;

use App\Traits\GenerateNumberTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes, GenerateNumberTrait;

    /**
     * Override prefix default dari trait.
     * @var string
     */
    protected string $invoicePrefix = 'INV';
    protected string $invoiceNumberField = 'transaction_number';
    protected int $randomLength = 8;


    protected $fillable = [
        'expiry_time', 'settlement_time', 'transaction_time', 'currency', 'payment_url', 'qris_url', 'midtrans_token', 'midtrans_transaction_id', 'midtrans_order_id', 'status', 'acquirer', 'qris_content', 'qris_issuer', 'payment_type', 'net_amount', 'admin_fee', 'gross_amount', 'user_id', 'order_id', 'transaction_number',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {


            if (empty($model->{$model->getInvoiceNumberField()})) {
                $model->{$model->getInvoiceNumberField()} = $model->generateInvoiceNumber();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
