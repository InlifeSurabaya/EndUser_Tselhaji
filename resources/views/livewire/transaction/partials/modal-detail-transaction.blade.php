<!-- Modal Detail Transaksi -->
<div
    x-show="openDetailModal"
    x-transition
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
>
    <div
        @click.outside="openDetailModal = false"
        class="bg-white rounded-xl shadow-lg max-w-lg w-full mx-4"
    >

        <!-- Header -->
        <div class="flex justify-between items-center px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-neutral-900">
                Detail Transaksi
            </h3>
            <button @click="openDetailModal = false" class="text-neutral-500 hover:text-neutral-800">
                ✕
            </button>
        </div>

        <!-- Body -->
        <div class="px-6 py-4 space-y-4 text-sm text-neutral-700">
            @if ($selectedTransaction)
                <div class="flex justify-between">
                    <span>Order ID</span>
                    <span class="font-medium">
                        {{ $selectedTransaction->order->order_number ?? '-' }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>Tanggal</span>
                    <span>
                        {{ $selectedTransaction->created_at->format('d M Y, H:i') }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>Status</span>
                    <span class="font-medium capitalize">
                        {{ $selectedTransaction->status }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>Total</span>
                    <span class="font-semibold">
                        {{ Number::currency($selectedTransaction->net_amount, 'IDR', 'id', 0) }}
                    </span>
                </div>

                <div class="pt-3 border-t">
                    <p class="text-xs text-neutral-500 mb-1">Catatan</p>
                    <p>
                        {{ $selectedTransaction->order->notes ?? 'Tidak ada catatan' }}
                    </p>
                </div>
            @else
                <p class="text-center text-neutral-500">
                    Memuat data...
                </p>
            @endif
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t text-right">
            <button
                @click="openDetailModal = false"
                class="px-4 py-2 rounded-lg bg-neutral-200 hover:bg-neutral-300 text-sm"
            >
                Tutup
            </button>
        </div>
    </div>
</div>
