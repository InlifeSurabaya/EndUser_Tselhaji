<?php

namespace App\Livewire\Admin;

use App\Models\CategoryCountryProduct;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manajemen Produk')]
#[Layout('components.layouts.admin')]
class ManajemenProduk extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    public $perPage = 10;
    public ?Product $editingProduct = null;
    public ?Product $productToDelete = null;

    // Properti untuk form (tetap sama)
    #[Validate('required|string|max:255')]
    public string $name = '';
    #[Validate('nullable|string')]
    public string $detail = '';
    #[Validate('required|numeric|min:0')]
    public $quota_amount = 0;
    #[Validate('required|numeric|min:0')]
    public $price = 0;
    #[Validate('required|string|in:harian,reguler,unlimited')]
    public string $quota_type = 'reguler';
    #[Validate('required|integer|min:0')]
    public $validity_days = 0;
    #[Validate('nullable|numeric|min:0|max:100')]
    public $discount = 0;
    #[Validate('required|boolean')]
    public bool $is_active = true;
    #[Validate('required|exists:category_country_products,id')]
    public $country_id = null;

    public $countries;

    public function mount()
    {
        $this->countries = CategoryCountryProduct::orderBy('name')->get();
        if ($this->countries->isNotEmpty()) {
            $this->country_id = $this->countries->first()->id;
        }
    }



    public function render()
    {
        $products = Product::with('country')->latest()->paginate($this->perPage);

        return view('livewire.admin.manajemen-produk', [
            'products' => $products
        ]);
    }
}
