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

    protected $fillable = [
        'transaction_number', 'order_id', 'user_id', 'gross_amount', 'admin_fee', 'net_amount', 'payment_type', 'qris_issuer', 'qris_content', 'acquirer', 'status', 'midtrans_order_id', 'midtrans_transaction_id', 'midtrans_token', 'qris_url', 'payment_url', 'currency', 'transaction_time', 'settlement_time', 'expiry_time',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
