<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\DiscountTypeEnum;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id()->index();
            $table->string('code')->unique();
            $table->decimal('discount_value');
            $table->date('start_date');
            $table->date('end_date');

            $table->enum('discount_type', [
                DiscountTypeEnum::PERCENTEAGE->value,
                DiscountTypeEnum::FIXED->value,
            ])->default(DiscountTypeEnum::FIXED->value);
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->nullable();
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
        Schema::dropIfExists('vouchers');
    }
};
