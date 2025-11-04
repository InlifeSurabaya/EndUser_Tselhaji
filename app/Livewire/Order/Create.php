<?php

namespace App\Livewire\Order;

use App\Enum\DiscountTypeEnum;
use App\Enum\OrderStatusEnum;
use App\Models\Order;
use App\Models\Product;
use App\Models\Voucher;
use App\Traits\LogsDeveloper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('New Order')]
class Create extends Component
{

    use LogsDeveloper;

    public $productId;
    public Product $product;
    public $notes;

    public $voucher;
    public ?Voucher $voucherModel = null;
    public $productDiscount = 0;
    public $voucherDiscount = 0;
    public $finalPrice = 0;
    public $guestEmail;
    public $phoneNumber;

    public function mount(): void
    {
        $this->productId = Session::get('selected_product_id');

        if (empty($this->productId)) {
            LivewireAlert::title('Oops')
                ->text('Product tidak ditemukan.')
                ->error()
                ->timer(4000)
                ->show();
            return;
        }

        $user = Auth::user();

        // Init phone number
        if (Auth::check() && $user->userProfile && $user->userProfile->phone != null) {
            $this->phoneNumber = Auth::user()->userProfile->phone;
        }
        $this->loadProduct();
    }

    /**
     * Load product ketika halaman pertama kali dibuka.
     */
    public function loadProduct()
    {
        try {
            $this->product = Product::with('country')->findOrFail($this->productId);
            // Hitung harga awal setelah produk di-load
            $this->calculatePrices();
        } catch (\Throwable $e) {
            LivewireAlert::title('Oops')
                ->text('Silakan pilih produk terlebih dahulu.')
                ->error()
                ->timer(4000)
                ->show();
        }
    }

    /**
     * Fungsi utama untuk menghitung semua diskon dan harga final.
     * Akan dipanggil saat load dan saat voucher diubah.
     */
    public function calculatePrices()
    {
        if (empty($this->product)) {
            return;
        }

        $originalPrice = $this->product->price;
        Log::info('Original price: ' . $originalPrice);
        $this->productDiscount = 0;
        $this->voucherDiscount = 0;

        // 1. Hitung Diskon Produk
        if ($this->product->discount > 0) {
            Log::info('Hitung diskon product');
            $this->productDiscount = ($originalPrice * $this->product->discount) / 100;
            Log::info('Hitung diskon product end ' . $this->productDiscount);
        }

        // Harga setelah diskon produk
        $priceAfterProductDiscount = $originalPrice - $this->productDiscount;
        Log::info('Harga after product discount: ' . $priceAfterProductDiscount);

        // 2. Hitung Diskon Voucher (jika ada voucher valid)
        if ($this->voucherModel) {
            Log::info('Hitung voucher product');
            if ($this->voucherModel->discount_type === DiscountTypeEnum::PERCENTEAGE->value) {
                Log::info('Hitung voucher product percent');
                $this->voucherDiscount = $priceAfterProductDiscount * ($this->voucherModel->discount_value / 100);
                Log::info('Hitung voucher product percent end ' . $this->voucherDiscount);
            } elseif ($this->voucherModel->discount_type === DiscountTypeEnum::FIXED->value) {
                Log::info('Hitung voucher product fixed');
                $this->voucherDiscount = $this->voucherModel->discount_value;
                Log::info('Hitung voucher product fixed end ' . $this->voucherDiscount);
            }
            $this->voucherDiscount = min($this->voucherDiscount, $priceAfterProductDiscount);
            Log::info('Hitung voucher product end kondisi ' . $this->voucherDiscount);
            Log::info('Harga voucher product end kondisi price after product ' . $priceAfterProductDiscount);

        }

        $this->finalPrice = $priceAfterProductDiscount - $this->voucherDiscount;
        Log::info('Final price: ' . $this->finalPrice);
    }

    /**
     * Memvalidasi voucher dan menghitung ulang harga.
     */
    public function checkVoucher()
    {
        DB::beginTransaction();
        try {
            if (empty($this->voucher)) {
                $this->voucherModel = null;
                $this->calculatePrices(); // Hitung ulang tanpa voucher
                LivewireAlert::title('Oops!')
                    ->text('Kamu belum memasukkan kode voucher.')
                    ->error()
                    ->timer(3000)
                    ->show();
                return;
            }

            $voucher = Voucher::where('code', $this->voucher)->first();

            // Validasi 1: Apa voucher ada?
            if (!$voucher) {
                $this->voucherModel = null;
                $this->calculatePrices();
                LivewireAlert::title('Oops! Voucher Nggak Ketemu')
                    ->text('Kode voucher sepertinya salah. Coba cek lagi, ya!')
                    ->error()
                    ->timer(4000)
                    ->show();
                return;
            }

            // Validasi 2: Apa voucher aktif?
            if (!$voucher->is_active) {
                $this->voucherModel = null;
                $this->calculatePrices();
                LivewireAlert::title('Yah, Gagal')
                    ->text('Voucher ini sudah tidak aktif lagi.')
                    ->error()
                    ->timer(4000)
                    ->show();
                return;
            }

            // Validasi 3: Apa voucher sudah kedaluwarsa?
            if ($voucher->end_date && Carbon::parse($voucher->end_date)->isPast()) {
                $this->voucherModel = null;
                $this->calculatePrices();
                LivewireAlert::title('Yah, Kedaluwarsa')
                    ->text('Voucher ini sudah melewati batas waktu penggunaan.')
                    ->error()
                    ->timer(4000)
                    ->show();
                return;
            }

            // Validasi 4: Apa voucher sudah mencapai limit?
            if ($voucher->usage_limit > 0 && $voucher->used_count >= $voucher->usage_limit) {
                $this->voucherModel = null;
                $this->calculatePrices();
                LivewireAlert::title('Yah, Kehabisan')
                    ->text('Limit penggunaan voucher ini sudah habis.')
                    ->error()
                    ->timer(4000)
                    ->show();
                return;
            }


            $this->voucherModel = $voucher;
            $this->calculatePrices();

            LivewireAlert::title('Asyik! Voucher Berhasil 🎉')
                ->text('Mantap, diskon voucher berhasil diterapkan.')
                ->success()
                ->timer(4000)
                ->show();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

        }
    }


    /**
     * Membuat order baru
     */
    public function createOrder()
    {
        // Pastikan harga sudah ter-update (untuk jaga-jaga)
        $this->calculatePrices();
        DB::beginTransaction();
        $user = Auth::user();

        try {
            if (!$user) {
                $this->validate([
                    'guestEmail' => 'email|required',
                    'phoneNumber' => 'numeric|required',
                ]);
            }

            // Create new order
            $newOrder = Order::create([
                'user_id' => $user->id ?? null,
                'product_id' => $this->product->id,
                'voucher_id' => $this->voucherModel?->id,
                'category_country_product_id' => $this->product->country->id,
                'original_price' => $this->product->price,
                'discount_amount' => $this->productDiscount + $this->voucherDiscount, // Total diskon
                'final_price' => $this->finalPrice,
                'status' => OrderStatusEnum::PENDING->value,
                'customer_name' => $user?->userProfile()?->fullname ?? null,
                'customer_email' => $user?->email ?? $this->guestEmail,
                'customer_phone' => $this->phoneNumber,
                'notes' => $this->notes ?? null,
                'expired_at' => Carbon::now()->copy()->addHours(24)
            ]);

            // Check apakah user memasukan voucher
            if ($this->voucherModel) {
                // Update used count
                $this->voucherModel->used_count = $this->voucherModel?->used_count + 1;
                $this->voucherModel?->save();
            }

            DB::commit();
            return $this->redirect(route('order.detail', ['uuidOrder' => $newOrder->uuid]), navigate: true);

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->logErrorForDeveloper($e, [
                'context' => 'Gagal saat membuat order',
                'product_id' => $this->productId ?? 'Not Set',
                'voucher_input' => $this->voucher ?? 'Not Set'
            ]);

            LivewireAlert::title('Terjadi Kesalahan')
                ->text($e->getMessage())
                ->error()
                ->show();
        }
    }

    public function render()
    {
        return view('livewire.order.create');
    }
}
