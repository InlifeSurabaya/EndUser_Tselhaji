<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\TransactionStatusController;
use App\Http\Controllers\Api\TransactionController;

// ============================================
// HEALTH & TEST ENDPOINTS
// ============================================

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'service' => 'Laravel B API',
        'timestamp' => now()->toISOString()
    ]);
});

Route::get('/test', function () {
    return response()->json([
        'message' => 'Laravel B API is working',
        'endpoints' => [
            'POST /api/transactions/store' => 'Create transaction',
            'GET /api/transactions/{transaction_number}' => 'Get transaction',
            'POST /api/transactions/update-status' => 'Update status from Laravel A',
            'POST /api/transactions/batch-fetch' => 'Fetch multiple transactions',
            'POST /api/transactions/create-test-sqlite' => 'Create test transaction',
            'POST /api/transactions/simple-update' => 'Simple status update',
            'GET /api/transactions/test/connection' => 'Test connection'
        ]
    ]);
});

// ============================================
// TRANSACTION API ENDPOINTS
// ============================================

Route::prefix('transactions')->group(function () {
    // TEST: Test endpoint
    Route::get('/test/connection', [TransactionStatusController::class, 'test']);

    // CREATE: Buat transaction baru di B (auto-sync ke A)
    Route::post('/store', [TransactionStatusController::class, 'store']);

    // GET: Ambil single transaction by transaction_number
    Route::get('/{transactionNumber}', [TransactionStatusController::class, 'getByTransactionNumber']);

    // UPDATE: Update status dari Laravel A
    Route::post('/update-status', [TransactionStatusController::class, 'updateStatus'])
        ->name('api.transactions.update-status');

    // BATCH: Ambil multiple transactions
    Route::post('/batch-fetch', [TransactionStatusController::class, 'batchFetch']);

    // TEST: Create test transaction (for development/testing)
    Route::post('/create-test-sqlite', function (Illuminate\Http\Request $request) {
        try {
            $request->validate([
                'transaction_number' => 'required|string|max:255',
                'gross_amount' => 'nullable|numeric'
            ]);

            $exists = DB::table('transactions')
                ->where('transaction_number', $request->transaction_number)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaction already exists',
                    'transaction_number' => $request->transaction_number
                ]);
            }

            // FIX: Buat order dummy dulu jika tidak ada
            $dummyOrder = DB::table('orders')->where('id', 999)->first();
            if (!$dummyOrder) {
                DB::table('orders')->insert([
                    'id' => 999,
                    'order_number' => 'DUMMY-999',
                    'user_id' => 1,
                    'product_id' => 1,
                    'category_country_product_id' => 1,
                    'original_price' => 0,
                    'discount_amount' => 0,
                    'final_price' => 0,
                    'status' => 'pending',
                    'customer_name' => 'Dummy',
                    'uuid' => 'dummy-999',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Insert transaction dengan order_id = 999
            DB::table('transactions')->insert([
                'transaction_number' => $request->transaction_number,
                'order_id' => 999, // ORDER ID VALID
                'user_id' => 1,
                'gross_amount' => $request->gross_amount ?? 100000,
                'net_amount' => $request->gross_amount ?? 100000,
                'payment_type' => 'qris',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test transaction created',
                'transaction_number' => $request->transaction_number
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'error_details' => $e->getTraceAsString()
            ], 500);
        }
    });

    // SIMPLE UPDATE: Endpoint sederhana untuk update status
    Route::post('/simple-update', function (Illuminate\Http\Request $request) {
        Log::info('SIMPLE UPDATE', $request->all());

        $transactionNumber = $request->input('transaction_number');
        $status = $request->input('status', 'settlement');

        if (!$transactionNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction number required'
            ], 400);
        }

        $updated = DB::table('transactions')
            ->where('transaction_number', $transactionNumber)
            ->update([
                'status' => $status,
                'settlement_time' => now(),
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated via simple endpoint',
            'transaction_number' => $transactionNumber,
            'old_status' => 'pending',
            'new_status' => $status,
            'updated_rows' => $updated
        ]);
    });
});
