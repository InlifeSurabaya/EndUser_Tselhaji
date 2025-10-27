<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\TransactionStatusEnum;
return new class extends Migration {
  public function up(): void
  {
    Schema::create('transactions', function (Blueprint $table) {
        $table->id()->index();
        $table->string('transaction_number')->unique();
        $table->foreignId('order_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');

        // Amount details
        $table->decimal('gross_amount', 10, 2);
        $table->decimal('admin_fee', 10, 2)->default(0);
        $table->decimal('net_amount', 10, 2);

        // QRIS specific fields
        $table->string('payment_type')->default('qris'); // selalu qris

        // Transaction status khusus QRIS
        $table->enum('status', [
            TransactionStatusEnum::PENDING->value,
            TransactionStatusEnum::SETTLEMENT->value,
            TransactionStatusEnum::EXPIRE->value,
            TransactionStatusEnum::CANCEL->value,
            TransactionStatusEnum::FAILURE->value,
        ])->default(TransactionStatusEnum::PENDING->value);

        // Midtrans specific fields untuk QRIS
        $table->string('midtrans_order_id')->nullable()->unique(); // order_id dari Midtrans
        $table->string('midtrans_transaction_id')->nullable()->unique(); // transaction_id dari Midtrans
        $table->string('midtrans_token')->nullable(); // snap token

        // QRIS specific timestamps
        $table->timestamp('transaction_time')->nullable();
        $table->timestamp('settlement_time')->nullable();
        $table->timestamp('expiry_time')->nullable();
        $table->timestamps();
        $table->softDeletes();

        // Indexes
        $table->index(['user_id', 'status']);
        $table->index(['order_id', 'status']);
        $table->index(['status', 'expiry_time']);
        $table->index('transaction_number');
        $table->index('midtrans_order_id');
        $table->index('midtrans_transaction_id');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('transactions');
  }
};
