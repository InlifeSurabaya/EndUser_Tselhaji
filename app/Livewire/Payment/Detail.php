<?php

namespace App\Livewire\Payment;

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detail Payment')]
class Detail extends Component
{
    public function render()
    {
        return view('livewire.payment.detail');
    }
}
