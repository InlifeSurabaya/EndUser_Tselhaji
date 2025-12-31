<?php

namespace App\Livewire\Order;

use App\Enum\OrderStatusEnum;
use App\Enum\TransactionStatusEnum;
use App\Models\Order;
use App\Models\Qris;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Detail Order')]
class Detail extends Component
{
    use WithFileUploads;

    public $order;

    public $qris;

    #[Validate('required|image|mimes:jpeg,png,jpg,gif,svg|max:5120')]
    public $buktiPembayaran;

    public function mount(string $uuidOrder): void
    {
        $this->order = Order::with([
            'voucher',
            'product',
            'user',
            'transaction'
        ])
            ->where('uuid', $uuidOrder)
            ->firstOrFail();

        // Load qris admin
        $this->qris = Qris::where('is_active', 1)->latest('created_at')->first();
    }


    public function createTransaction()
    {
        // Validate
        $this->validate();
        try {
            // Save image
            $path = $this->buktiPembayaran->store('bukti-pembayaran', 'public');

            // Create transaaction
            $transaction = Transaction::create([
                'order_id' => $this->order->id,
                'user_id' => $this->order->user->id ?? null,
                'gross_amount' => $this->order->original_price,
                'net_amount' => $this->order->final_price,
                'status' => TransactionStatusEnum::PROSES->value,
                'transaction_time' => Carbon::now(),
                'settlement_time' => Carbon::now(),
                'payment_proof' => $path,
            ]);

            $this->order->status = OrderStatusEnum::PROSES->value;
            $this->order->settlement_time = Carbon::now();
            $this->order->save();

            LivewireAlert::title('Success')
                ->text('Bukti pembayaran telah dikirim!, tunggu konfirmasi selanjutnya.')
                ->success()
                ->timer(60000)
                ->withConfirmButton()
                ->show();

        } catch (ValidationException $e) {
            LivewireAlert::title('Error')
                ->text($e->getMessage())
                ->error()
                ->timer(60000)
                ->toast()
                ->show();
        }
        catch (\Throwable $e) {
            Log::info('Error when create transaction in detail order ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.order.detail');
    }
}
