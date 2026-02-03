<div class="max-w-7xl mx-auto my-7 px-4 sm:px-6 lg:px-8">
    @if($product)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- KOLOM KIRI (Detail Produk & Form) --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- ... (Kode Card Produk Bagian Atas TETAP SAMA) ... --}}
                <div class="bg-white rounded-lg shadow-sm border border-[var(--color-border)]">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-[var(--color-neutral-600)]">{{ $product->country->name }}</span>
                        </div>
                        <h1 class="text-2xl font-bold text-[var(--color-neutral-900)] mb-3">{{ $product->name }}</h1>
                        {{-- ... Detail Kuota & Masa Aktif ... --}}
                        <div class="flex items-center space-x-6 border-y border-[var(--color-border)] py-4 my-4">
                            <div>
                                <span class="text-xs text-[var(--color-neutral-500)]">Kuota</span>
                                <p class="text-lg font-semibold text-[var(--color-neutral-800)]">
                                    {{ $product->quota_amount }} {{ strtoupper($product->quota_type) }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-[var(--color-neutral-500)]">Masa Aktif</span>
                                <p class="text-lg font-semibold text-[var(--color-neutral-800)]">
                                    {{ $product->validity_days }} Hari
                                </p>
                            </div>
                        </div>
                        <p class="text-sm text-[var(--color-neutral-700)]">{{ $product->detail }}</p>
                    </div>
                </div>

                {{-- Form Input User (Email, Voucher, Phone, Notes) --}}
                <div class="bg-white rounded-lg shadow-sm border border-[var(--color-border)]">
                    <div class="p-6 space-y-5">
                        {{-- ... (Input Form TETAP SAMA seperti sebelumnya) ... --}}
                        @guest
                            <div>
                                <label for="guestEmail" class="block text-sm font-medium mb-2 text-[var(--color-neutral-700)]">Email</label>
                                <input type="email" id="guestEmail" wire:model.blur="guestEmail" class="py-3 px-4 block w-full border border-[var(--color-border)] rounded-lg text-sm focus:border-[var(--color-primary-600)] focus:ring-[var(--color-primary-600)]" placeholder="email@contoh.com">
                                @error('guestEmail') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>
                        @endguest

                        {{-- List Voucher Tersedia --}}
                        @if($availableVouchers->count() > 0)
                            {{-- ... (Kode Loop Voucher TETAP SAMA) ... --}}
                            <div class="pt-2">
                                <label class="block text-sm font-medium mb-2 text-[var(--color-neutral-700)]">Voucher Tersedia</label>
                                <div class="flex overflow-x-auto py-2 gap-3" style="scrollbar-width: thin;">
                                    @foreach($availableVouchers as $v)
                                        <div x-data="{ code: '{{ $v->code }}', copied: false }" class="flex-shrink-0 flex items-center gap-2 border border-dashed border-[var(--color-primary-600)] bg-[var(--color-primary-50)] rounded-lg pl-3 pr-2 py-1.5">
                                            <span class="text-sm font-medium text-[var(--color-primary-700)]">{{ $v->code }}</span>
                                            <button type="button" @click="navigator.clipboard.writeText(code); $wire.set('voucher', code); copied = true; setTimeout(() => copied = false, 2000);" class="text-xs font-semibold rounded-md px-2 py-1 transition-all" :class="copied ? 'bg-green-500 text-white' : 'bg-white text-[var(--color-primary-700)] hover:bg-gray-50'">
                                                <span x-show="!copied">Salin</span><span x-show="copied" style="display: none;">Tersalin!</span>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div>
                            <label for="phoneNumber" class="block text-sm font-medium mb-2 text-[var(--color-neutral-700)]">No. Telepon</label>
                            <input type="tel" id="phoneNumber" wire:model.blur="phoneNumber" class="py-3 px-4 block w-full border border-[var(--color-border)] rounded-lg text-sm focus:border-[var(--color-primary-600)] focus:ring-[var(--color-primary-600)]" required>
                            @error('phoneNumber') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 text-[var(--color-neutral-700)]">Kode Voucher</label>
                            <div class="flex gap-2">
                                <input type="text" wire:model="voucher" class="py-3 px-4 block w-full border border-[var(--color-border)] rounded-lg text-sm focus:border-[var(--color-primary-600)] focus:ring-[var(--color-primary-600)]" placeholder="Masukkan kode voucher">
                                <button type="button" wire:click="checkVoucher" class="py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-[var(--color-border)] bg-white text-[var(--color-neutral-700)] hover:bg-[var(--color-neutral-50)] w-[120px]">
                                    <span wire:loading.remove wire:target="checkVoucher">Terapkan</span>
                                    <span wire:loading wire:target="checkVoucher"><span class="animate-spin inline-block size-4 border-[3px] border-current border-t-transparent text-[var(--color-neutral-600)] rounded-full"></span></span>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 text-[var(--color-neutral-700)]">Catatan</label>
                            <textarea wire:model.blur="notes" rows="3" class="py-3 px-4 block w-full border border-[var(--color-border)] rounded-lg text-sm focus:border-[var(--color-primary-600)] focus:ring-[var(--color-primary-600)]"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN (Ringkasan Pembayaran - DIPERBARUI) --}}
            <div class="lg:col-span-1">
                <div class="lg:sticky lg:top-7">
                    <div class="bg-white rounded-lg shadow-sm border border-[var(--color-border)]">
                        <div class="p-6">
                            <h2 class="text-xl font-semibold text-[var(--color-neutral-900)] mb-4">Ringkasan Pembayaran</h2>

                            <div class="space-y-3">
                                {{-- Harga Asli --}}
                                <div class="flex justify-between text-sm text-[var(--color-neutral-700)]">
                                    <span>Harga Asli</span>
                                    <span>{{ Number::currency($product->price, 'IDR', 'id', 0) }}</span>
                                </div>

                                {{-- 1. Diskon Produk --}}
                                @if($productDiscount > 0)
                                    <div class="flex justify-between text-sm text-[var(--color-success)]">
                                        <span>Diskon Produk ({{ $product->discount }}%)</span>
                                        <span>- {{ Number::currency($productDiscount, 'IDR', 'id', 0) }}</span>
                                    </div>
                                @endif

                                {{-- 2. BARU: Diskon Segmen User --}}
                                @if($segmentDiscountAmount > 0)
                                    <div class="flex justify-between text-sm text-purple-600 font-medium">
                                        <div class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path d="M11.251.068a.5.5 0 0 1 .227.58L9.677 6.5H13a.5.5 0 0 1 .364.843l-8 8.5a.5.5 0 0 1-.842-.49L6.323 9.5H3a.5.5 0 0 1-.364-.843l8-8.5a.5.5 0 0 1 .615-.09z"/></svg>
                                            <span>Diskon Spesial ({{ $discountSegmentUser }}%)</span>
                                        </div>
                                        <span>- {{ Number::currency($segmentDiscountAmount, 'IDR', 'id', 0) }}</span>
                                    </div>
                                @endif

                                {{-- 3. Diskon Voucher --}}
                                @if($voucherDiscount > 0)
                                    <div class="flex justify-between text-sm text-[var(--color-success)]">
                                        <span>Voucher ({{ $voucherModel->code }})</span>
                                        <span>- {{ Number::currency($voucherDiscount, 'IDR', 'id', 0) }}</span>
                                    </div>
                                @endif
                            </div>

                            <hr class="my-4 border-dashed border-[var(--color-border)]">

                            <div class="flex justify-between items-center mb-5">
                                <span class="text-lg font-semibold text-[var(--color-neutral-900)]">Total Bayar</span>
                                <span class="text-2xl font-bold text-[var(--color-primary-600)]">
                                    {{ Number::currency( $finalPrice, 'IDR', 'id', 0) }}
                                </span>
                            </div>

                            <div class="space-y-3">
                                <button type="button" wire:click="createOrder" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-[var(--color-primary-600)] text-white hover:bg-[var(--color-primary-700)] disabled:opacity-50 disabled:pointer-events-none">
                                    <span wire:loading.remove wire:target="createOrder">Lanjutkan ke Pembayaran</span>
                                    <span wire:loading wire:target="createOrder">
                                        <span class="animate-spin inline-block size-4 border-[3px] border-current border-t-transparent text-white rounded-full"></span>
                                        Memproses...
                                    </span>
                                </button>

                                <button type="button" onclick="window.history.back()" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-[var(--color-border)] bg-white text-[var(--color-neutral-700)] shadow-sm hover:bg-[var(--color-neutral-50)] disabled:opacity-50 disabled:pointer-events-none">
                                    Batalkan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @else
        {{-- Loading State --}}
        <div class="text-center py-20">
            <p class="text-[var(--color-neutral-600)] mb-4">Memuat detail produk...</p>
            <div class="animate-spin inline-block size-8 border-[3px] border-current border-t-transparent text-[var(--color-primary-600)] rounded-full"></div>
        </div>
    @endif
</div>
// Di halaman checkout, tambahkan script ini
<script>
document.addEventListener('livewire:initialized', () => {
    // Listen untuk order created
    Livewire.on('order-created', (event) => {
        if (event.success) {
            // Redirect ke success page
            window.location.href = `/order/success/${event.order_number}`;
        } else {
            // Show error
            Swal.fire('Error', event.message || 'Failed to create order', 'error');
        }
    });

    // Intercept sebelum create order
    Livewire.on('creating-order', () => {
        // Bisa tambahkan loading state
        console.log('Creating order in Laravel B...');
    });
});
</script>
