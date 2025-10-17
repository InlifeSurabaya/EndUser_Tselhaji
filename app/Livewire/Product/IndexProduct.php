<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('List Paket')]
class IndexProduct extends Component
{
    use WithPagination;

    public ?Product $selectedProduct = null;
    public bool $showModal = false;

    /**
     * Menampilkan detail produk di modal
     */
    public function showProductDetail(int $productId): void
    {
        $this->selectedProduct = Product::with('country')->findOrFail($productId);
        $this->showModal = true;
    }

    /**
     * Proses pembelian produk
     */
    public function buyNow(int $productId): void
    {
        $product = Product::find($productId);

        // Logika bisnis pembelian di sini
        session()->flash('message', 'Anda telah berhasil membeli paket: ' . $product->name);

        // Tutup modal dan reset state
        $this->showModal = false;
        $this->selectedProduct = null;
    }

    public function render()
    {
        $products = Product::with('country:id,name')
            ->where('is_active', 1)
            ->latest()
            ->paginate(8);
        return view('livewire.product.index-product', [
            'products' => $products
        ]);
    }
}
