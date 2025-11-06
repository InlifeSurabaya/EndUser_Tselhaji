<?php

namespace App\Livewire\Order;

use App\Enum\TransactionStatusEnum;
use App\Models\Order;
use App\Models\Qris;
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
    public $qris;

    public function mount(string $uuidOrder): void
    {
        $this->order = Order::with([
            'voucher',
            'product',
            'user'
        ])
            ->where('uuid', $uuidOrder)
            ->firstOrFail();

        // Load qris admin
        $this->qris = Qris::where('is_active', 1)->latest('created_at')->first();
    }



    public function render()
    {
        return view('livewire.order.detail');
    }
}
