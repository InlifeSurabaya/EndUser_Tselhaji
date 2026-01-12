<?php

namespace App\Livewire\Product;

use App\Models\CategoryCountryProduct;
use App\Models\HargaSpesial;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\UserPreference;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Enum\UserSegmentEnum;
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

    public $recommendationProducts = [];

    public $specialDiscount = 0;

    public function mount()
    {
        $this->countries = CategoryCountryProduct::select(['id', 'name', 'country_code'])->get();

        if (Auth::check() && Auth::user()->is_new) {
            LivewireAlert::title('Bantu kami mengenali anda')
                ->text('Isi budget dan durasi perjalanan agar kami bisa merekomendasikan paket yang pas!')
                ->success()
                ->withConfirmButton()
                ->onConfirm('goToReferenceUser')
                ->show();
            return;
        } elseif (Auth::check()) {
            $this->getRecommendationProducts();
        }
    }

    public function getRecommendationProducts()
    {
        $user = Auth::user();
        $date = Carbon::now();
        $url  = config('services.api.url_ai'); // URL API Python

        // Query History Transaksi
        $historySatuTahun = Transaction::where('user_id', $user->id)
            ->whereBetween('transaction_time', [$date->copy()->subYear()->startOfYear(), $date->copy()->subYear()->endOfYear()]);

        $historyBulanLalu = Transaction::where('user_id', $user->id)
            ->whereBetween('transaction_time', [$date->copy()->subMonth()->startOfMonth(), $date->copy()->subMonth()->endOfMonth()]);


//        Log::info('QUERY: ' . $historySatuTahun->toRawSql());

        // Default: Anggap User Baru
        $targetSegment = UserSegmentEnum::NEW->value;
        $inputPrice = 0;
        $inputQuota = 0;
        $inputDays  = 0;
        $hasData    = false;

        // 1. LOGIC SEGMENTASI & DATA PREPARATION
        if ($historySatuTahun->count() >= 5) {
            // Kondisi: Loyal / Globetrotter
            $targetSegment = UserSegmentEnum::LOYAL->value;

            $transactions = $historySatuTahun->with('order.product')->get();
            $inputPrice   = $transactions->avg(fn($t) => $t->order->product->price);
            $inputQuota   = $transactions->avg(fn($t) => $t->order->product->quota_amount);
            $inputDays    = $transactions->avg(fn($t) => $t->order->product->validity_days);
            $hasData      = true;

        } elseif ($historyBulanLalu->count() >= 2) {
            // Kondisi: Active / Jetsetter
            $targetSegment = UserSegmentEnum::ACTIVE->value;

            $transactions = $historyBulanLalu->with('order.product')->get();
            $inputPrice   = $transactions->avg(fn($t) => $t->order->product->price);
            $inputQuota   = $transactions->avg(fn($t) => $t->order->product->quota_amount);
            $inputDays    = $transactions->avg(fn($t) => $t->order->product->validity_days);
            $hasData      = true;

        } else {
            // Kondisi: New / Voyager
            $targetSegment = UserSegmentEnum::NEW->value;
            $preference    = UserPreference::where('user_id', $user->id)->latest()->first();

            if ($preference) {
                $inputPrice = $preference->planned_budget;
                $inputQuota = $preference->planned_quota;
                $inputDays  = $preference->planned_duration;
                $hasData    = true;
            }
        }

        Log::info('Target segment: ' . $targetSegment);

        $promo = HargaSpesial::where('kategori_harga_spesial', $targetSegment)->first();
        $this->specialDiscount = $promo ? $promo->potongan_product : 0;

        if ($hasData) {
            try {
                // Endpoint: /recommendation/{price}/{quota}/{day}
                $response = Http::get($url . 'recommendation/' . (int)$inputPrice . '/' . (int)$inputQuota . '/' . (int)$inputDays);

                if ($response->successful()) {
                    $recommendedName = $response->json()['recommended_package'] ?? null;

                    if ($recommendedName) {
                        // Cari produk mirip dengan hasil AI
                        $this->recommendationProducts = Product::where('name', 'LIKE', "%{$recommendedName}%")
                            ->latest()
                            ->take(4)
                            ->get();
                    }
                }
            } catch (\Exception $e) {
                $this->recommendationProducts = [];
            }
        }

        // Fallback jika hasil AI kosong tapi user punya segmen
        if (empty($this->recommendationProducts) && $hasData) {
            $this->recommendationProducts = Product::inRandomOrder()->take(4)->get();
        }
    }

    public function goToReferenceUser()
    {
        $this->redirect(route('user.reference'), navigate: true);
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
    public function newOrder(int $productId, int $discount = 0)
    {
        Session::put('selected_product_id', $productId);
        Session::put('selected_discount', $discount);

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
