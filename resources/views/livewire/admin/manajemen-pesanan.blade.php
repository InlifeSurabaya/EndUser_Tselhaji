<div x-data="{ showDetailModal: false }" class="bg-white">
    <div class="p-6 border-b border-neutral-200">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-neutral-800">Manajemen Pesanan</h1>
                <p class="text-sm text-neutral-600 mt-1">Lihat dan kelola semua pesanan yang masuk.</p>
            </div>

            {{-- Tombol Sync ke Laravel A --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <div class="flex items-center gap-3">
                    <button
                        wire:click="syncSelectedToLaravelA"
                        wire:loading.attr="disabled"
                        :disabled="selectedOrders.length === 0"
                        class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm flex items-center gap-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        title="Sync selected orders to Laravel A"
                    >
                        <svg wire:loading.remove wire:target="syncSelectedToLaravelA" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <svg wire:loading wire:target="syncSelectedToLaravelA" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Sync Selected ({{ count($selectedOrders) }})</span>
                    </button>

                    <button
                        wire:click="syncAllToLaravelA"
                        wire:loading.attr="disabled"
                        class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm flex items-center gap-2 transition-colors"
                        title="Sync all orders to Laravel A"
                    >
                        <svg wire:loading.remove wire:target="syncAllToLaravelA" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <svg wire:loading wire:target="syncAllToLaravelA" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Sync All ({{ $totalOrders }})</span>
                    </button>
                </div>

                {{-- Test connection buttons --}}
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <button
                        wire:click="checkLaravelAConnection"
                        wire:loading.attr="disabled"
                        class="px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"/>
                        </svg>
                        Test Connection
                    </button>

                    <button
                        wire:click="testSync"
                        wire:loading.attr="disabled"
                        class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Test Sync
                    </button>
                </div>
            </div>
        </div>

        {{-- Status Sync --}}
        @if($syncStatus)
            <div class="mt-4 mb-0 p-3 rounded-lg border {{ str_contains($syncStatus, '✅') ? 'bg-green-50 border-green-200 text-green-800' : (str_contains($syncStatus, 'Syncing') || str_contains($syncStatus, '🔄') ? 'bg-blue-50 border-blue-200 text-blue-800' : 'bg-yellow-50 border-yellow-200 text-yellow-800') }}">
                <div class="flex items-start gap-3">
                    @if(str_contains($syncStatus, '✅'))
                        <svg class="w-5 h-5 mt-0.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @elseif(str_contains($syncStatus, 'Syncing') || str_contains($syncStatus, '🔄'))
                        <svg class="w-5 h-5 mt-0.5 text-blue-500 flex-shrink-0 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    @else
                        <svg class="w-5 h-5 mt-0.5 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    @endif
                    <div>
                        <p class="font-medium">{{ $syncStatus }}</p>
                        @if($isSyncing)
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full animate-pulse"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Debug Info Section --}}
        @if($debugInfo)
            <div class="mt-4 p-4 bg-gray-900 border border-gray-700 rounded-lg">
                <div class="flex justify-between items-center mb-2">
                    <p class="font-semibold text-gray-300">Debug Information:</p>
                    <div class="flex gap-2">
                        <button
                            onclick="copyDebugInfo()"
                            class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 rounded flex items-center gap-1"
                        >
                            <i class="fas fa-copy text-sm"></i> Copy
                        </button>
                        <button
                            wire:click="$set('debugInfo', '')"
                            class="px-3 py-1 text-sm bg-red-100 hover:bg-red-200 text-red-700 rounded"
                        >
                            Clear
                        </button>
                        <button
                            onclick="document.getElementById('debugInfo').classList.toggle('hidden')"
                            class="text-gray-400 hover:text-white text-sm"
                        >
                            Toggle
                        </button>
                    </div>
                </div>
                <div id="debugInfo" class="overflow-auto max-h-96">
                    <pre class="text-xs text-green-400 font-mono whitespace-pre-wrap">{{ $debugInfo }}</pre>
                </div>
            </div>
        @endif
    </div>

    <div class="p-6 space-y-6">

        {{-- Section Filter & Search --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="p-3 pl-10 block w-full text-sm rounded-lg border-neutral-200 focus:border-accent-500 focus:ring-accent-500"
                        placeholder="Cari (No. Pesanan, Nama, Email, Produk)..."
                    >
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="h-4 w-4 text-neutral-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div>
                <select
                    wire:model.live.debounce.300ms="statusFilter"
                    class="p-3 block w-full text-sm rounded-lg border-neutral-200 focus:border-accent-500 focus:ring-accent-500"
                >
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Table Start --}}
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="rounded-lg shadow overflow-hidden">
                        <table class="min-w-full divide-y divide-neutral-200">
                            <thead class="bg-neutral-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase">
                                    <input type="checkbox"
                                           @click="wire.selectAll()"
                                           class="rounded border-neutral-300 cursor-pointer"
                                           :checked="selectedOrders.length === {{ $totalOrders }}">
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase">
                                    No. Pesanan
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase">
                                    Pelanggan
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase">
                                    Produk
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase">
                                    Total
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase">
                                    Tanggal
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-neutral-600 uppercase">
                                    Aksi
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200">
                            @forelse($orders as $order)
                                <tr wire:key="{{ $order->id }}" class="hover:bg-neutral-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input
                                            type="checkbox"
                                            value="{{ $order->id }}"
                                            wire:model="selectedOrders"
                                            class="rounded border-neutral-300 cursor-pointer"
                                        >
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-800">{{ $order->order_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">
                                        <div>{{ $order->customer_name }}</div>
                                        <div class="text-xs text-neutral-500">{{ $order->customer_email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">{{ $order->product?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">
                                        IDR {{ number_format($order->final_price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $statusClass = match($order->status) {
                                                'success' => 'bg-green-100 text-green-800',
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'failed', 'expired', 'cancelled' => 'bg-red-100 text-red-800',
                                                'proses' => 'bg-blue-100 text-blue-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span
                                            class="py-1 px-2.5 inline-flex items-center gap-x-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button
                                            type="button"
                                            @click="showDetailModal = true; $wire.getOrderDetails({{ $order->id }})"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors"
                                        >
                                            Lihat Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-10 text-center text-sm text-neutral-500">
                                        Tidak ada data pesanan yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- Table End --}}

        {{-- Pagination --}}
        <div>
            {{ $orders->links() }}
        </div>

    </div>

    {{-- Modal Detail Start --}}
    <div
        x-cloak
        x-show="showDetailModal"
        @keydown.escape.window="showDetailModal = false; $wire.closeModal()"
        class="fixed inset-0 z-50 flex items-center justify-center"
    >
        {{-- Backdrop --}}
        <div x-show="showDetailModal" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0" class="absolute inset-0 bg-neutral-900/50 backdrop-blur-sm"></div>

        {{-- Modal Content --}}
        <div
            x-show="showDetailModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-xl w-full max-w-2xl relative overflow-hidden"
            @click.away="showDetailModal = false; $wire.closeModal()"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between p-6 border-b border-neutral-200">
                <h2 class="text-xl font-semibold text-neutral-800">
                    Detail Pesanan
                </h2>
                <button @click="showDetailModal = false; $wire.closeModal()"
                        class="text-neutral-500 hover:text-blue-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-6 max-h-[70vh] overflow-y-auto">

                {{-- Loading State --}}
                <div wire:loading.block wire:target="getOrderDetails" class="animate-pulse space-y-5">
                    <div class="h-5 bg-neutral-200 rounded w-1/3"></div>
                    <div class="space-y-3">
                        <div class="h-4 bg-neutral-200 rounded w-full"></div>
                        <div class="h-4 bg-neutral-200 rounded w-5/6"></div>
                    </div>
                    <div class="h-5 bg-neutral-200 rounded w-1/4 mt-4"></div>
                    <div class="space-y-3">
                        <div class="h-4 bg-neutral-200 rounded w-full"></div>
                        <div class="h-4 bg-neutral-200 rounded w-full"></div>
                    </div>
                </div>

                <div wire:loading.remove wire:target="getOrderDetails">
                    @if($selectedOrder)
                        <div class="space-y-6">

                            {{-- Section 1: Rincian Pesanan --}}
                            <div>
                                <h3 class="text-lg font-semibold text-neutral-800 mb-3">Rincian Pesanan</h3>
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3">
                                    <div class="text-sm">
                                        <dt class="font-medium text-neutral-600">No. Pesanan:</dt>
                                        <dd class="text-neutral-800 font-medium">{{ $selectedOrder->order_number }}</dd>
                                    </div>
                                    <div class="text-sm">
                                        <dt class="font-medium text-neutral-600">Tanggal:</dt>
                                        <dd class="text-neutral-800">{{ $selectedOrder->created_at->format('d M Y, H:i') }}</dd>
                                    </div>
                                    <div class="text-sm">
                                        <dt class="font-medium text-neutral-600">Status Pesanan:</dt>
                                        <dd class="text-neutral-800">{{ ucfirst($selectedOrder->status) }}</dd>
                                    </div>
                                    <div class="text-sm">
                                        <dt class="font-medium text-neutral-600">Catatan:</dt>
                                        <dd class="text-neutral-800">{{ $selectedOrder->notes ?? '-' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <hr class="border-neutral-200"/>

                            {{-- Section 2: Pelanggan --}}
                            <div>
                                <h3 class="text-lg font-semibold text-neutral-800 mb-3">Pelanggan</h3>
                                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3">
                                    <div class="text-sm">
                                        <dt class="font-medium text-neutral-600">Nama:</dt>
                                        <dd class="text-neutral-800">{{ $selectedOrder->customer_name ?? 'Tamu' }}</dd>
                                    </div>
                                    <div class="text-sm">
                                        <dt class="font-medium text-neutral-600">Email:</dt>
                                        <dd class="text-neutral-800">{{ $selectedOrder->customer_email }}</dd>
                                    </div>
                                    <div class="text-sm">
                                        <dt class="font-medium text-neutral-600">Telepon:</dt>
                                        <dd class="text-neutral-800">{{ $selectedOrder->customer_phone ?? '-' }}</dd>
                                    </div>
                                    <div class="text-sm">
                                        <dt class="font-medium text-neutral-600">User ID:</dt>
                                        <dd class="text-neutral-800">{{ $selectedOrder->user_id ?? 'Tamu' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <hr class="border-neutral-200"/>

                            {{-- Section 3: Produk & Pembayaran --}}
                            <div>
                                <h3 class="text-lg font-semibold text-neutral-800 mb-3">Produk & Pembayaran</h3>
                                <div class="flow-root">
                                    <ul class="divide-y divide-neutral-200">
                                        <li class="py-3">
                                            <p class="text-sm font-medium text-neutral-800">{{ $selectedOrder->product?->name ?? 'N/A' }}</p>
                                            <p class="text-sm text-neutral-600">1x</p>
                                        </li>
                                    </ul>
                                </div>
                                <dl class="mt-4 space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <dt class="text-neutral-600">Harga Asli:</dt>
                                        <dd class="font-medium text-neutral-800">
                                            IDR {{ number_format($selectedOrder->original_price, 0, ',', '.') }}</dd>
                                    </div>
                                    @if($selectedOrder->voucher_id)
                                        <div class="flex justify-between">
                                            <dt class="text-neutral-600">Diskon ({{ $selectedOrder->voucher?->code }}):</dt>
                                            <dd class="font-medium text-blue-600">-
                                                IDR {{ number_format($selectedOrder->discount_amount, 0, ',', '.') }}</dd>
                                        </div>
                                    @endif
                                    <div class="flex justify-between border-t border-neutral-200 pt-2">
                                        <dt class="font-semibold text-neutral-800">Total Akhir:</dt>
                                        <dd class="font-semibold text-neutral-800">
                                            IDR {{ number_format($selectedOrder->final_price, 0, ',', '.') }}</dd>
                                    </div>
                                </dl>
                            </div>

                            {{-- Section 4: Bukti Pembayaran --}}
                            @if($selectedOrder && $selectedOrder->transaction && $selectedOrder->transaction->payment_proof)
                                <hr class="border-neutral-200"/>
                                <div>
                                    <h3 class="text-lg font-semibold text-neutral-800 mb-3">Bukti Pembayaran</h3>
                                    <div class="bg-neutral-50 border border-neutral-200 rounded-lg p-4">
                                        <div class="flex flex-col items-center">
                                            {{-- Thumbnail Gambar --}}
                                            <div class="relative group cursor-pointer overflow-hidden rounded-md border border-neutral-300 shadow-sm">
                                                <a href="{{ asset('storage/' . $selectedOrder->transaction->payment_proof) }}" target="_blank">
                                                    <img
                                                        src="{{ asset('storage/' . $selectedOrder->transaction->payment_proof) }}"
                                                        alt="Bukti Pembayaran"
                                                        class="max-h-64 w-auto object-contain hover:scale-105 transition-transform duration-300"
                                                        onerror="this.onerror=null; this.src='https://via.placeholder.com/300x200?text=Gambar+Rusak';"
                                                    >
                                                </a>
                                            </div>

                                            {{-- Link Text --}}
                                            <a href="{{ asset('storage/' . $selectedOrder->transaction->payment_proof) }}"
                                               target="_blank"
                                               class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                                Lihat Ukuran Penuh
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <hr class="border-neutral-200"/>
                                <div>
                                    <h3 class="text-lg font-semibold text-neutral-800 mb-3">Bukti Pembayaran</h3>
                                    <div class="flex flex-col items-center justify-center p-6 bg-gray-50 border border-dashed border-gray-300 rounded-lg">
                                        <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-gray-400 italic text-sm">Belum ada bukti pembayaran</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Section 5: Transaksi (Opsional) --}}
                            @if($selectedTransaction)
                                <hr class="border-neutral-200"/>
                                <div>
                                    <h3 class="text-lg font-semibold text-neutral-800 mb-3">Transaksi</h3>
                                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3">
                                        <div class="text-sm">
                                            <dt class="font-medium text-neutral-600">No. Transaksi:</dt>
                                            <dd class="text-neutral-800">{{ $selectedTransaction->transaction_number }}</dd>
                                        </div>
                                        <div class="text-sm">
                                            <dt class="font-medium text-neutral-600">Metode Pembayaran:</dt>
                                            <dd class="text-neutral-800">{{ $selectedTransaction->payment_type }}</dd>
                                        </div>
                                        <div class="text-sm">
                                            <dt class="font-medium text-neutral-600">Status Pembayaran:</dt>
                                            <dd class="text-neutral-800">{{ ucfirst($selectedTransaction->status) }}</dd>
                                        </div>
                                        <div class="text-sm">
                                            <dt class="font-medium text-neutral-600">Waktu Pembayaran:</dt>
                                            <dd class="text-neutral-800">{{ $selectedTransaction->settlement_time ? \Carbon\Carbon::parse($selectedTransaction->settlement_time)->format('d M Y, H:i') : '-' }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            @endif

                        </div>
                    @else
                        <div class="text-center text-neutral-500 py-10">
                            Gagal memuat data pesanan.
                        </div>
                    @endif
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 p-6 border-t border-neutral-200 bg-neutral-50 rounded-b-2xl">
                <button
                    type="button"
                    @click="showDetailModal = false; $wire.closeModal()"
                    class="px-4 py-2 text-sm font-semibold rounded-lg text-neutral-700 bg-neutral-100 hover:bg-neutral-200 transition-colors"
                >
                    Tutup
                </button>
            </div>

        </div>
    </div>
    {{-- Modal Detail End --}}

</div>

<script>
function copyDebugInfo() {
    const debugText = document.getElementById('debugInfo')?.textContent;
    if (debugText) {
        navigator.clipboard.writeText(debugText).then(() => {
            alert('✅ Debug info copied!');
        });
    }
}

// Auto-scroll debug output to bottom
document.addEventListener('livewire:initialized', () => {
    Livewire.on('debug-updated', () => {
        const debugOutput = document.getElementById('debugInfo');
        if (debugOutput) {
            debugOutput.scrollTop = debugOutput.scrollHeight;
        }
    });
});
</script>
