<?php

namespace App\Http\Controllers;

use App\Enum\TransactionStatusEnum;
use App\Models\Order;
use Illuminate\Http\Request;

class MidtransWebHookController extends Controller
{
    /**
     * Handle notifikasi webhook dari Midtrans.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        try {
            $payload = $request->all();

            $serverKey = config('MIDTRANS_SERVER_KEY');

            $signatureKey = $this->generateSignature($payload, $serverKey);

            if ($payload['signature_key'] !== $signatureKey) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid signature.',
                ], 403); // 403 Forbidden
            }


            $orderId = $payload['order_id'];
            $statusCode = $payload['status_code'];
            $transactionStatus = $payload['transaction_status'];

            // Cari order di database Anda
            $order = Order::with('transaction')
                ->where('order_number', $orderId)
                ->first();

            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found.',
                ], 404);
            }

            if ($order->status === 'paid' && $transactionStatus === 'settlement') {
                return response()->json(['status' => 'ok', 'message' => 'Already processed.']);
            }

            $this->updateOrderStatus($order, $transactionStatus);

            return response()->json(['status' => 'ok', 'message' => 'Notification processed.']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong.'], 404);
        }

    }

    /**
     * Hasilkan signature key untuk verifikasi.
     * Rumus: hash('sha512', order_id . status_code . gross_amount . server_key)
     */
    private function generateSignature($payload, $serverKey)
    {
        $orderId = $payload['order_id'];
        $statusCode = $payload['status_code'];
        $grossAmount = $payload['gross_amount'];

        $stringToHash = $orderId . $statusCode . $grossAmount . $serverKey;

        // Hasilkan hash SHA-512
        return hash('sha512', $stringToHash);
    }

    /**
     * Logika untuk meng-update status order di database Anda.
     */
    private function updateOrderStatus(Order $order, $transactionStatus)
    {
        switch ($transactionStatus) {
            case 'settlement':
                $order->status = TransactionStatusEnum::SETTLEMENT->value;
                $order->transaction->status = TransactionStatusEnum::SETTLEMENT->value;
                $order->transaction->settlement_time = now();
                $order->settlement_time = now();

                // TODO: Kirim email ke user, generate invoice, dll.
                break;

            case 'pending':
                // Menunggu pembayaran
                $order->status = TransactionStatusEnum::PENDING->value;
                $order->transaction->status = TransactionStatusEnum::PENDING->value;
                break;
            case 'expire':
                // Pembayaran kadaluarsa
                $order->status = TransactionStatusEnum::EXPIRE->value;
                $order->transaction->status = TransactionStatusEnum::EXPIRE->value;
                break;

            case 'cancel':
                $order->status = TransactionStatusEnum::CANCEL->value;
                $order->transaction->status = TransactionStatusEnum::CANCEL->value;
                break;
            case 'deny':
                $order->status = TransactionStatusEnum::DENY->value;
                $order->transaction->status = TransactionStatusEnum::DENY->value;
                break;
        }

        // Simpan perubahan status ke database
        $order->save();
    }
}
