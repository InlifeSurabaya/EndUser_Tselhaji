<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Admin Dashboard')]
#[Layout('components.layouts.admin')]
class DashboardAdmin extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard-admin');
    }
}
