<?php

namespace App\Livewire\Admin;

use App\Models\CategoryCountryProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\Qris;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Admin Dashboard')]
#[Layout('components.layouts.admin')]
class DashboardAdmin extends Component
{
    // Properties untuk menyimpan data dashboard
    public $totalUsers, $newUsersToday;
    public $totalProducts, $activeProducts;
    public $totalOrders, $ordersThisMonth;
    public $totalRevenue, $transactionsCount;
    
    public $recentOrders;
    public $topProducts;
    public $countryStats;
    
    public $isQrisActive;
    public $activeVouchersCount;
    
    public $chartLabels = [];
    public $chartData = [];

    public function mount()
    {
        $this->checkQris();
        $this->loadStatistics();
        $this->loadCharts();
        $this->loadTables();
    }

    private function checkQris()
    {
        // Check apakah qris ada yang aktif (Sesuai logic awal kamu)
        $this->isQrisActive = Qris::where('is_active', 1)->exists();
        
        if (!$this->isQrisActive) {
            LivewireAlert::title('Perhatian')
                ->text('QRIS belum aktif. Segera upload foto QRIS agar transaksi berjalan.')
                ->warning()
                ->withConfirmButton('Upload Sekarang')
                ->onConfirm('goToQris')
                ->timer(30000)
                ->show();
        }
    }

    private function loadStatistics()
    {
        // 1. User Stats
        $this->totalUsers = User::count();
        $this->newUsersToday = User::whereDate('created_at', today())->count();

        // 2. Product Stats
        $this->totalProducts = Product::count();
        $this->activeProducts = Product::where('is_active', 1)->count();

        // 3. Order Stats
        $this->totalOrders = Order::count();
        $this->ordersThisMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // 4. Revenue Stats (Mengambil dari Transaction yang sukses/settlement)
        // Asumsi status sukses midtrans adalah 'settlement' atau 'capture'
        $this->totalRevenue = Transaction::whereIn('status', ['settlement', 'capture', 'success'])
            ->sum('gross_amount');
        
        $this->transactionsCount = Transaction::whereIn('status', ['settlement', 'capture', 'success'])
            ->count();

        // 5. Voucher & QRIS
        $this->activeVouchersCount = Voucher::where('is_active', 1)
            ->whereDate('end_date', '>=', now())
            ->count();
    }

    private function loadCharts()
    {
        // Grafik Transaksi Bulanan Tahun Ini
        $monthlyTransactions = Transaction::select(
            DB::raw('SUM(gross_amount) as total'),
            DB::raw('MONTH(created_at) as month')
        )
        ->whereYear('created_at', date('Y'))
        ->whereIn('status', ['settlement', 'capture', 'success'])
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

        // Fill data 1-12 bulan (isi 0 jika tidak ada data)
        for ($i = 1; $i <= 12; $i++) {
            $this->chartLabels[] = date('F', mktime(0, 0, 0, $i, 1)); // Nama Bulan
            $this->chartData[] = $monthlyTransactions[$i] ?? 0;
        }
    }

    private function loadTables()
    {
        // Order Terbaru (5 Data)
        $this->recentOrders = Order::with(['user', 'product'])
            ->latest()
            ->take(5)
            ->get();

        // Produk Terlaris (Top 3)
        // Mengelompokkan berdasarkan product_id di tabel orders
        $this->topProducts = Order::select('product_id', DB::raw('count(*) as total_sold'))
            ->with(['product.country']) // Eager load product dan relasi negaranya
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(3)
            ->get();

        // Statistik Kategori Negara (Berapa produk per negara/kategori)
        $this->countryStats = CategoryCountryProduct::withCount('product')
            ->orderByDesc('product_count')
            ->take(3)
            ->get();
    }

    public function goToQris()
    {
        return $this->redirect(route('admin.manajemen-qris'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.dashboard-admin');
    }
}