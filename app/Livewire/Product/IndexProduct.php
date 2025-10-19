<?php

namespace App\Livewire\Product;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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



    public function render()
    {
        $products = Product::with('country:id,name,country_code')
            ->where('is_active', 1)
            ->latest()
            ->paginate(8);
        return view('livewire.product.index-product', [
            'products' => $products
        ]);
    }
}
