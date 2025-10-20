<?php

namespace App\Livewire\Order;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Component;

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

    public function render()
    {
        return view('livewire.order.detail');
    }
}
