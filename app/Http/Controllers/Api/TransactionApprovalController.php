<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionApprovalController extends Controller
{
    /**
     * Handle approval from Laravel A
     */
    public function handleApproval(Request $request)
    {
        Log::info('✅ APPROVAL REQUEST FROM LARAVEL A', $request->all());

        try {
            // Validasi
            $validator = Validator::make($request->all(), [
                'transaction_number' => 'required|string',
                'approval_status' => 'required|in:approved,rejected',
                'approved_by' => 'required|string',
                'approved_at' => 'required|date',
                'reference_data.id_transaksi' => 'required|string'
            ]);

            if ($validator->fails()) {
                Log::warning('Approval request validation failed', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $transactionNumber = $request->input('transaction_number');
            $approvalStatus = $request->input('approval_status');
            $approvedBy = $request->input('approved_by');
            $approvedAt = $request->input('approved_at');
            $idTransaksi = $request->input('reference_data.id_transaksi');
            $notes = $request->input('notes');

            // Cek transaction di Laravel B
            $transaction = DB::table('transactions')
                ->where('transaction_number', $transactionNumber)
                ->orWhere('order_id', $transactionNumber)
                ->first();

            if (!$transaction) {
                Log::warning('Transaction not found in Laravel B', [
                    'transaction_number' => $transactionNumber,
                    'search_by' => 'transaction_number/order_id'
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found in Laravel B',
                    'transaction_number' => $transactionNumber
                ], 404);
            }

            // Update approval status di Laravel B - FIXED VERSION
            $updateData = [
                'status' => 'completed',  // Update kolom 'status' yang ada
                'updated_at' => now(),
            ];

            $updated = DB::table('transactions')
                ->where('transaction_number', $transactionNumber)
                ->orWhere('order_id', $transactionNumber)
                ->update($updateData);

            Log::info('Transaction approval updated in Laravel B', [
                'transaction_number' => $transactionNumber,
                'updated' => $updated,
                'approval_status' => $approvalStatus,
                'id_transaksi_reference' => $idTransaksi
            ]);

            // Log activity
            $this->logApprovalActivity($transactionNumber, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Transaction approval status updated',
                'transaction_number' => $transactionNumber,
                'approval_status' => $approvalStatus,
                'approved_by' => $approvedBy,
                'approved_at' => $approvedAt,
                'id_transaksi_reference' => $idTransaksi,
                'updated_in_b' => $updated,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Transaction approval error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log approval activity
     */
    private function logApprovalActivity($transactionNumber, $requestData)
    {
        try {
            // Cek apakah table exists
            if (!DB::getSchemaBuilder()->hasTable('transaction_approval_logs')) {
                DB::statement('
                    CREATE TABLE IF NOT EXISTS transaction_approval_logs (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        transaction_number VARCHAR(255),
                        action VARCHAR(100),
                        request_data TEXT,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ');
            }

            DB::table('transaction_approval_logs')->insert([
                'transaction_number' => $transactionNumber,
                'action' => 'approved_from_laravel_a',
                'request_data' => json_encode($requestData),
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log approval activity: ' . $e->getMessage());
        }
    }

    /**
     * Get transaction status
     */
    public function getStatus($transactionNumber)
    {
        try {
            $transaction = DB::table('transactions')
                ->where('transaction_number', $transactionNumber)
                ->orWhere('order_id', $transactionNumber)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_number' => $transaction->transaction_number,
                    'order_id' => $transaction->order_id,
                    'status' => $transaction->status,
                    'approved_by' => $transaction->approved_by ?? null,
                    'approved_at' => $transaction->approved_at ?? null
                ]
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
            'message' => 'Transaction Approval Endpoint Ready',
            'endpoint' => '/api/transactions/approval',
            'method' => 'POST',
            'purpose' => 'Receive approval status from Laravel A',
            'expected_payload' => [
                'transaction_number' => 'string',
                'approval_status' => 'approved/rejected',
                'approved_by' => 'string',
                'approved_at' => 'ISO8601 date',
                'reference_data' => [
                    'id_transaksi' => 'string'
                ],
                'notes' => 'string (optional)'
            ]
        ]);
    }
}
