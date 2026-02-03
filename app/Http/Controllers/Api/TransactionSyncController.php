<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class TransactionSyncController extends Controller
{
    /**
     * Update transaction status from Laravel A
     */
    public function updateStatus(Request $request)
    {
        Log::info('🔄 SYNC FROM LARAVEL A RECEIVED', $request->all());

        try {
            // Validasi
            $validator = Validator::make($request->all(), [
                'transaction_number' => 'required|string',
                'status' => 'required|string|in:pending,completed,approved,rejected,settlement',
                'approval_data' => 'required|array',
                'approval_data.id_transaksi' => 'required|string',
                'timestamp' => 'required|string',
                'request_id' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $transactionNumber = $request->input('transaction_number');
            $status = $request->input('status');
            $approvalData = $request->input('approval_data');
            $requestId = $request->input('request_id');

            // Cari transaksi di Laravel B
            $transaction = DB::table('transactions')
                ->where('transaction_number', $transactionNumber)
                ->orWhere('order_id', $transactionNumber)
                ->orWhere('reference_number', $transactionNumber)
                ->first();

            if (!$transaction) {
                Log::warning('Transaction not found in Laravel B for sync', [
                    'transaction_number' => $transactionNumber,
                    'search_fields' => ['transaction_number', 'order_id', 'reference_number']
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found in Laravel B',
                    'transaction_number' => $transactionNumber
                ], 404);
            }

            // Siapkan data update dengan cek kolom
            $updateData = [
                'status' => $status,
                'updated_at' => now(),
            ];

            // Hanya tambahkan kolom jika ada
            $columns = Schema::getColumnListing('transactions');

            if (in_array('sync_from_a', $columns)) {
                $updateData['sync_from_a'] = true;
            }

            if (in_array('sync_timestamp', $columns)) {
                $updateData['sync_timestamp'] = now();
            }

            if (in_array('sync_request_id', $columns)) {
                $updateData['sync_request_id'] = $requestId;
            }

            if (in_array('approved_by', $columns) && isset($approvalData['approved_by'])) {
                $updateData['approved_by'] = $approvalData['approved_by'];
            }

            if (in_array('approved_at', $columns) && isset($approvalData['approved_at'])) {
                $updateData['approved_at'] = $approvalData['approved_at'];
            }

            if (in_array('sync_data', $columns)) {
                $updateData['sync_data'] = json_encode($approvalData);
            }

            // Update transaksi
            $updated = DB::table('transactions')
                ->where('transaction_number', $transactionNumber)
                ->orWhere('order_id', $transactionNumber)
                ->orWhere('reference_number', $transactionNumber)
                ->update($updateData);

            Log::info('✅ Transaction updated in Laravel B from sync', [
                'transaction_number' => $transactionNumber,
                'new_status' => $status,
                'id_transaksi_from_a' => $approvalData['id_transaksi'] ?? null,
                'rows_affected' => $updated,
                'update_data' => $updateData
            ]);

            // Simpan log sync
            $this->saveSyncLog($transactionNumber, $request->all(), $updated);

            return response()->json([
                'success' => true,
                'message' => 'Transaction status synced successfully',
                'transaction_number' => $transactionNumber,
                'status_updated_to' => $status,
                'id_transaksi_a' => $approvalData['id_transaksi'] ?? null,
                'timestamp' => now()->toISOString(),
                'sync_request_id' => $requestId,
                'update_applied' => $updateData
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Sync from Laravel A failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sync failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save sync log
     */
    private function saveSyncLog($transactionNumber, $requestData, $updated)
    {
        try {
            // Cek atau buat table tanpa SQL langsung
            if (!Schema::hasTable('transaction_sync_logs')) {
                // Create table via Schema builder
                Schema::create('transaction_sync_logs', function ($table) {
                    $table->id();
                    $table->string('transaction_number');
                    $table->string('source');
                    $table->text('request_data')->nullable();
                    $table->integer('updated_rows')->default(0);
                    $table->timestamps();
                });

                Log::info('Created transaction_sync_logs table');
            }

            DB::table('transaction_sync_logs')->insert([
                'transaction_number' => $transactionNumber,
                'source' => 'laravel_a_sync',
                'request_data' => json_encode($requestData),
                'updated_rows' => $updated,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Sync log saved successfully');

        } catch (\Exception $e) {
            Log::warning('Failed to save sync log (non-critical): ' . $e->getMessage());
            // Jangan throw error, karena ini hanya logging
        }
    }

    /**
     * Check sync status
     */
    public function checkSyncStatus($transactionNumber)
    {
        try {
            $transaction = DB::table('transactions')
                ->where('transaction_number', $transactionNumber)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            // Get sync logs if any
            $syncLogs = [];
            if (Schema::hasTable('transaction_sync_logs')) {
                $syncLogs = DB::table('transaction_sync_logs')
                    ->where('transaction_number', $transactionNumber)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            return response()->json([
                'success' => true,
                'transaction' => [
                    'transaction_number' => $transaction->transaction_number,
                    'status' => $transaction->status,
                    'sync_from_a' => $transaction->sync_from_a ?? false,
                    'sync_timestamp' => $transaction->sync_timestamp ?? null,
                    'approved_by' => $transaction->approved_by ?? null,
                    'approved_at' => $transaction->approved_at ?? null
                ],
                'sync_logs' => $syncLogs,
                'last_sync' => $syncLogs->first()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test endpoint
     */
    public function test()
    {
        return response()->json([
            'endpoint' => '/api/transactions/update-status',
            'method' => 'POST',
            'purpose' => 'Receive sync updates from Laravel A',
            'expected_payload' => [
                'transaction_number' => 'string (required)',
                'status' => 'string (required)',
                'approval_data' => 'array (required)',
                'timestamp' => 'ISO8601 date (required)',
                'request_id' => 'string (required)'
            ],
            'notes' => 'This endpoint updates transaction status in Laravel B when Laravel A approves a transaction'
        ]);
    }
}
