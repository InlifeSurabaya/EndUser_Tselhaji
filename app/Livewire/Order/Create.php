<?php

namespace App\Livewire\Order;

use App\Enum\DiscountTypeEnum;
use App\Enum\OrderStatusEnum;
use App\Models\Order;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\Transaction;
use App\Traits\LogsDeveloper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('New Order')]
class Create extends Component
{
    use LogsDeveloper;

    public $availableVouchers;
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
    public $discountSegmentUser = 0; // Persentase (misal: 10)
    public $segmentDiscountAmount = 0; // Nominal (misal: 5000)


    // Tambahkan property untuk payment
    public $paymentType = 'transfer'; // transfer, qris, cash, etc

    public function mount(): void
    {
        $this->productId = Session::get('selected_product_id');
        $this->discountSegmentUser = Session::get('selected_discount');

        Log::info('create ' . $this->productId);
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
        // Load voucher
        $this->availableVouchers = Voucher::where('is_active', 1)
            ->where('user_can_see', 1)
            ->whereColumn('used_count', '<', 'usage_limit')
            ->where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->select(['id', 'code', 'discount_value', 'discount_type'])
            ->get();

        // Load product
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

        if ($this->product->discount > 0) {
            Log::info('Hitung diskon product');
            $this->productDiscount = ($originalPrice * $this->product->discount) / 100;
            Log::info('Hitung diskon product end ' . $this->productDiscount);
        }

        $runningPrice = $originalPrice - $this->productDiscount;

        if ($this->discountSegmentUser > 0) {
            $this->segmentDiscountAmount = $runningPrice * ($this->discountSegmentUser / 100);

            $runningPrice = $runningPrice - $this->segmentDiscountAmount;
        }

        if ($this->voucherModel) {
            $calculatedVoucherDiscount = 0;

            if ($this->voucherModel->discount_type === DiscountTypeEnum::PERCENTEAGE->value) {
                $calculatedVoucherDiscount = $runningPrice * ($this->voucherModel->discount_value / 100);
            } elseif ($this->voucherModel->discount_type === DiscountTypeEnum::FIXED->value) {
                $calculatedVoucherDiscount = $this->voucherModel->discount_value;
            }

            $this->voucherDiscount = min($calculatedVoucherDiscount, $runningPrice);

            $runningPrice = $runningPrice - $this->voucherDiscount;
        }

        $this->finalPrice = max(0, $runningPrice);
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
     * Generate unique transaction number
     */
    private function generateTransactionNumber()
    {
        return 'TRX-' . time() . '-' . strtoupper(substr(uniqid(), -8));
    }

    /**
     * Create transaction di Laravel B
     */
    /**
 * Create transaction di Laravel B
 */
private function createTransactionInLaravelB($transactionNumber, $grossAmount, $orderId = 0)
{
    try {
        $laravelBUrl = env('LARAVEL_B_URL', 'http://localhost:8000');

        Log::info('Creating transaction via UAS API', [
            'url' => $laravelBUrl . '/api/transaksi/uas-guaranteed',
            'transaction_number' => $transactionNumber,
            'amount' => $grossAmount
        ]);

        // Gunakan endpoint UAS yang sudah terbukti berhasil
        $response = Http::timeout(30)
            ->post("{$laravelBUrl}/api/transaksi/uas-guaranteed", [
                'transaction_number' => $transactionNumber,
                'gross_amount' => $grossAmount,
                'payment_type' => $this->paymentType,
                'status' => 'pending',
                'customer_phone' => $this->phoneNumber,
                'customer_email' => $this->guestEmail ?? (Auth::check() ? Auth::user()->email : null),
                'order_id' => $orderId,
                'notes' => 'Order dari Laravel A Livewire',
                'product_name' => $this->product->name ?? 'Produk Digital'
            ]);

        if ($response->successful()) {
            $responseData = $response->json();
            Log::info('Transaction created via UAS API', [
                'transaction_number' => $transactionNumber,
                'response' => $responseData
            ]);

            return [
                'success' => true,
                'data' => $responseData
            ];
        } else {
            Log::warning('UAS API responded with non-success', [
                'transaction_number' => $transactionNumber,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            // Fallback ke simulation endpoint
            return $this->createTransactionFallback($transactionNumber, $grossAmount);
        }
    } catch (\Exception $e) {
        Log::error('Exception creating transaction via UAS API', [
            'transaction_number' => $transactionNumber,
            'error' => $e->getMessage()
        ]);

        // Coba fallback
        return $this->createTransactionFallback($transactionNumber, $grossAmount);
    }
}

/**
 * Fallback menggunakan simulation endpoint
 */
private function createTransactionFallback($transactionNumber, $grossAmount)
{
    try {
        $laravelBUrl = env('LARAVEL_B_URL', 'http://localhost:8000');

        Log::info('Trying fallback to simulation endpoint');

        $response = Http::timeout(30)
            ->post("{$laravelBUrl}/api/transaksi/uas-simulation", [
                'transaction_number' => $transactionNumber,
                'gross_amount' => $grossAmount,
                'payment_type' => $this->paymentType,
                'customer_phone' => $this->phoneNumber
            ]);

        if ($response->successful()) {
            $responseData = $response->json();
            Log::info('Transaction created via simulation endpoint', [
                'transaction_number' => $transactionNumber,
                'response' => $responseData
            ]);

            return [
                'success' => true,
                'data' => $responseData,
                'note' => 'Created via simulation (fallback)'
            ];
        } else {
            Log::error('Fallback also failed', [
                'transaction_number' => $transactionNumber,
                'status' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => 'All endpoints failed'
            ];
        }
    } catch (\Exception $e) {
        Log::error('Fallback creation failed', [
            'transaction_number' => $transactionNumber,
            'error' => $e->getMessage()
        ]);

        return [
            'success' => false,
            'error' => 'Fallback failed: ' . $e->getMessage()
        ];
    }
}

    /**
     * Create transaction di database lokal (Laravel A)
     */
    private function createLocalTransaction($orderId, $transactionNumber)
    {
        try {
            // Buat transaction di Laravel A
            $transaction = Transaction::create([
                'transaction_number' => $transactionNumber,
                'order_id' => $orderId,
                'user_id' => Auth::id() ?? 1,
                'gross_amount' => $this->finalPrice,
                'net_amount' => $this->finalPrice,
                'payment_type' => $this->paymentType,
                'status' => 'pending',
                'transaction_time' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Local transaction created', [
                'transaction_id' => $transaction->id,
                'transaction_number' => $transactionNumber,
                'order_id' => $orderId
            ]);

            return $transaction;
        } catch (\Exception $e) {
            Log::error('Failed to create local transaction', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Sync order ke Laravel B (optional)
     */
    private function syncOrderToLaravelB($order)
    {
        try {
            $laravelBUrl = env('LARAVEL_B_URL', 'http://localhost:8000');

            $orderData = [
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name ?? 'Customer',
                'customer_email' => $order->customer_email,
                'customer_phone' => $order->customer_phone,
                'product_name' => $this->product->name,
                'product_price' => $this->finalPrice,
                'final_price' => $this->finalPrice,
                'status' => 'pending',
                'created_at' => $order->created_at->toDateTimeString(),
                'notes' => $order->notes ?? 'Order from Laravel A',
                'sync_id' => 'sync-' . uniqid()
            ];

            $response = Http::timeout(30)
                ->post("{$laravelBUrl}/api/orders/sync", $orderData);

            if ($response->successful()) {
                Log::info('Order synced to Laravel B', [
                    'order_number' => $order->order_number,
                    'response' => $response->json()
                ]);
            } else {
                Log::warning('Failed to sync order to Laravel B', [
                    'order_number' => $order->order_number,
                    'status' => $response->status()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception syncing order to Laravel B', [
                'order_number' => $order->order_number ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Membuat order baru dengan transaction - SIMPLE VERSION
     */
 /**
 * Membuat order baru - Sederhana dan mengikuti alur curl
 */
public function createOrder()
{
    // Validasi
    $user = Auth::user();
    if (!$user) {
        $this->validate([
            'guestEmail' => 'email|required',
            'phoneNumber' => 'numeric|required|min:10',
        ]);
    } else {
        $this->validate([
            'phoneNumber' => 'numeric|required|min:10',
        ]);
    }

    DB::beginTransaction();
    try {
        // Pastikan harga sudah ter-update
        $this->calculatePrices();

        Log::info('=== CREATE ORDER PROCESS STARTED ===');
        Log::info('Product: ' . ($this->product->name ?? 'N/A'));
        Log::info('Final Price: ' . $this->finalPrice);
        Log::info('Phone: ' . $this->phoneNumber);
        Log::info('Payment Type: ' . $this->paymentType);

        // 1. Generate transaction number
        $transactionNumber = 'TRX-' . time() . '-' . rand(100, 999);
        Log::info('Generated transaction number: ' . $transactionNumber);

        // 2. Create transaction via UAS API (Laravel B)
        Log::info('Calling UAS API...');
        $apiResponse = $this->createTransactionInLaravelB(
            $transactionNumber,
            $this->finalPrice,
            0
        );

        if (!$apiResponse['success']) {
            Log::error('API call failed, creating order locally only');

            // Still create order locally but mark as warning
            $newOrder = $this->createLocalOrderOnly($transactionNumber);

            LivewireAlert::title('Order Dibuat dengan Catatan')
                ->html('Order berhasil dibuat di sistem kami.<br>Sistem pembayaran eksternal sedang maintenance.')
                ->warning()
                ->timer(5000)
                ->show();

            return $this->redirectToOrderDetail($newOrder);
        }

        Log::info('API call successful, proceeding with order creation');

        // 3. Create new order di Laravel A
        $newOrder = Order::create([
            'user_id' => $user->id ?? null,
            'product_id' => $this->product->id,
            'voucher_id' => $this->voucherModel?->id,
            'category_country_product_id' => $this->product->country->id,
            'original_price' => $this->product->price,
            'discount_amount' => $this->productDiscount + $this->voucherDiscount,
            'final_price' => $this->finalPrice,
            'status' => OrderStatusEnum::PENDING->value,
            'customer_name' => $user?->userProfile()?->fullname ?? 'Customer',
            'customer_email' => $user?->email ?? $this->guestEmail,
            'customer_phone' => $this->phoneNumber,
            'notes' => $this->notes ?? 'Created via UAS API integration',
            'expired_at' => Carbon::now()->copy()->addHours(24),
            'order_number' => $transactionNumber,
            'payment_reference' => $apiResponse['data']['transaction'] ?? $transactionNumber,
        ]);

        Log::info('Order created in database', ['order_id' => $newOrder->id]);

        // 4. Update voucher jika digunakan
        if ($this->voucherModel) {
            $this->voucherModel->increment('used_count');
            Log::info('Voucher updated', ['voucher_id' => $this->voucherModel->id]);
        }

        // 5. Create local transaction record
        $this->createLocalTransaction($newOrder->id, $transactionNumber);
        Log::info('Local transaction record created');

        DB::commit();
        Log::info('=== CREATE ORDER PROCESS COMPLETED ===');

        // Simpan data ke session
        Session::put('last_transaction_number', $transactionNumber);
        Session::put('last_order_id', $newOrder->id);

        LivewireAlert::title('✅ Order Berhasil Dibuat!')
            ->text('Silakan lanjutkan ke halaman detail untuk informasi pembayaran.')
            ->success()
            ->timer(4000)
            ->show();

        return $this->redirectToOrderDetail($newOrder);

    } catch (ValidationException $e) {
        DB::rollBack();
        Log::error('Validation error', ['errors' => $e->errors()]);

        LivewireAlert::title('Validasi Gagal')
            ->text(implode(', ', array_flatten($e->errors())))
            ->error()
            ->show();

    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Create order failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        LivewireAlert::title('Terjadi Kesalahan')
            ->text('Silakan coba lagi atau hubungi admin: ' . $e->getMessage())
            ->error()
            ->show();
    }
}

/**
 * Create local order only (without API call)
 */
private function createLocalOrderOnly($transactionNumber)
{
    $user = Auth::user();

    return Order::create([
        'user_id' => $user->id ?? null,
        'product_id' => $this->product->id,
        'voucher_id' => $this->voucherModel?->id,
        'category_country_product_id' => $this->product->country->id,
        'original_price' => $this->product->price,
        'discount_amount' => $this->productDiscount + $this->voucherDiscount,
        'final_price' => $this->finalPrice,
        'status' => OrderStatusEnum::PENDING->value,
        'customer_name' => $user?->userProfile()?->fullname ?? 'Customer',
        'customer_email' => $user?->email ?? $this->guestEmail,
        'customer_phone' => $this->phoneNumber,
        'notes' => ($this->notes ?? '') . ' [API-UNAVAILABLE]',
        'expired_at' => Carbon::now()->copy()->addHours(24),
        'order_number' => $transactionNumber,
        'payment_reference' => $transactionNumber,
    ]);
}

/**
 * Redirect to order detail
 */
private function redirectToOrderDetail($order)
{
    return $this->redirect(route('order.detail', ['uuidOrder' => $order->uuid]), navigate: true);
}
    /**
     * Update payment type
     */
    public function updatedPaymentType($value)
    {
        $this->paymentType = $value;
        Log::info('Payment type updated to: ' . $value);
    }

    public function render()
    {
        return view('livewire.order.create', [
            'paymentTypes' => [
                'transfer' => 'Transfer Bank',
                'qris' => 'QRIS',
                'cash' => 'Cash',
                'virtual_account' => 'Virtual Account',
                'ewallet' => 'E-Wallet',
            ]
        ]);
    }
    /**
 * Test UAS API directly
 */
public function testUasApi()
{
    try {
        $this->calculatePrices();
        $transactionNumber = 'TEST-' . time();

        Log::info('Testing UAS API directly...');

        $response = Http::post('http://localhost:8000/api/transaksi/uas-guaranteed', [
            'transaction_number' => $transactionNumber,
            'gross_amount' => $this->finalPrice,
            'payment_type' => 'qris',
            'customer_phone' => '08123456789',
            'product_name' => 'Test Product'
        ]);

        if ($response->successful()) {
            $data = $response->json();
            LivewireAlert::title('✅ API Test Successful')
                ->text('Response: ' . ($data['message'] ?? 'Success'))
                ->success()
                ->timer(4000)
                ->show();

            Log::info('API Test Success', $data);
        } else {
            LivewireAlert::title('❌ API Test Failed')
                ->text('Status: ' . $response->status())
                ->error()
                ->timer(4000)
                ->show();
        }

    } catch (\Exception $e) {
        LivewireAlert::title('⚠️ API Test Error')
            ->text('Error: ' . $e->getMessage())
            ->warning()
            ->timer(4000)
            ->show();
    }
}
}
