@php
    // Logika untuk menentukan warna badge status
    $statusText = '';
    $statusColorClasses = '';

    switch ($order->status) {
        case 'pending':
            $statusText = 'Menunggu Pembayaran';
            // Menggunakan warna kuning/oranye (warning)
            // (Kita gunakan proxy warna yang ada atau Tailwind default jika --color-warning- light tidak ada)
            $statusColorClasses = 'bg-yellow-100 text-yellow-800';
            break;
        case 'settlement':
            $statusText = 'Pembayaran Berhasil';
            // Menggunakan warna hijau (success)
            $statusColorClasses = 'bg-green-100 text-green-800';
            break;
        case 'cancel':
            $statusText = 'Dibatalkan';
            // Menggunakan warna merah (error)
            $statusColorClasses = 'bg-[var(--color-primary-50)] text-[var(--color-primary-700)]';
            break;
        case 'expire':
            $statusText = 'Kedaluwarsa';
            $statusColorClasses = 'bg-[var(--color-primary-50)] text-[var(--color-primary-700)]';
            break;
        case 'failure':
            $statusText = 'Pembayaran Gagal';
            $statusColorClasses = 'bg-[var(--color-primary-50)] text-[var(--color-primary-700)]';
            break;
        default:
            $statusText = strtoupper($order->status);
            $statusColorClasses = 'bg-[var(--color-neutral-100)] text-[var(--color-neutral-700)]';
    }
@endphp

<div class="max-w-3xl mx-auto my-7 px-4 sm:px-6 lg:px-8">
    <div class="space-y-6">

        @if ($order->status == 'pending')
            <div class="bg-white rounded-lg shadow-sm border border-[var(--color-border)]">
                <div class="p-6 text-center">

                    <span
                        class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium {{ $statusColorClasses }}">
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
                                <svg class="size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
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

                    <div
                        class="mt-6 p-4 rounded-lg bg-[var(--color-primary-50)] border border-[var(--color-primary-200)]">
                        <p class="text-sm font-medium text-[var(--color-primary-800)]">
                            Segera bayar sebelum:
                        </p>
                        <p class="text-lg font-bold text-[var(--color-primary-700)]">
                            {{ \Carbon\Carbon::parse($order->expired_at)->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }}
                        </p>
                    </div>


                    <button type="button" wire:click="createTransactionMidtrans({{$order->id}})"
                            class="w-full mt-6 py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-[var(--color-primary-600)] text-white hover:bg-[var(--color-primary-700)]">
                        @if($order->url_midtrans)
                            Bayar Sekarang
                        @else
                            Lanjutkan Pembayaran
                        @endif
                    </button>
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm border border-[var(--color-border)]">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-[var(--color-neutral-900)]">Status Pesanan</h2>
                            <p class="text-sm text-[var(--color-neutral-600)]">
                                Nomor Order:
                                <span
                                    class="font-medium text-[var(--color-neutral-800)]">{{ $order->order_number }}</span>
                            </p>
                        </div>
                        <div class="mt-3 sm:mt-0">
                            <span
                                class="inline-flex items-center gap-x-1.5 py-2 px-4 rounded-lg text-sm font-medium {{ $statusColorClasses }}">
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>
                    @if ($order->status == 'settlement' && $order->settlement_time)
                        <p class="mt-4 text-sm text-green-700">
                            Pembayaran telah dikonfirmasi pada:
                            <span
                                class="font-medium">{{ \Carbon\Carbon::parse($order->settlement_time)->isoFormat('D MMMM YYYY, HH:mm') }}</span>
                        </p>
                    @endif
                </div>
            </div>
        @endif


        <div class="bg-white rounded-lg shadow-sm border border-[var(--color-border)]">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-[var(--color-neutral-900)] mb-4">Detail Produk</h2>

                <div class="flex justify-between items-start py-3 border-b border-[var(--color-border)]">
                    <span class="text-sm text-[var(--color-neutral-600)]">Produk</span>
                    <div class="text-right">
                        <span class="text-sm font-medium text-[var(--color-neutral-800)]">
                            {{ $order->product->name }}
                        </span>
                        @if($order->product->country)
                            <span class="block text-xs text-[var(--color-neutral-500)]">
                                {{ $order->product->country->name }}
                            </span>
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
                    <span class="text-sm font-medium text-[var(--color-neutral-800)]">
                        {{ $order->product->validity_days }} Hari
                    </span>
                </div>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-white rounded-lg shadow-sm border border-[var(--color-border)]">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-[var(--color-neutral-900)] mb-4">Detail Pelanggan</h2>


                    <div class="space-y-3">
                        @if($order->customer_name)
                            <div class="flex justify-between">
                                <span class="text-sm text-[var(--color-neutral-600)]">Nama</span>
                                <span
                                    class="text-sm font-medium text-[var(--color-neutral-800)]">{{ $order->customer_name ?? '' }}</span>
                            </div>
                        @endif

                        @if($order->customer_email)
                            <div class="flex justify-between">
                                <span class="text-sm text-[var(--color-neutral-600)]">Email</span>
                                <span
                                    class="text-sm font-medium text-[var(--color-neutral-800)]">{{ $order->customer_email }}</span>
                            </div>
                        @endif

                        @if($order->customer_phone)
                            <div class="flex justify-between">
                                <span class="text-sm text-[var(--color-neutral-600)]">Telepon</span>
                                <span
                                    class="text-sm font-medium text-[var(--color-neutral-800)]">{{ $order->customer_phone }}</span>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

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
</div>
