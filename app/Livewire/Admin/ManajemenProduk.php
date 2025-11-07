<?php

namespace App\Livewire\Admin;

use App\Models\CategoryCountryProduct;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
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

    // Properti untuk form
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string')]
    public string $detail = '';

    #[Validate('required|numeric|min:0')]
    public $quota_amount = 0;

    #[Validate('required|numeric|min:0')]
    public $price = 0;

    #[Validate('required|string|in:gb,mb')]
    public string $quota_type = 'gb,mb';

    #[Validate('required|integer|min:0')]
    public $validity_days = 0;

    #[Validate('nullable|numeric|min:0|max:100')]
    public $discount = 0;

    #[Validate('required|boolean')]
    public bool $is_active = true;

    #[Validate('required|exists:category_country_products,id')]
    public $country_id = null;

    public $countries;

    // Digunakan untuk menampilkan detail produk ketika modal edit ditekan.
    public string $nameDetailProduct = '';

    public string $detailProduct = '';

    public $quota_amountDetailProduct = 0;

    public $priceDetailProduct = 0;

    public string $quota_typeDetailProduct = 'reguler';

    public $validity_daysDetailProduct = 0;

    public $discountDetailProduct = 0;

    public bool $is_activeDetailProduct = true;

    public $country_idDetailProduct = null;

    public function mount()
    {
        $this->countries = CategoryCountryProduct::orderBy('name')->get();
        if ($this->countries->isNotEmpty()) {
            $this->country_id = $this->countries->first()->id;
        }
    }

    /**
     * Mendapatkan detail product untuk ditampilkan pada modal edit.
     *
     * @return void
     */
    public function getProduct(int $idProduct)
    {
        if (empty($idProduct)) {
            LivewireAlert::title('Ooops')
                ->text('Something went wrong, when get detail product.')
                ->timer(6000)
                ->error()
                ->toast()
                ->position('top-end')
                ->show();

            return;
        }

        $detailProduct = Product::findOrFail($idProduct);

        // Set properti ...DetailProduct sesuai permintaan
        $this->nameDetailProduct = $detailProduct->name;
        $this->detailProduct = $detailProduct->detail;
        $this->quota_amountDetailProduct = $detailProduct->quota_amount;
        $this->priceDetailProduct = $detailProduct->price;
        $this->quota_typeDetailProduct = $detailProduct->quota_type;
        $this->validity_daysDetailProduct = $detailProduct->validity_days;
        $this->discountDetailProduct = $detailProduct->discount;
        $this->is_activeDetailProduct = $detailProduct->is_active;
        $this->country_idDetailProduct = $detailProduct->country_id;

        $this->editingProduct = $detailProduct;
    }

    /**`
     * Mengupdate data produk yang ada di database.
     * Method ini dipicu dari modal edit dan menggunakan
     * properti '...DetailProduct' untuk validasi dan update.
     *
     * @return void
     */
    public function updateProduct()
    {
        if (! $this->editingProduct) {
            LivewireAlert::title('Error')
                ->text('Tidak ada produk yang dipilih untuk diupdate.')
                ->error()->toast()->position('top-end')->show();

            return;
        }

        $rules = [
            'nameDetailProduct' => 'required|string|max:255',
            'detailProduct' => 'nullable|string',
            'quota_amountDetailProduct' => 'required|numeric|min:0',
            'priceDetailProduct' => 'required|numeric|min:0',
            'quota_typeDetailProduct' => 'required|string|in:gb,mb',
            'validity_daysDetailProduct' => 'required|integer|min:0',
            'discountDetailProduct' => 'nullable|numeric|min:0|max:100',
            'is_activeDetailProduct' => 'required|boolean',
            'country_idDetailProduct' => 'required|exists:category_country_products,id',
        ];

        try {
            $validatedData = $this->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            LivewireAlert::title('Data Tidak Valid')
                ->text('Silakan periksa kembali data yang Anda masukkan.')
                ->error()->toast()->position('top-end')->show();
            throw $e;
        }

        try {
            $this->editingProduct->update([
                'name' => $this->nameDetailProduct,
                'detail' => $this->detailProduct,
                'quota_amount' => $this->quota_amountDetailProduct,
                'price' => $this->priceDetailProduct,
                'quota_type' => $this->quota_typeDetailProduct,
                'validity_days' => $this->validity_daysDetailProduct,
                'discount' => $this->discountDetailProduct,
                'is_active' => $this->is_activeDetailProduct,
                'country_id' => $this->country_idDetailProduct,
            ]);

            LivewireAlert::title('Berhasil')
                ->text('Produk berhasil diperbarui.')
                ->success()->toast()->position('top-end')->show();

            $this->clearForms();
            $this->dispatch('close-modal');

        } catch (\Exception $e) {
            LivewireAlert::title('Gagal Update')
                ->text('Terjadi kesalahan: '.$e->getMessage())
                ->error()->toast()->position('top-end')->show();
        }
    }

    /**
     * Menyimpan produk baru ke database.
     * Method ini dipicu dari modal tambah produk dan menggunakan
     * properti form utama yang memiliki atribut #[Validate].
     *
     * @return void
     */
    public function saveProduct()
    {
        try {
            $validatedData = $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            LivewireAlert::title('Data Tidak Valid')
                ->text('Silakan periksa kembali data yang Anda masukkan.')
                ->error()->toast()->position('top-end')->show();

            throw $e;
        }

        try {
            Product::create([
                'country_id' => $validatedData['country_id'],
                'name' => $validatedData['name'],
                'detail' => $validatedData['detail'],
                'price' => $validatedData['price'],
                'quota_amount' => $validatedData['quota_amount'],
                'quota_type' => $validatedData['quota_type'],
                'validity_days' => $validatedData['validity_days'],
                'discount' => $validatedData['discount'],
                'is_active' => $validatedData['is_active'],
            ]);

            LivewireAlert::title('Berhasil')
                ->text('Produk baru berhasil ditambahkan.')
                ->success()->toast()->position('top-end')->show();

            $this->clearForms();

            $this->dispatch('close-modal');
        } catch (\Exception $e) {
            LivewireAlert::title('Gagal Menyimpan')
                ->text('Terjadi kesalahan: '.$e->getMessage())
                ->error()->toast()->position('top-end')->show();
        }
    }

    /**
     * Membersihkan state form (tambah dan edit)
     * dan mereset error validasi serta state editing.
     *
     * @return void
     */
    public function clearForms()
    {
        // Reset properti form utama (tambah)
        $this->reset(
            'name', 'detail', 'quota_amount', 'price',
            'validity_days', 'discount'
        );

        $this->quota_type = 'gb,mb';
        $this->is_active = true;
        if ($this->countries->isNotEmpty()) {
            $this->country_id = $this->countries->first()->id;
        }

        $this->reset(
            'nameDetailProduct', 'detailProduct', 'quota_amountDetailProduct',
            'priceDetailProduct', 'quota_typeDetailProduct', 'validity_daysDetailProduct',
            'discountDetailProduct', 'is_activeDetailProduct', 'country_idDetailProduct'
        );

        // Reset properti helper
        $this->editingProduct = null;
        $this->productToDelete = null;

        // Bersihkan error validasi
        $this->resetErrorBag();
    }

    public function render()
    {
        $products = Product::with('country')->latest()->paginate($this->perPage);

        return view('livewire.admin.manajemen-produk', [
            'products' => $products,
        ]);
    }
}
