<?php

namespace App\Livewire\Admin;

use App\Models\HargaSpesial;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Enum\UserSegmentEnum;

#[Title('Manajemen Harga Spesial')]
#[Layout('components.layouts.admin')]
class ManajemenHargaSpesial extends Component
{
    public $discounts = [];

    public function mount()
    {
        foreach (UserSegmentEnum::cases() as $segment) {
            $data = HargaSpesial::where('kategori_harga_spesial', $segment->value)->first();

            $this->discounts[$segment->value] = $data ? $data->potongan_product : 0;
        }
    }

    public function updateHarga($segmentValue)
    {
        $this->validate([
            "discounts.$segmentValue" => 'required|integer|min:0|max:100',
        ], [
            "discounts.$segmentValue.required" => 'Persentase harus diisi.',
            "discounts.$segmentValue.min" => 'Minimal 0%.',
            "discounts.$segmentValue.max" => 'Maksimal 100%.',
        ]);

        HargaSpesial::updateOrCreate(
            ['kategori_harga_spesial' => $segmentValue],
            ['potongan_product' => $this->discounts[$segmentValue]]
        );

        LivewireAlert::title('Berhasil')
            ->text('Potongan harga untuk kategori ini berhasil diperbarui.')
            ->toast()
            ->position('top-end')
            ->success()
            ->show();
    }

    public function render()
    {
        return view('livewire.admin.manajemen-harga-spesial', [
            'segments' => UserSegmentEnum::cases()
        ]);
    }
}
