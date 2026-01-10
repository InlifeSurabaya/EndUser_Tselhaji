<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\UserSegmentEnum;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('harga_spesials', function (Blueprint $table) {
            $table->id()->index();
            $table->enum('kategori_harga_spesial', [
                UserSegmentEnum::LOYAL->value,
                UserSegmentEnum::ACTIVE->value,
                UserSegmentEnum::NEW->value,
            ]);
            $table->integer('potongan_product');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_spesials');
    }
};
