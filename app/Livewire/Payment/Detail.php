<?php

namespace App\Livewire\Payment;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Order Detail')]
class Detail extends Component
{
    public function render()
    {
        return view('livewire.payment.detail');
    }
}
