<?php

namespace App\Livewire\Payment;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create Order')]
class Create extends Component
{
    public function render()
    {
        return view('livewire.payment.create');
    }
}
