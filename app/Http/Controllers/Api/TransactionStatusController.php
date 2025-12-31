<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Transaction as BTransaction;

class TransactionStatusController extends Controller
{
    /**
     * Update status dari Laravel A
     */
    public function updateStatus(Request $request)
    {
        Log::info('📥 [B] Received status update from Laravel A', $request->all());

        try {
            $validated = $request->validate([
                'transaction_number' => 'required|string',
                'status' => 'required|string|in:pending,settlement,failed,expired,cancelled',
                'approval_data' => 'required|array',
                'timestamp' => 'required|date',
                'source' => 'required|string'
            ]);

            $transactionNumber = $validated['transaction_number'];

            Log::info('🔍 [B] Looking for order/transaction', [
                'transaction_number' => $transactionNumber
            ]);

            // Cari order berdasarkan order_number atau transaction_number
            $order = Order::where('order_number', $transactionNumber)
                         ->orWhereHas('transaction', function($q) use ($transactionNumber) {
                             $q->where('transaction_number', $transactionNumber);
                         })
                         ->with('transaction')
                         ->first();

            if (!$order) {
                Log::warning('❌ [B] Order not found', ['transaction_number' => $transactionNumber]);
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            Log::info('✅ [B] Order found', [
                'order_id' => $order->id,
                'current_status' => $order->status,
                'new_status' => $validated['status']
            ]);

            // Update order
            $order->update([
                'status' => $validated['status'],
                'updated_at' => now(),
                'notes' => ($order->notes ?? '') . "\n\n🔄 Status updated from Laravel A: " .
                          $validated['status'] . " at " . now()->format('Y-m-d H:i:s') .
                          "\nApproved by: " . ($validated['approval_data']['approved_by'] ?? 'unknown') .
                          "\nMethod: " . ($validated['approval_data']['metode_pembayaran'] ?? 'cash') .
                          "\nTime: " . ($validated['approval_data']['approved_at'] ?? 'unknown')
            ]);

            // Update transaction jika ada
            if ($order->transaction) {
                $order->transaction->update([
                    'status' => $validated['status'],
                    'settlement_time' => $validated['approval_data']['payment_settled_at'] ?? null,
                    'updated_at' => now()
                ]);
            }

            Log::info('✅ [B] Order updated successfully', [
                'order_number' => $order->order_number,
                'old_status' => $order->getOriginal('status'),
                'new_status' => $validated['status']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'updated_at' => $order->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('💥 [B] Error updating status: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
