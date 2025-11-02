<?php

namespace App\Livewire\Order;

use App\Enum\TransactionStatusEnum;
use App\Models\Order;
use App\Models\Transaction as TransactionModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Component;
use Midtrans\Snap;

#[Title('Detail Order')]
class Detail extends Component
{
    public $order;

    public function mount(string $uuidOrder): void
    {
        $this->order = Order::with([
            'voucher',
            'product',
            'user'
        ])
            ->where('uuid', $uuidOrder)
            ->firstOrFail();

        Log::info('Order Detail', ['order' => $this->order]);
    }

    /**
     * Redirect user ke halaman pembayaran midtrans
     * @param int $orderId
     * @return \Illuminate\Http\RedirectResponse|void
     * @throws \Throwable
     */
    public function createTransactionMidtrans(int $orderId)
    {
        DB::beginTransaction();
        try {
            $order = Order::with(['user', 'transaction', 'product'])->findOrFail($orderId);

            // Check apakah user sudah pernah klik
            if ($order->url_midtrans && Carbon::parse($order->transaction->expiry_time)->isPast()) {
                return redirect()->to($order->url_midtrans);
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) $order->final_price,
                ],
                'customer_details' => [
                    'first_name' => $order->user->userProfile->fullname ?? $order->customer_email,
                    'email' => $order->customer_email,
                ],
                'enabled_payments' => [
                    'other_qris'
                ],
                'expiry' => [
                    'start_time' => Carbon::now()->format('Y-m-d H:i:s O'), // e.g. "2025-10-21 16:00:00 +0700"
                    'unit'       => 'hour',
                    'duration'   => 24,
                ]
            ];

            $snapMidtrans = Snap::createTransaction($params);

            $order->url_midtrans = $snapMidtrans->redirect_url;
            $order->save();

            // Create transactions
            $transaction = TransactionModel::create([
                'order_id' => $orderId,
                'user_id' => Auth::id() ?? null,
                'gross_amount' => $order->original_price,
                'net_amount' => $order->final_price,
                'status' => TransactionStatusEnum::PENDING->value,
                'transaction_time' => Carbon::now(),
                'expiry_time' => Carbon::now()->addHours(24),
                'midtrans_order_id' => $order->order_number,
                'midtrans_token' => $snapMidtrans->token,
                'midtrans_transaction_id' => $snapMidtrans->transaction_id ?? null,
            ]);

            DB::commit();
            return redirect()->to($snapMidtrans->redirect_url);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::info('Create transaction midtrans ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.order.detail');
    }
}
