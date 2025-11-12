<?php

use App\Enum\OrderStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->index();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('voucher_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('category_country_product_id')->constrained()->onDelete('cascade');

            // Pricing details
            $table->decimal('original_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_price', 10, 2);

            // Order status khusus QRIS
            $table->enum('status', [
                OrderStatusEnum::PENDING->value,
                OrderStatusEnum::SETTLEMENT->value,
                OrderStatusEnum::EXPIRE->value,
                OrderStatusEnum::CANCEL->value,
                OrderStatusEnum::DENY->value,
            ])->default(OrderStatusEnum::PENDING->value);

            // Customer details untuk Midtrans
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();

            $table->text('notes')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('settlement_time')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['status', 'expired_at']);
            $table->index('order_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
