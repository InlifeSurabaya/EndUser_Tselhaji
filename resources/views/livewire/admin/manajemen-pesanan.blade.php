<div x-data="{ showDetailModal: false }" class="bg-white">
    <div class="p-6 border-b border-neutral-200">
        <h1 class="text-2xl font-semibold text-neutral-800">Manajemen Pesanan</h1>
        <p class="text-sm text-neutral-600 mt-1">Lihat dan kelola semua pesanan yang masuk.</p>
    </div>

    <div class="p-6 space-y-6">

        {{-- Sectoin Button action--}}
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
                    <option value="">Semua Status</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{--        Table Start--}}
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class=" rounded-lg shadow overflow-hidden">
                        <table class="min-w-full divide-y divide-neutral-200">
                            <thead class="bg-neutral-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase">No.
                                    Pesanan
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Pelanggan
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Produk
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Total
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-neutral-600 uppercase">Tanggal
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-neutral-600 uppercase">Aksi
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200">
                            @forelse($orders as $order)
                                <tr wire:key="{{ $order->id }}" class="hover:bg-neutral-50">
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
                                                'success' => 'bg-success/10 text-success',
                                                'pending' => 'bg-warning/10 text-warning',
                                                'failed', 'expired', 'cancelled' => 'bg-primary-100 text-primary-700',
                                                'proses' => 'bg-blue-100 text-blue-800',
                                                default => 'bg-neutral-100 text-neutral-700',
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
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg text-accent-700 bg-accent-100 hover:bg-accent-200 transition-colors"
                                        >
                                            Lihat Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-sm text-neutral-500">
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
        {{--        Table End--}}

        {{-- Pagination --}}
        <div>
            {{ $orders->links() }}
        </div>

    </div>

    {{-- Modal Detail Start--}}
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
                        class="text-neutral-500 hover:text-primary-600 transition-colors">
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
                                            <p class="text-sm font-medium text-neutral-800">{{ $selectedOrder->product?->name }}</p>
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
                                            <dd class="font-medium text-primary-600">-
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

                            {{-- Section 4: Bukti Pembayaran (BARU) --}}
                            @if($selectedOrder->transaction->payment_proof)
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
                                                    >
                                                </a>
                                            </div>

                                            {{-- Link Text --}}
                                            <a href="{{ asset('storage/' . $selectedOrder->transaction->payment_proof) }}"
                                               target="_blank"
                                               class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-primary-600 hover:text-primary-700 hover:underline">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                                Lihat Ukuran Penuh
                                            </a>
                                        </div>
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
                    class="px-4 py-2 text-sm font-semibold rounded-lg text-neutral-700 bg-neutral-100 hover:bg-neutral-200 focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 transition-colors"
                >
                    Tutup
                </button>
            </div>

        </div>
    </div>
    {{-- Modal Detail Start--}}

</div>
