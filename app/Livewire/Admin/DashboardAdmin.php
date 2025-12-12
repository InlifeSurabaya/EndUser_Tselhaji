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
        $isQrisActive = Qris::where('is_active', 1)->latest()->first();
        if (!$isQrisActive) {
            LivewireAlert::title('Wah')
                ->text('Segera upload foto qris')
                ->warning()
                ->withConfirmButton()
                ->onConfirm('goToQris')
                ->timer(30000)
                ->show();
        }
    }

    /**
     * Method untuk meneruskan ke halaman qris
     * @return void
     */
    public function goToQris()
    {
        return $this->redirect(route('admin.manajemen-qris'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.dashboard-admin');
    }
}
