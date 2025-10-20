<?php

namespace App\Livewire\Payment;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('New Payment')]
class Create extends Component
{
    public function render()
    {
        return view('livewire.payment.create');
    }
}
