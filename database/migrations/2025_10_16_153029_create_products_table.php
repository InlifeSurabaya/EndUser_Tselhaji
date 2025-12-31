<?php

use App\Enum\QuotaTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id()->index();
            $table->string('name')->nullable();
            $table->text('detail')->nullable();
            $table->bigInteger('harga');
            $table->float('quota_amount');
            $table->enum('quota_type', [
                QuotaTypeEnum::GB->value,
                QuotaTypeEnum::MB->value,
            ])->default(QuotaTypeEnum::GB->value);
            $table->integer('validity_days');
            $table->integer('discount')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
