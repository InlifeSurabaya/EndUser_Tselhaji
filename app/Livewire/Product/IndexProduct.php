<?php

namespace App\Livewire\Product;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Session;
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
     * Navigate user ke create order
     *
     * @return null
     */
    public function newOrder(int $productId)
    {
        Session::put('selected_product_id', $productId);

        return $this->redirect(route('order.create'), navigate: true);
    }

    public function render()
    {
        $products = Product::with('country:id,name,country_code')
            ->where('is_active', 1)
            ->latest()
            ->paginate(8);

        return view('livewire.product.index-product', [
            'products' => $products,
        ]);
    }
}
