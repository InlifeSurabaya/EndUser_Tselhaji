<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manajemen Qris')]
#[Layout('components.layouts.admin')]
class ManajemenQris extends Component
{
    public function render()
    {
        return view('livewire.admin.manajemen-qris');
    }
}
