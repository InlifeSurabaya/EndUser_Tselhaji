<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionSyncLogsTable extends Migration
{
    public function up()
    {
        Schema::create('transaction_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number');
            $table->string('source');
            $table->json('request_data');
            $table->integer('updated_rows')->default(0);
            $table->timestamps();

            $table->index('transaction_number');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaction_sync_logs');
    }
}
