<div class="max-w-2xl mx-auto my-7 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm border border-[var(--color-border)]">
        <div class="p-6 sm:p-8">

            <!-- [ HEADER ] -->
            <div class="text-center">
                <h1 class="text-2xl sm:text-3xl font-bold text-[var(--color-neutral-900)]">
                    Cek Status Pesanan Anda
                </h1>
                <p class="mt-2 text-sm text-[var(--color-neutral-600)]">
                    Masukkan nomor order yang Anda terima untuk melihat status terbaru.
                </p>
            </div>

            <!-- [ FORM PENCARIAN ] -->
            <!-- [ FORM PENCARIAN YANG DIPERBARUI ] -->
            <form class="mt-8 space-y-4" wire:submit.prevent="searchOrderNumber">

                <!-- Input Nomor Order -->
                <div>
                    <label for="orderNumber" class="block text-sm font-medium mb-2 text-[var(--color-neutral-700)]">
                        Nomor Order
                    </label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                            <svg class="flex-shrink-0 size-4 text-[var(--color-neutral-400)]" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15.5 15.5 19 19"/>
                                <path d="M5 11a6 6 0 1 0 12 0 6 6 0 1 0-12 0Z"/>
                            </svg>
                        </div>
                        <input type="text" id="orderNumber"
                               wire:model="orderNumber"
                               class="py-3 ps-11 pe-4 block w-full border border-[var(--color-border)] rounded-lg text-sm focus:border-[var(--color-primary-600)] focus:ring-[var(--color-primary-600)]"
                               placeholder="Contoh: ORD/202510/0001">
                    </div>
                    @error('orderNumber') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- [BARU] Input Email -->
                <!-- <div>
                    <label for="email" class="block text-sm font-medium mb-2 text-[var(--color-neutral-700)]">
                        Email Pemesan
                    </label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                            <svg class="flex-shrink-0 size-4 text-[var(--color-neutral-400)]" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="16" x="2" y="4" rx="2"/>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                            </svg>
                        </div>
                        <input type="email" id="email"
                               wire:model="email"
                               class="py-3 ps-11 pe-4 block w-full border border-[var(--color-border)] rounded-lg text-sm focus:border-[var(--color-primary-600)] focus:ring-[var(--color-primary-600)]"
                               placeholder="email@anda.com">
                    </div>
                    @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div> -->

                <!-- Tombol Cek Pesanan (dibuat full-width) -->
                <button type="submit"
                        class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-[var(--color-primary-600)] text-white hover:bg-[var(--color-primary-700)] disabled:opacity-50 disabled:pointer-events-none"
                        wire:loading.attr="disabled">

                    <span wire:loading.remove wire:target="searchOrderNumber">
                        Cek Pesanan
                    </span>
                    <span wire:loading wire:target="searchOrderNumber" class="inline-flex items-center gap-x-2">
                        <span class="animate-spin inline-block size-4 border-[3px] border-current border-t-transparent text-white rounded-full" role="status" aria-label="loading"></span>
                        Mencari...
                    </span>
                </button>
            </form>

            <!-- [ AREA HASIL PENCARIAN ] -->
            <div class="mt-8" wire:loading.class="opacity-50 transition-opacity">

                @if ($data)
                    {{-- ====================== --}}
                    {{--      HASIL DITEMUKAN   --}}
                    {{-- ====================== --}}
                    @php
                        // Logika untuk badge status
                        $statusText = '';
                        $statusColorClasses = '';
                        switch ($data->status) {
                            case 'pending': $statusText = 'Menunggu Pembayaran'; $statusColorClasses = 'bg-yellow-100 text-yellow-800'; break;
                            case 'settlement': $statusText = 'Pembayaran Berhasil'; $statusColorClasses = 'bg-green-100 text-green-800'; break;
                            default: $statusText = ucfirst($data->status); $statusColorClasses = 'bg-gray-100 text-gray-800'; break;
                        }
                    @endphp

                    <div class="border-2 border-dashed border-[var(--color-primary-200)] rounded-lg p-5">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold text-[var(--color-primary-800)]">Pesanan Ditemukan</p>
                                <h3 class="text-lg font-bold text-[var(--color-neutral-900)] mt-1">{{ $data->order_number }}</h3>
                                <p class="text-xs text-[var(--color-neutral-500)] mt-1">
                                    Dibuat pada: {{ \Carbon\Carbon::parse($data->created_at)->isoFormat('D MMMM YYYY, HH:mm') }}
                                </p>
                            </div>
                            <div class="mt-3 sm:mt-0">
                                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium {{ $statusColorClasses }}">
                                    {{ $statusText }}
                                </span>
                            </div>
                        </div>

                        <hr class="my-4 border-dashed border-[var(--color-border)]">

                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xs text-[var(--color-neutral-500)]">Total Bayar</p>
                                <p class="text-lg font-bold text-[var(--color-neutral-800)]">{{ Number::currency($data->final_price, 'IDR', 'id', 0) }}</p>
                            </div>
                            <a href="{{ route('order.detail', ['uuidOrder' => $data->uuid]) }}" wire:navigate
                               class="py-2 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-[var(--color-accent-600)] text-white hover:bg-[var(--color-accent-700)]">
                                Lihat Detail
                            </a>
                        </div>
                    </div>

                @elseif ($searched && !$data)
                    {{-- ====================== --}}
                    {{-- HASIL TIDAK DITEMUKAN  --}}
                    {{-- ====================== --}}
                    <div class="text-center border-2 border-dashed border-[var(--color-border)] rounded-lg p-8">
                        <svg class="mx-auto size-12 text-[var(--color-neutral-400)]" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21.174 18.337a1 1 0 0 1-1.414 1.414l-4.243-4.243a8 8 0 1 1 1.414-1.414l4.243 4.243Zm-8.174-2.337a6 6 0 1 0-8.485-8.485 6 6 0 0 0 8.485 8.485Z"/>
                            <path d="m14.5 9.5-5 5"/>
                            <path d="m9.5 9.5 5 5"/>
                        </svg>
                        <h3 class="mt-4 text-lg font-semibold text-[var(--color-neutral-800)]">Pesanan Tidak Ditemukan</h3>
                        <p class="mt-1 text-sm text-[var(--color-neutral-600)]">
                            Pastikan nomor order yang Anda masukkan sudah benar dan coba lagi.
                        </p>
                    </div>

                @else
                    {{-- ====================== --}}
                    {{--     TAMPILAN AWAL     --}}
                    {{-- ====================== --}}
                    <div class="text-center border-2 border-dashed border-[var(--color-border)] rounded-lg p-8">
                        <p class="text-sm text-[var(--color-neutral-500)]">
                            Hasil pencarian akan muncul di sini.
                        </p>
                    </div>
                @endif

            </div>

        </div>
    </div>
</div>
