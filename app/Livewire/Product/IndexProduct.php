<?php

namespace App\Livewire\Product;

use App\Models\CategoryCountryProduct;
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

    public $countries;

    // Search properti
    public $filterQuotaType;
    public $filterQuotaAmount;
    public $filterCountry;

    public function mount()
    {
        $this->countries = CategoryCountryProduct::select(['id', 'name', 'country_code'])->get();
    }

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
            ->when($this->filterQuotaType, function ($filterQuotaType) {
                return $filterQuotaType->where('quota_type', $this->filterQuotaType);
            })
            ->when($this->filterQuotaAmount, function ($filterQuotaAmount) {
                return $filterQuotaAmount->where('quota_amount', $this->filterQuotaAmount);
            })
            ->when($this->filterCountry, function ($filterCountry) {
                return $filterCountry->where('country_id', $this->filterCountry);
            })
            ->latest()
            ->paginate(8);

        return view('livewire.product.index-product', [
            'products' => $products,
        ]);
    }
}
