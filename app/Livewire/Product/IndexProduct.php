<?php

namespace App\Livewire\Product;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('List Paket')]
class IndexProduct extends Component
{
    public function render()
    {
        return view('livewire.product.index-product');
    }
}
