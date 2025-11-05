<?php

namespace App\Livewire\Admin;

use App\Models\Qris;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Admin Dashboard')]
#[Layout('components.layouts.admin')]
class DashboardAdmin extends Component
{
    public function mount()
    {
        // Check apakah qris ada yang aktif
        $isQrisActive = Qris::latest('created_at')->first();
        if (!$isQrisActive) {
            LivewireAlert::title("Wah")
                ->text("Segera upload foto qris")
                ->warning()
                ->withConfirmButton()
                ->timer(30000)
                ->show();
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard-admin');
    }
}
