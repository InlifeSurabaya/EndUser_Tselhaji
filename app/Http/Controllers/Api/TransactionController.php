<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    /**
     * Create new transaction in Laravel B and sync to Laravel A
     */
    public function store(Request $request)
    {
        try {
            Log::info('🆕 CREATE TRANSACTION IN LARAVEL B', $request->all());

            $validator = Validator::make($request->all(), [
                'transaction_number' => 'required|string|unique:transactions',
                'order_id' => 'nullable|integer',
                'gross_amount' => 'required|numeric',
                'payment_type' => 'required|string',
                'customer_name' => 'nullable|string',
                'customer_email' => 'nullable|email',
                'metadata' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Insert to Laravel B
            $transactionData = [
                'transaction_number' => $request->transaction_number,
                'order_id' => $request->order_id ?? rand(1000, 9999),
                'user_id' => 1, // Default user
                'gross_amount' => $request->gross_amount,
                'net_amount' => $request->gross_amount * 0.95, // Example calculation
                'payment_type' => $request->payment_type,
                'status' => 'pending',
                'transaction_time' => now(),
                'settlement_time' => null,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'metadata' => json_encode($request->metadata ?? []),
                'created_at' => now(),
                'updated_at' => now()
            ];

            DB::table('transactions')->insert($transactionData);

            Log::info('✅ Transaction created in Laravel B', [
                'transaction_number' => $request->transaction_number,
                'amount' => $request->gross_amount
            ]);

            // Auto-sync to Laravel A
            $syncResult = $this->syncToLaravelA($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'data' => $transactionData,
                'sync_to_a' => $syncResult
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Create transaction failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Create transaction failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync transaction to Laravel A
     */
    private function syncToLaravelA($transactionData)
    {
        try {
            $laravelAUrl = 'http://localhost:8000'; // Laravel A URL

            $payload = [
                'transaction_number' => $transactionData['transaction_number'],
                'order_id' => $transactionData['order_id'] ?? null,
                'gross_amount' => $transactionData['gross_amount'],
                'payment_type' => $transactionData['payment_type'],
                'status' => 'pending',
                'customer_name' => $transactionData['customer_name'] ?? 'Customer from B',
                'customer_email' => $transactionData['customer_email'] ?? null,
                'metadata' => $transactionData['metadata'] ?? [],
                'transaction_time' => now()->toISOString()
            ];

            Log::info('🔄 Syncing to Laravel A', [
                'url' => $laravelAUrl . '/api/sync/from-b',
                'payload' => $payload
            ]);

            $response = Http::timeout(10)
                ->post($laravelAUrl . '/api/sync/from-b', $payload);

            return [
                'sent' => true,
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'response' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Failed to sync to Laravel A', [
                'error' => $e->getMessage()
            ]);

            return [
                'sent' => false,
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update transaction status from Laravel A
     */
    public function updateStatus(Request $request)
    {
        try {
            Log::info('🔄 UPDATE STATUS FROM LARAVEL A', $request->all());

            $validator = Validator::make($request->all(), [
                'transaction_number' => 'required|string',
                'status' => 'required|string|in:settlement,completed,approved',
                'approval_data' => 'required|array',
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

            // Update transaction in Laravel B
            $updateData = [
                'status' => $request->status,
                'settlement_time' => now(),
                'updated_at' => now(),
                'metadata' => json_encode([
                    'approved_by' => $request->input('approval_data.approved_by'),
                    'approved_at' => $request->input('approval_data.approved_at'),
                    'laravel_a_reference' => $request->input('approval_data.id_transaksi'),
                    'sync_from_a' => true,
                    'sync_timestamp' => now()->toISOString(),
                    'sync_request_id' => $request->request_id
                ])
            ];

            $updated = DB::table('transactions')
                ->where('transaction_number', $request->transaction_number)
                ->update($updateData);

            if ($updated) {
                Log::info('✅ Transaction status updated in Laravel B', [
                    'transaction_number' => $request->transaction_number,
                    'new_status' => $request->status,
                    'approved_by' => $request->input('approval_data.approved_by')
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Transaction status updated successfully',
                    'data' => [
                        'transaction_number' => $request->transaction_number,
                        'status' => $request->status,
                        'settlement_time' => now()->toISOString()
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Transaction not found in Laravel B'
            ], 404);

        } catch (\Exception $e) {
            Log::error('❌ Update status failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Update status failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get transaction by transaction_number
     */
    public function show($transactionNumber)
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

            return response()->json([
                'success' => true,
                'message' => 'Transaction found',
                'data' => $transaction
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test endpoint
     */
    public function test()
    {
        return response()->json([
            'message' => 'Transaction API is working',
            'endpoints' => [
                'POST /api/transactions' => 'Create new transaction',
                'POST /api/transactions/update-status' => 'Update status from Laravel A',
                'GET /api/transactions/{transaction_number}' => 'Get transaction details'
            ],
            'timestamp' => now()->toISOString()
        ]);
    }
}
