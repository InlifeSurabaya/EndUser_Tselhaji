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
{{--            <div class="w-full sm:w-32">--}}
{{--                <label for="filter-quota-amount" class="sr-only">Jumlah Kuota</label>--}}
{{--                <input--}}
{{--                    type="number"--}}
{{--                    id="filter-quota-amount"--}}
{{--                    wire:model.live.debounce.300ms="filterQuotaAmount"--}}
{{--                    placeholder="Jumlah"--}}
{{--                    class="[appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none p-3 block w-full text-sm rounded-lg border border-neutral-200 focus:border-accent-500 focus:ring-accent-500 transition"--}}
{{--                />--}}
{{--            </div>--}}

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
              <div wire:loading wire:target="filterCountry, filterQuotaAmount, filterQuotaType"
                   class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="animate-spin h-5 w-5 text-accent-600" ...>
                  ...
                </svg>
              </div>
            </div>
        </div>
    </div>
  {{-- Section button end --}}

  {{-- SECTION: REKOMENDASI PRODUK (Hanya muncul jika ada data) --}}
  {{-- SECTION: REKOMENDASI PRODUK --}}
  @if (!empty($recommendationProducts) && count($recommendationProducts) > 0)
    <div class="mb-12 border-b border-neutral-200 pb-8">

      {{-- Header Section --}}
      <div class="flex items-center gap-2 mb-4">
        <h2 class="text-2xl font-bold text-neutral-800">
          Rekomendasi Spesial Untuk Anda
        </h2>
        <span
          class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gradient-to-r from-primary-600 to-violet-600 text-white shadow-sm">
          <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path
              d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>
          </svg>
          Pilihan AI
        </span>
      </div>

      <p class="text-neutral-600 mb-6">
        Berdasarkan riwayat dan preferensi perjalanan Anda, kami menyarankan paket ini:
      </p>

      {{-- Grid Card --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach ($recommendationProducts as $product)
          <div wire:key="rec-{{ $product->id }}" wire:click="showProductDetail({{$product->id}})"
               class="group flex flex-col h-full bg-white border border-primary-200 shadow-sm rounded-xl transition-all duration-300 hover:border-primary-400 hover:shadow-lg hover:-translate-y-1 relative overflow-hidden cursor-pointer">

            {{-- Ribbon Recommendation --}}
            <div class="absolute top-0 right-0 z-10">
              <div class="bg-primary-600 text-white text-[10px] font-bold px-3 py-1 rounded-bl-lg shadow-sm">
                REKOMENDASI
              </div>
            </div>

            {{-- Card Header --}}
            <div
              class="p-4 md:p-5 flex justify-between items-center border-b border-neutral-100 bg-primary-50/30 rounded-t-xl">
              <div class="flex items-center gap-2">
                @if($product->country->country_code)
                  <span class="fi fi-{{ strtolower($product->country->country_code) }}"></span>
                @else
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-neutral-500" fill="none"
                       viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9V3m-9 9h18"/>
                  </svg>
                @endif
                <span class="text-sm font-semibold text-neutral-700">{{ $product->country->name ?? 'Global' }}</span>
              </div>
            </div>

            {{-- Card Body --}}
            <div class="p-4 md:p-5 flex-grow text-center">
              <p class="font-sans font-extrabold text-primary-700">
                <span class="text-5xl">{{ $product->quota_amount }}</span>
                <span class="text-2xl text-neutral-600">{{ $product->quota_type }}</span>
              </p>
              <h3 class="mt-2 text-lg font-bold text-neutral-800 group-hover:text-primary-700">
                {{ $product->name }}
              </h3>
              <p class="mt-1 text-sm text-neutral-600">
                Masa Aktif {{ $product->validity_days }} Hari
              </p>
            </div>

            {{-- Card Footer (Logic Harga Spesial) --}}
            <div class="p-4 md:p-5 bg-neutral-50 rounded-b-xl border-t border-neutral-200 text-center">

              @if ($specialDiscount > 0)
                {{-- TAMPILAN JIKA ADA DISKON SPESIAL --}}
                <div class="flex justify-center items-center gap-2">
                  <span class="text-lg font-medium text-neutral-400 line-through">
                    {{ Number::currency($product->price, 'IDR', 'id', 0) }}
                  </span>
                  <span
                    class="inline-flex items-center gap-x-1 py-1 px-2 rounded-md text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                    <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                      <path
                        d="M11.251.068a.5.5 0 0 1 .227.58L9.677 6.5H13a.5.5 0 0 1 .364.843l-8 8.5a.5.5 0 0 1-.842-.49L6.323 9.5H3a.5.5 0 0 1-.364-.843l8-8.5a.5.5 0 0 1 .615-.09z"/>
                    </svg>
                    HEMAT {{ $specialDiscount }}%
                  </span>
                </div>
                <p class="text-2xl font-bold text-primary-600 mt-1">
                  {{ Number::currency($product->price - ($product->price * $specialDiscount / 100), 'IDR', 'id', 0) }}
                </p>
                <p class="text-[10px] text-primary-600 font-medium mt-1">
                  Penawaran khusus segment Anda!
                </p>
              @else
                {{-- TAMPILAN HARGA NORMAL --}}
                <p class="text-2xl font-bold text-primary-600">
                  {{ Number::currency($product->price, 'IDR', 'id', 0) }}
                </p>
              @endif

              <div class="mt-4">
                <button type="button" wire:click="newOrder({{$product->id}}, {{ $specialDiscount }})"
                        class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-gradient-to-r from-primary-600 to-violet-600 text-white hover:from-primary-700 hover:to-violet-700 shadow-md hover:shadow-lg transition-all">
                  <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                       stroke="currentColor" stroke-width="2">
                    <path
                      d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                  </svg>
                  Ambil Promo Spesial
                </button>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

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
                    wire:click="newOrder({{ $selectedProduct->id }})"
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
