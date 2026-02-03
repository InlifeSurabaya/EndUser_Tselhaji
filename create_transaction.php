<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$trx_number = 'DIRECT-TRX-' . time();

try {
    DB::table('transactions')->insert([
        'transaction_number' => $trx_number,
        'order_id' => rand(1000, 9999),
        'user_id' => 1,
        'gross_amount' => 100000,
        'net_amount' => 95000,
        'payment_type' => 'qris',
        'status' => 'pending',
        'transaction_time' => now(),
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "✅ SUCCESS: Transaction created: $trx_number\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
