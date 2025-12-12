@php
    // Logika warna badge status
    $statusText = '';
    $statusColorClasses = '';

    switch ($order->status) {
        case 'pending':
            $statusText = 'Menunggu Pembayaran';
            $statusColorClasses = 'bg-yellow-100 text-yellow-800';
            break;
        case 'settlement':
            $statusText = 'Pembayaran Berhasil';
            $statusColorClasses = 'bg-green-100 text-green-800';
            break;
        case 'cancel':
            $statusText = 'Dibatalkan';
            $statusColorClasses = 'bg-[var(--color-primary-50)] text-[var(--color-primary-700)]';
            break;
        case 'expire':
            $statusText = 'Kedaluwarsa';
            $statusColorClasses = 'bg-[var(--color-primary-50)] text-[var(--color-primary-700)]';
            break;
        case 'deny':
            $statusText = 'Pembayaran Gagal';
            $statusColorClasses = 'bg-[var(--color-primary-50)] text-[var(--color-primary-700)]';
            break;
        case 'proses':
              $statusText = "Proses pengecekan admin";
              $statusColorClasses = 'bg-blue-100 text-blue-800';
              break;
        default:
            $statusText = strtoupper($order->status);
            $statusColorClasses = 'bg-[var(--color-neutral-100)] text-[var(--color-neutral-700)]';
    }
@endphp

<div class="max-w-3xl mx-auto my-7 px-4 sm:px-6 lg:px-8" x-data="{ showQrisModal: false }">

    <div class="space-y-6">

        @if ($order->status == 'pending')
            <div class="bg-white rounded-lg shadow-sm border border-[var(--color-border)]">
                <div class="p-6 text-center">

                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium {{ $statusColorClasses }}">
                        {{ $statusText }}
                    </span>

                    <div class="mt-4"
                         x-data="{
                             textToCopy: '{{ $order->order_number }}',
                             copied: false,
                             copy() {
                                 navigator.clipboard.writeText(this.textToCopy);
                                 this.copied = true;
                                 setTimeout(() => { this.copied = false }, 2000);
                             }
                         }">
                        <span class="text-sm text-[var(--color-neutral-600)]">
                            Nomor Order:
                        </span>
                        <div class="flex items-center justify-center gap-2 mt-1">
                            <span class="font-medium text-[var(--color-neutral-800)] select-all">
                                {{ $order->order_number }}
                            </span>
                            <button type="button" @click="copy()"
                                    class="py-1 px-2 inline-flex justify-center items-center gap-x-1 text-xs font-medium rounded-lg border border-[var(--color-border)] bg-white text-[var(--color-neutral-700)] hover:bg-[var(--color-neutral-50)]">
                                <svg class="size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/>
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2 2V6a2 2 0 0 1 2-2h2"/>
                                </svg>
                                <span x-show="!copied">Copy</span>
                                <span x-show="copied" class="text-green-600">Copied!</span>
                            </button>
                        </div>
                    </div>

                    <h2 class="mt-4 text-sm font-medium text-[var(--color-neutral-600)]">Total Tagihan</h2>
                    <p class="text-4xl font-bold text-[var(--color-primary-600)] tracking-tight">
                        {{ Number::currency($order->final_price, 'IDR', 'id', 0) }}
                    </p>

                    <div class="mt-6 p-4 rounded-lg bg-[var(--color-primary-50)] border border-[var(--color-primary-200)]">
                        <p class="text-sm font-medium text-[var(--color-primary-800)]">
                            Segera bayar sebelum:
                        </p>
                        <p class="text-lg font-bold text-[var(--color-primary-700)]">
                            {{ \Carbon\Carbon::parse($order->expired_at)->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }}
                        </p>
                    </div>

                    @empty($qris == null)
                        <button type="button"
                                @click="showQrisModal = true"
                                class="w-full mt-6 py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-[var(--color-primary-600)] text-white hover:bg-[var(--color-primary-700)]">
                            Bayar Sekarang (QRIS)
                        </button>
                    @endempty

                    @empty($qris != null)
                        <p class="my-4 text-[var(--color-primary-800)] font-bold text-xl">Cek secara berkala, QR Qris akan muncul.</p>
                    @endempty

                    {{-- FITUR BARU: UPLOAD BUKTI BAYAR (ACCORDION) --}}
                    @if(empty($order->transaction))
                    <div x-data="{ openUpload: false }" class="mt-6 border-t border-[var(--color-border)] pt-4">
                        <button @click="openUpload = !openUpload" type="button"
                                class="text-sm text-[var(--color-primary-600)] font-medium hover:underline flex items-center justify-center w-full gap-1">
                            <span>Sudah transfer manual? Upload Bukti Bayar</span>
                            <svg :class="openUpload ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="openUpload" x-transition style="display: none;">
                            <div class="mt-4 text-left bg-[var(--color-neutral-50)] p-4 rounded-lg border border-[var(--color-border)]">
                                <label class="block text-sm font-medium text-[var(--color-neutral-700)] mb-2">
                                    Pilih File Gambar
                                </label>

                                <input type="file" wire:model="buktiPembayaran"
                                       class="block w-full text-sm text-slate-500
                                       file:mr-4 file:py-2 file:px-4
                                       file:rounded-full file:border-0
                                       file:text-xs file:font-semibold
                                       file:bg-[var(--color-primary-100)] file:text-[var(--color-primary-700)]
                                       hover:file:bg-[var(--color-primary-200)]
                                       border border-[var(--color-border)] rounded-lg cursor-pointer bg-white mb-2">

                                @error('paymentProof')
                                <span class="text-xs text-red-500 block mb-2">{{ $message }}</span>
                                @enderror

                                <button wire:click="createTransaction"
                                        wire:loading.attr="disabled"
                                        class="w-full mt-2 py-2 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-[var(--color-neutral-800)] text-white hover:bg-[var(--color-neutral-900)] disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span wire:loading.remove wire:target="createTransaction">Kirim Bukti</span>
                                    <span wire:loading wire:target="createTransaction">Mengirim...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                    {{-- ========================================== --}}

                </div>
            </div>
        @else
            {{-- Tampilan jika Status BUKAN Pending (Settlement, Expire, dll) --}}
            <div class="bg-white rounded-lg shadow-sm border border-[var(--color-border)]">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-[var(--color-neutral-900)]">Status Pesanan</h2>
                            <p class="text-sm text-[var(--color-neutral-600)]">
                                Nomor Order:
                                <span class="font-medium text-[var(--color-neutral-800)]">{{ $order->order_number }}</span>
                            </p>
                        </div>
                        <div class="mt-3 sm:mt-0">
                            <span class="inline-flex items-center gap-x-1.5 py-2 px-4 rounded-lg text-sm font-medium {{ $statusColorClasses }}">
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>
                    @if ($order->status == 'settlement' && $order->settlement_time)
                        <p class="mt-4 text-sm text-green-700">
                            Pembayaran telah dikonfirmasi pada:
                            <span class="font-medium">{{ \Carbon\Carbon::parse($order->settlement_time)->isoFormat('D MMMM YYYY, HH:mm') }}</span>
                        </p>
                    @endif

                    {{-- Menampilkan Bukti Bayar jika sudah diupload (Opsional) --}}
                    @if($order->payment_proof)
                        <div class="mt-4 pt-4 border-t border-dashed border-gray-200">
                            <p class="text-sm text-gray-600 mb-2">Bukti Pembayaran Anda:</p>
                            <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank" class="text-sm text-blue-600 hover:underline">Lihat Bukti Transfer</a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Card Detail Produk --}}
        <div class="bg-white rounded-lg shadow-sm border border-[var(--color-border)]">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-[var(--color-neutral-900)] mb-4">Detail Produk</h2>
                <div class="flex justify-between items-start py-3 border-b border-[var(--color-border)]">
                    <span class="text-sm text-[var(--color-neutral-600)]">Produk</span>
                    <div class="text-right">
                        <span class="text-sm font-medium text-[var(--color-neutral-800)]">{{ $order->product->name }}</span>
                        @if($order->product->country)
                            <span class="block text-xs text-[var(--color-neutral-500)]">{{ $order->product->country->name }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex justify-between py-3 border-b border-[var(--color-border)]">
                    <span class="text-sm text-[var(--color-neutral-600)]">Kuota</span>
                    <span class="text-sm font-medium text-[var(--color-neutral-800)]">
                        {{ $order->product->quota_amount }} {{ strtoupper($order->product->quota_type) }}
                    </span>
                </div>
                <div class="flex justify-between py-3">
                    <span class="text-sm text-[var(--color-neutral-600)]">Masa Aktif</span>
                    <span class="text-sm font-medium text-[var(--color-neutral-800)]">{{ $order->product->validity_days }} Hari</span>
                </div>
            </div>
        </div>

        {{-- Grid Pelanggan & Pembayaran --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Card Pelanggan --}}
            <div class="bg-white rounded-lg shadow-sm border border-[var(--color-border)]">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-[var(--color-neutral-900)] mb-4">Detail Pelanggan</h2>
                    <div class="space-y-3">
                        @if($order->customer_name)
                            <div class="flex justify-between">
                                <span class="text-sm text-[var(--color-neutral-600)]">Nama</span>
                                <span class="text-sm font-medium text-[var(--color-neutral-800)]">{{ $order->customer_name }}</span>
                            </div>
                        @endif
                        @if($order->customer_email)
                            <div class="flex justify-between">
                                <span class="text-sm text-[var(--color-neutral-600)]">Email</span>
                                <span class="text-sm font-medium text-[var(--color-neutral-800)]">{{ $order->customer_email }}</span>
                            </div>
                        @endif
                        @if($order->customer_phone)
                            <div class="flex justify-between">
                                <span class="text-sm text-[var(--color-neutral-600)]">Telepon</span>
                                <span class="text-sm font-medium text-[var(--color-neutral-800)]">{{ $order->customer_phone }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Card Rincian Pembayaran --}}
            <div class="bg-white rounded-lg shadow-sm border border-[var(--color-border)]">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-[var(--color-neutral-900)] mb-4">Rincian Pembayaran</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm text-[var(--color-neutral-700)]">
                            <span>Harga Asli</span>
                            <span>{{ Number::currency($order->original_price, 'IDR', 'id', 0) }}</span>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="flex justify-between text-sm text-[var(--color-success)]">
                                <span>Diskon</span>
                                <span>- {{ Number::currency($order->discount_amount, 'IDR', 'id', 0) }}</span>
                            </div>
                        @endif
                        @if($order->voucher)
                            <div class="flex justify-between text-sm text-[var(--color-success)]">
                                <span>Voucher</span>
                                <span class="font-medium">{{ $order->voucher->code }}</span>
                            </div>
                        @endif
                        <hr class="my-2 border-dashed border-[var(--color-border)]">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-[var(--color-neutral-900)]">Total Bayar</span>
                            <span class="text-xl font-bold text-[var(--color-primary-600)]">
                                {{ Number::currency($order->final_price, 'IDR', 'id', 0) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- QRIS Modal --}}
    <div x-show="showQrisModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-xl bg-opacity-75 p-4"
         style="display: none;">
        <div @click.outside="showQrisModal = false" class="relative w-full max-w-xs bg-white rounded-lg shadow-xl p-6">
            <button type="button" @click="showQrisModal = false" class="absolute -top-3 -right-3 p-1 bg-[var(--color-neutral-700)] text-white rounded-full hover:bg-[var(--color-neutral-900)] focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <h3 class="text-lg font-semibold text-center text-[var(--color-neutral-900)] mb-4">Scan untuk Membayar</h3>
            <img wire:poll src="{{ asset('storage/' . $qris?->file) }}" alt="QRIS Payment Code" class="w-full h-auto rounded-md border border-[var(--color-border)]">
            <p class="text-xs text-center text-[var(--color-neutral-600)] mt-4">Scan menggunakan aplikasi bank atau e-wallet Anda.</p>
        </div>
    </div>
</div>
