<div class="p-6 mb-10 bg-white">

    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-neutral-800">Dashboard Overview</h1>
        <p class="text-sm text-gray-500">Monitor kinerja penjualan dan pengguna Anda.</p>
    </div>

    {{-- Card info --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total User</p>
                    <h2 class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalUsers) }}</h2>
                </div>
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                @if($newUsersToday > 0)
                <span class="text-green-600 bg-green-50 px-2 py-0.5 rounded-full font-medium flex items-center gap-1">
                    +{{ $newUsersToday }}
                </span>
                <span class="text-gray-400 ml-2">hari ini</span>
                @else
                <span class="text-gray-400">Belum ada user baru hari ini</span>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Produk</p>
                    <h2 class="text-3xl font-bold text-gray-900 mt-2">{{ $totalProducts }}</h2>
                </div>
                <div class="p-2 bg-purple-50 rounded-lg text-purple-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full font-medium">{{ $activeProducts }}
                    Aktif</span>
                <span class="text-gray-400 ml-2">di katalog</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Order</p>
                    <h2 class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalOrders) }}</h2>
                </div>
                <div class="p-2 bg-orange-50 rounded-lg text-orange-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full font-medium">+{{ $ordersThisMonth
                    }}</span>
                <span class="text-gray-400 ml-2">bulan ini</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pendapatan</p>
                    <h2 class="text-2xl font-bold text-gray-900 mt-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </h2>
                </div>
                <div class="p-2 bg-green-50 rounded-lg text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 bg-green-50 px-2 py-0.5 rounded-full font-medium">{{ $transactionsCount
                    }}</span>
                <span class="text-gray-400 ml-2">transaksi berhasil</span>
            </div>
        </div>

    </div>

    {{-- Order Terbaru & Produk Terlaris --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

        {{-- Statistik chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-neutral-800">Statistik Pendapatan ({{ date('Y') }})</h3>
            </div>

            <div class="relative h-72 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-neutral-800">Order Terbaru</h3>
                <a wire:navigate href="{{ route('admin.manajemen-pesanan') }}"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs text-gray-500 uppercase tracking-wider border-b border-gray-100">
                            <th class="py-3 font-medium">Order ID</th>
                            {{-- <th class="py-3 font-medium">Customer</th>
                            <th class="py-3 font-medium">Produk</th>
                            <th class="py-3 font-medium">Total</th> --}}
                            <th class="py-3 font-medium text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-50">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="py-3 font-medium text-gray-900 group-hover:text-blue-600">
                                #{{ $order->order_number }}
                            </td>
                            {{-- <td class="py-3 text-gray-600">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 uppercase">
                                        {{ substr($order->customer_name ?? 'G', 0, 1) }}
                                    </div>
                                    <span class="truncate max-w-[100px]">{{ $order->customer_name }}</span>
                                </div>
                            </td>
                            <td class="py-3 text-gray-600 truncate max-w-[150px]">
                                {{ $order->product->name ?? 'Produk Dihapus' }}
                            </td>
                            <td class="py-3 font-semibold text-gray-900">
                                Rp {{ number_format($order->final_price, 0, ',', '.') }}
                            </td> --}}
                            <td class="py-3 text-right">
                                @php
                                $statusColor = match($order->status) {
                                'success', 'paid', 'settlement' => 'bg-green-100 text-green-700 border-green-200',
                                'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                'failed', 'cancel', 'expire' => 'bg-red-100 text-red-700 border-red-200',
                                default => 'bg-gray-100 text-gray-700 border-gray-200',
                                };
                                @endphp
                                <span class="px-3 py-1 text-xs font-medium rounded-full border {{ $statusColor }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-500">Belum ada order.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        {{-- <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-neutral-800 mb-6">Produk Terlaris</h3>
            <div class="space-y-5">
                @forelse($topProducts as $index => $item)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-gray-400 font-bold text-sm">0{{ $index + 1 }}</span>
                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-lg">
                            @if(isset($item->product->country->country_code))
                            <img src="https://flagcdn.com/w40/{{ strtolower($item->product->country->country_code) }}.png"
                                class="w-6 rounded-sm shadow-sm" alt="{{ $item->product->country->country_code }}">
                            @else
                            📦
                            @endif
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-sm font-semibold text-gray-900 truncate max-w-[120px]"
                                title="{{ $item->product->name ?? '-' }}">
                                {{ $item->product->name ?? 'Unknown' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $item->product->country->name ?? 'Global' }}
                            </p>
                        </div>
                    </div>
                    <span class="font-bold text-neutral-800 text-sm whitespace-nowrap">{{ $item->total_sold }}x</span>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center">Belum ada data penjualan.</p>
                @endforelse
            </div>

            <button wire:navigate href="{{ route('admin.manajemen-produk') }}"
                class="w-full mt-6 py-2 text-sm text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50 transition">Lihat
                Katalog</button>
        </div> --}}
    </div>


    {{-- Distribusi kategori, Status Pembayaran, Qris Gateway--}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold text-neutral-800 mb-4">Distribusi Kategori</h3>
            <div class="flex flex-col gap-3">
                @forelse($countryStats as $stat)
                <div class="flex justify-between items-center text-sm">
                    <span class="flex items-center gap-2">
                        @if($stat->country_code)
                        <img src="https://flagcdn.com/w20/{{ strtolower($stat->country_code) }}.png"
                            class="w-4 rounded-sm">
                        @else
                        🌍
                        @endif
                        {{ $stat->name }}
                    </span>
                    <span class="font-semibold text-gray-700">{{ $stat->product_count }} Produk</span>
                </div>
                @empty
                <span class="text-xs text-gray-400">Belum ada kategori.</span>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Status Pembayaran</p>
                <h3 class="text-lg font-bold text-neutral-800">QRIS Gateway</h3>
            </div>
            <div class="text-center">
                @if($isQrisActive)
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                    Online
                </span>
                @else
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200 cursor-pointer"
                    wire:click="goToQris">
                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                    Offline
                </span>
                @endif
            </div>
        </div>

        <div
            class="bg-gradient-to-r from-primary-700 to-primary-600 rounded-2xl shadow-md p-6 text-white flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm mb-1">Voucher Aktif</p>
                <h3 class="text-2xl font-bold">{{ $activeVouchersCount }} Promo</h3>
            </div>
            <div class="h-10 w-10 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                    </path>
                </svg>
            </div>
        </div>

    </div>



</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('livewire:navigated', () => {
        initChart();
    });

    // Fallback jika tidak menggunakan wire:navigate
    document.addEventListener('DOMContentLoaded', () => {
        initChart();
    });

    function initChart() {
        const ctx = document.getElementById('revenueChart');
        if(!ctx) return;

        // Destroy existing chart if any (to prevent canvas reuse error on livewire update)
        if(window.myRevenueChart) {
            window.myRevenueChart.destroy();
        }

        const labels = @json($chartLabels);
        const data = @json($chartData);

        window.myRevenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: data,
                    borderColor: '#2563EB', // Blue-600
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2,
                    tension: 0.4, // Membuat garis melengkung halus
                    fill: true,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#2563EB',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#F3F4F6'
                        },
                        ticks: {
                            callback: function(value, index, values) {
                                // Persingkat angka juta (cth: 1jt)
                                if(value >= 1000000) return 'Rp ' + (value/1000000) + 'jt';
                                if(value >= 1000) return 'Rp ' + (value/1000) + 'rb';
                                return value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
</script>