<?php

namespace App\Livewire\Order;

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Cek Pesanan')]
class CheckOrder extends Component
{
    public $orderNumber;
    public $email;
    public $data;

    // Properti baru untuk melacak apakah pencarian sudah dilakukan
    public $searched = false;

    /**
     * Cari order number di database.
     */
    public function searchOrderNumber()
    {
        $this->validate([
            'orderNumber' => 'required',
            'email' => 'required|email',
        ]);

        // Tandai bahwa pencarian telah dilakukan
        $this->searched = true;

        // Cari data
        $this->data = Order::where('order_number', $this->orderNumber)
            ->where('customer_email', $this->email)
            ->first();
    }

    public function render()
    {
        return view('livewire.order.check-order');
    }
}
