<div class="max-w-7xl mx-auto my-7">

    {{-- Section button start --}}
    <div class="mb-6 grid grid-rows-2 gap-1 md:gap-2">
        <div>
            <h2 class="text-2xl font-bold text-neutral-800">
                Pilih Paket Kuota
            </h2>
            <p class="text-neutral-600 mt-1">
                Temukan paket yang paling sesuai untuk Anda.
            </p>
        </div>

        <div class="w-full sm:w-auto flex flex-col sm:flex-row gap-4">

            {{-- Filter Kuota (Jumlah) --}}
            <div class="w-full sm:w-32">
                <label for="filter-quota-amount" class="sr-only">Jumlah Kuota</label>
                <input
                    type="number"
                    id="filter-quota-amount"
                    wire:model.live.debounce.300ms="filterQuotaAmount"
                    placeholder="Jumlah"
                    class="[appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none p-3 block w-full text-sm rounded-lg border border-neutral-200 focus:border-accent-500 focus:ring-accent-500 transition"
                />
            </div>

            {{-- Filter Kuota (Tipe) --}}
            <div class="w-full sm:w-32 relative z-20">
                <label for="filter-quota-type" class="sr-only">Tipe Kuota</label>
                <select
                    id="filter-quota-type"
                    wire:model.live="filterQuotaType"
                    class="p-3 pr-10 block w-full text-sm rounded-lg border border-neutral-200 focus:border-accent-500 focus:ring-accent-500 transition"
                >
                    <option value="">Tipe</option>
                    <option value="gb">GB</option>
                    <option value="mb">MB</option>
                </select>
            </div>

            {{-- Filter Dropdown (Negara) --}}
            <div class="w-full sm:min-w-[200px] relative z-10">
                <label for="filter-country" class="sr-only">Filter Berdasarkan Negara</label>
                <select
                    id="filter-country"
                    wire:model.live="filterCountry"
                    class="p-3 pr-10 block w-full text-sm rounded-lg border border-neutral-200 focus:border-accent-500 focus:ring-accent-500 transition"
                >
                    <option value="">Semua Negara</option>
                    @foreach ($countries ?? [] as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>

                {{-- Indikator Loading --}}
                <div wire:loading wire:target="filterCountry, filterQuotaAmount, filterQuotaType" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="animate-spin h-5 w-5 text-accent-600" ... >
                        ...
                    </svg>
                </div>
            </div>
        </div>
    </div>
    {{-- Section button end --}}

    {{-- Grid Container untuk Card Produk --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 ">
        @forelse ($products as $product)
            {{-- Awal dari card produk --}}
            <div wire:key="{{ $product->id }}" wire:click="showProductDetail({{$product->id}})"
                 class="group flex flex-col h-full bg-white border border-neutral-200 shadow-sm rounded-xl transition-all duration-300 hover:border-primary-300 hover:shadow-lg hover:-translate-y-1">

                {{-- Bagian Header Kartu dengan Badge Negara --}}
                <div
                    class="p-4 md:p-5 flex justify-between items-center border-b border-neutral-200 bg-neutral-50 rounded-t-xl">
                    {{-- Icon Globe --}}
                    <div class="flex items-center gap-2">
                        @if($product->country->country_code)
                            <span class="fi fi-{{ strtolower($product->country->country_code)  }}"></span>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-neutral-500" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9V3m-9 9h18"/>
                            </svg>
                        @endif
                        <span
                            class="text-sm font-semibold text-neutral-700">{{ $product->country->name ?? 'Global' }}</span>
                    </div>
                    {{-- Badge Best Seller --}}
                    @if ($loop->first)
                        <span
                            class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                            Best
                            Seller
                        </span>
                    @endif
                </div>

                {{-- Bagian Body Kartu --}}
                <div class="p-4 md:p-5 flex-grow">
                    <div class="text-center">
                        {{-- Jumlah Kuota --}}
                        <p class="font-sans font-extrabold text-primary-600">
                            <span class="text-5xl">{{ $product->quota_amount }}</span>
                            <span class="text-2xl text-neutral-600">{{ $product->quota_type }}</span>
                        </p>
                        {{-- Nama Paket --}}
                        <h3 class="mt-2 text-lg font-bold text-neutral-800 group-hover:text-primary-700">
                            {{ $product->name }}
                        </h3>
                        {{-- Masa Aktif --}}
                        <p class="mt-1 text-sm text-neutral-600">
                            Masa Aktif {{ $product->validity_days }} Hari
                        </p>
                    </div>
                </div>

                {{-- Bagian Footer Kartu (Harga & Tombol) --}}
                <div class="p-4 md:p-5 bg-neutral-50 rounded-b-xl border-t border-neutral-200">
                    <div class="text-center">
                        @if ($product->discount > 0)
                            {{-- Tampilan jika ada diskon --}}
                            <div class="flex justify-center items-center gap-2">
                                <span
                                    class="text-lg font-medium text-neutral-400 line-through">
                                    {{ Number::currency($product->price, 'IDR', 'id', 0) }}
                                </span>
                                <span
                                    class="inline-flex items-center gap-x-1 py-1 px-2 rounded-md text-xs font-medium bg-red-100 text-red-800">
                                    {{ $product->discount }}% OFF
                                </span>
                            </div>
                            <p class="text-2xl font-bold text-primary-600 mt-1">
                                {{ Number::currency($product->price - ($product->price * $product->discount) / 100, 'IDR', 'id', 0) }}
                            </p>
                        @else
                            {{-- Tampilan harga normal --}}
                            <p class="text-2xl font-bold text-primary-600">
                                {{ Number::currency($product->price, 'IDR', 'id', 0) }}
                            </p>
                        @endif
                    </div>

                    {{-- Tombol Beli --}}
                    <div class="mt-4">
                        <button type="button"
                                wire:click="newOrder({{$product->id}})"
                                class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50 disabled:pointer-events-none">
                            Beli Sekarang
                        </button>
                    </div>
                </div>
            </div>
            {{-- Akhir dari card produk --}}
        @empty
            {{-- Tampilan jika tidak ada produk --}}
            <div class="col-span-full text-center py-12">
                <p class="text-neutral-600">Oops! Produk belum tersedia saat ini.</p>
            </div>
        @endforelse
    </div>
    {{-- Grid Container untuk Card Produk --}}


    {{-- Link Paginasi --}}
    <div class="mt-8 ">
        {{ $products->links() }}
    </div>

    {{-- MODAL DETAIL PRODUK --}}
    @if ($selectedProduct)
        <div x-data="{ show: @entangle('showModal').live }"
             x-show="show"
             x-cloak
             @keydown.escape.window="show = false"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">

            {{-- Backdrop --}}
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="show = false"
                 class="fixed inset-0 backdrop-blur-sm bg-opacity-50"></div>

            {{-- Modal Container --}}
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     @click.stop
                     class="relative w-full max-w-lg bg-white rounded-xl shadow-xl">

                    {{-- Header Modal --}}
                    <div class="flex justify-between items-center py-3 px-4 border-b border-neutral-200">
                        <h3 class="font-bold text-neutral-800">
                            Detail Paket
                        </h3>
                        <button type="button"
                                @click="show = false"
                                class="flex justify-center items-center w-7 h-7 text-sm font-semibold rounded-full border border-transparent text-neutral-700 hover:bg-neutral-100">
                            <span class="sr-only">Close</span>
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Body Modal --}}
                    <div class="p-4 overflow-y-auto max-h-[60vh]">
                        <div class="text-center mb-4">
                            <p class="font-sans font-extrabold text-primary-600">
                                <span class="text-6xl">{{ $selectedProduct->quota_amount }}</span>
                                <span class="text-3xl text-neutral-600">{{ $selectedProduct->quota_type }}</span>
                            </p>
                            <h3 class="mt-2 text-2xl font-bold text-neutral-800">
                                {{ $selectedProduct->name }}
                            </h3>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-neutral-600">Negara:</span>
                                <span
                                    class="font-semibold text-neutral-800">{{ $selectedProduct->country->name ?? 'Global' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-neutral-600">Masa Aktif:</span>
                                <span class="font-semibold text-neutral-800">{{ $selectedProduct->validity_days }}
                                    Hari
                                </span>
                            </div>
                            <hr class="border-neutral-200">
                            <p class="text-neutral-700">
                                {{ $selectedProduct->detail }}
                            </p>
                            <hr class="border-neutral-200">
                            <div class="text-center">
                                @if ($selectedProduct->discount > 0)
                                    <div class="flex justify-center items-center gap-2">
                                        <span class="text-xl font-medium text-neutral-400 line-through">
                                            {{ Number::currency($selectedProduct->price, 'IDR', 'id', 0) }}
                                        </span>
                                        <span
                                            class="inline-flex items-center gap-x-1 py-1 px-2 rounded-md text-xs font-medium bg-red-100 text-red-800">
                                            {{ $selectedProduct->discount }}% OFF
                                        </span>
                                    </div>
                                    <p class="text-3xl font-bold text-primary-600 mt-1">
                                        {{ Number::currency($selectedProduct->price - ($selectedProduct->price * $selectedProduct->discount) / 100, 'IDR', 'id', 0) }}
                                    </p>
                                @else
                                    <p class="text-3xl font-bold text-primary-600">
                                        {{ Number::currency($selectedProduct->price, 'IDR', 'id', 0) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Footer Modal --}}
                    <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-neutral-200">
                        <button type="button"
                                @click="show = false"
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-neutral-200 bg-white text-neutral-800 shadow-sm hover:bg-neutral-50">
                            Batal
                        </button>
                        <button type="button"
                                wire:click="newOrder({{$product->id}})"
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-primary-600 text-white hover:bg-primary-700">
                            Beli Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- MODAL DETAIL PRODUK --}}

</div>
