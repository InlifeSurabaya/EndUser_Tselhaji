<?php

namespace App\Livewire\Order;

use App\Models\Order;
use App\Models\Qris;
use Livewire\Attributes\Title;
use Livewire\Component;

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
            'user',
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
