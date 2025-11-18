<?php

namespace App\Livewire\Admin;

use App\Models\Qris;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Manajemen Qris')]
#[Layout('components.layouts.admin')]
class ManajemenQris extends Component
{
    use WithFileUploads;

    #[Validate('image|max:5120')]
    public $newQris;

    public $currentQris;

    public function mount()
    {
        $this->currentQris = Qris::where('is_active', 1)->latest()->first();
    }

    public function clearPreview()
    {
        $this->reset('newQris');
        $this->dispatch('clear-file-input');
    }

    // Fungsi untuk membuat QRIS baru.
    public function save()
    {
        $this->validateOnly('newQris');

        $path = $this->newQris->store('qris_image', 'public');

        // Check qris sebelumnya, kalau ada nonaktifkan dulu
        Qris::where('is_active', 1)->update(['is_active' => 0]);

        $newQris = Qris::create([
            'file' => $path,
            'is_active' => 1,
        ]);

        $this->currentQris = $newQris;

        $this->reset('newQris');

        LivewireAlert::title('Success')
            ->text('Berhasil membuat QRIS!')
            ->success()
            ->toast()
            ->position('top-end')
            ->timer(5000)
            ->show();
    }

    public function render()
    {
        return view('livewire.admin.manajemen-qris');
    }
}
