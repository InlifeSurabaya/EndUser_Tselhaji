<?php

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
       // database/migrations/xxxx_create_roaming_datas_table.php
Schema::create('roaming_data', function (Blueprint $table) {
    $table->id();
    $table->integer('event_date');
    $table->string('area');
    $table->string('regional');
    $table->string('cluster');
    $table->string('kabupaten');
    $table->string('id_pelanggan');
    $table->integer('package_keyword');
    $table->string('package_name');
    $table->integer('grouping');
    $table->integer('day');
    $table->integer('quota_bns_mb');
    $table->decimal('rev', 15, 2);
    $table->integer('trx');
    $table->integer('subs');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roaming_data');
    }
};
