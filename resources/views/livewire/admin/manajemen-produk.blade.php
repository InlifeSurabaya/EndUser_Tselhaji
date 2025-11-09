<div x-data="{ tambahProdukModal: false, showEditModal: false }"
     @close-modal.window="
        tambahProdukModal = false,
        showEditModal = false
     "
     class="p-4 sm:p-6 lg:p-8 space-y-6 bg-neutral-50 min-h-screen">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900">Manajemen Produk</h1>
            <p class="text-sm text-neutral-600">Tambah, edit, atau hapus produk kuota digital.</p>
        </div>

        <div class="mt-4 sm:mt-0">
            @role(\App\Enum\RoleEnum::SUPER_ADMIN->value)
            <button @click="tambahProdukModal = ! tambahProdukModal" type="button"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 transition-all">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Tambah Produk
            </button>
            @endrole
        </div>
    </div>

    {{-- Action section --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">

        {{-- Per Page (kolom 3/12) --}}
        <div class="md:col-span-3 flex items-center gap-2">
            <label for="perPage" class="text-sm text-neutral-700 shrink-0">Tampilkan</label>

            <select id="perPage" wire:model.live="perPage"
                    class="h-10 px-3 bg-white border border-neutral-300 rounded-lg text-sm focus:border-accent-500 focus:ring-accent-500">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="50">50</option>
            </select>

            <span class="text-sm text-neutral-700 shrink-0">entri</span>
        </div>

        {{-- Search (kolom 6/12) --}}
        <div class="md:col-span-6 relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18a7.5 7.5 0 006.15-3.35z"/>
                </svg>
            </span>

            <input
                wire:model.live="nameItem"
                type="text"
                class="h-10 pl-10 pr-10 block w-full border border-neutral-300 rounded-lg text-sm
                   focus:border-blue-500 focus:ring-blue-500"
                placeholder="Cari produk…"
            >

            @if ($nameItem)
                <button
                    wire:click="$set('nameItem', '')"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-neutral-400 hover:text-neutral-700">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            @endif
        </div>

        {{-- Country Filter (kolom 3/12) --}}
        <div class="md:col-span-3">
            <select wire:model.live="countryFilter"
                    class="h-10 px-3 bg-white border border-neutral-300 rounded-lg text-sm focus:border-accent-500 focus:ring-accent-500 w-full">
                <option value="">Semua Negara</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                @endforeach
            </select>
        </div>
    </div>




    {{-- Table start --}}
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class="border border-neutral-200 rounded-lg shadow-sm overflow-hidden bg-white">
                    <table class="min-w-full divide-y divide-neutral-200">
                        <thead class="bg-neutral-100">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold text-neutral-600 uppercase">Nama Produk
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold text-neutral-600 uppercase">Negara
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold text-neutral-600 uppercase">Harga
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold text-neutral-600 uppercase">Kuota
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold text-neutral-600 uppercase">Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-semibold text-neutral-600 uppercase">Aksi
                            </th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200">
                        @forelse ($products as $product)
                            <tr wire:key="{{ $product->id }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">{{ $product->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">
                                    <span class="fi fi-{{ $product->country->country_code }} mr-2"></span>
                                    {{ $product->country->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">{{ Number::currency($product->price, 'IDR', 'id', 0) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">{{ $product->quota_amount }}
                                    GB
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if ($product->is_active)
                                        <span
                                            class="inline-flex items-center gap-1.5 py-1 px-2 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 py-1 px-2 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right space-x-2">
                                    @role(\App\Enum\RoleEnum::SUPER_ADMIN->value)
                                    <button @click="showEditModal = ! showEditModal"
                                            wire:click="getProduct({{ $product->id }})" type="button"
                                            wire:loading.attr="disabled"
                                            wire:target="getProduct({{ $product->id }})"
                                            class="font-semibold text-accent-600 hover:text-accent-700">Edit
                                    </button>
                                    <button wire:click="deleteProductAlert({{ $product->id }})" type="button"
                                            class="font-semibold text-primary-600 hover:text-primary-700">Hapus
                                    </button>
                                    @endrole
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-neutral-500">Tidak ada data
                                    produk ditemukan.
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


    {{-- Pagination start --}}
    <div class="mt-4">
        {{ $products->links() }}
    </div>


    {{-- Modal tambah produk start --}}
    <div
        x-cloak
        x-show="tambahProdukModal"
        @keydown.escape.window="tambahProdukModal = false"
        class="fixed inset-0 z-50 flex items-center justify-center"

        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-neutral-900/50 backdrop-blur-sm"></div>

        <div
            class="bg-white rounded-2xl shadow-xl w-full max-w-lg relative"
            @click.outside="tambahProdukModal = true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <div class="flex items-center justify-between p-6 border-b border-neutral-200">
                <h2 class="text-xl font-semibold text-neutral-800">
                    {{-- Ini akan dinamis jika kamu menambahkan logika edit --}}
                    Tambah Produk Baru
                </h2>
                <button @click="tambahProdukModal = false"
                        class="text-neutral-500 hover:text-primary-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="saveProduct">
                <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                    <div>
                        <label for="name" class="block text-sm font-medium text-neutral-800 mb-1">Nama Produk</label>
                        <input type="text" id="name" wire:model.defer="name"
                               class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition"
                               placeholder="Contoh: Kuota Internet 10GB">
                        @error('name') <p class="text-xs text-primary-600 mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="detail" class="block text-sm font-medium text-neutral-800 mb-1">Detail
                            (Opsional)</label>
                        <textarea id="detail" wire:model.defer="detail" rows="2"
                                  class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition"
                                  placeholder="Deskripsi singkat produk..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="quota_amount" class="block text-sm font-medium text-neutral-800 mb-1">Kuota
                                (GB)</label>
                            <input type="number" id="quota_amount" wire:model.defer="quota_amount"
                                   class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition"
                                   placeholder="10">
                            @error('quota_amount') <p
                                class="text-xs text-primary-600 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="quota_type" class="block text-sm font-medium text-neutral-800 mb-1">Jenis
                                Kuota</label>
                            <select id="quota_type" wire:model.defer="quota_type"
                                    class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition">
                                <option value="">Pilih</option>
                                <option value="gb">GB</option>
                                <option value="mb">MB</option>
                            </select>
                            @error('quota_type') <p class="text-xs text-primary-600 mt-1.5">{{ $message }}</p @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-neutral-800 mb-1">Harga
                                (IDR)</label>
                            <input type="number" id="price" wire:model.defer="price"
                                   class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition"
                                   placeholder="50000">
                            @error('price') <p class="text-xs text-primary-600 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="discount" class="block text-sm font-medium text-neutral-800 mb-1">Diskon
                                (%)</label>
                            <input type="number" id="discount" wire:model.defer="discount"
                                   class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition"
                                   placeholder="0" value="0">
                            @error('discount') <p class="text-xs text-primary-600 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="validity_days" class="block text-sm font-medium text-neutral-800 mb-1">Masa
                                Berlaku (hari)</label>
                            <input type="number" id="validity_days" wire:model.defer="validity_days"
                                   class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition"
                                   placeholder="30">
                            @error('validity_days') <p
                                class="text-xs text-primary-600 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="country_id"
                                   class="block text-sm font-medium text-neutral-800 mb-1">Negara</label>
                            <select id="country_id" wire:model.defer="country_id"
                                    class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition">
                                <option value="">Pilih negara</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                            @error('country_id') <p class="text-xs text-primary-600 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div x-data="{ enabled: @entangle('is_active') }" class="flex items-center justify-between">
                        <span class="text-sm font-medium text-neutral-800">Aktifkan produk</span>
                        <button
                            type="button"
                            @click="enabled = !enabled"
                            :class="enabled ? 'bg-accent-600' : 'bg-neutral-200'"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2"
                        >
                            <span class="sr-only">Aktifkan produk</span>
                            <span
                                :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                            ></span>
                        </button>
                    </div>
                </div>

                <div class="flex justify-end gap-3 p-6 border-t border-neutral-200 bg-neutral-50 rounded-b-2xl">
                    <button type="button" @click="tambahProdukModal = false"
                            class="px-4 py-2 text-sm font-semibold rounded-lg text-neutral-700 bg-neutral-100 hover:bg-neutral-200 focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 transition-colors">
                        Batal
                    </button>

                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-75 cursor-wait"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-accent-600 rounded-lg hover:bg-accent-700 focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 transition-colors">

                        <svg wire:loading wire:target="saveProduct" class="animate-spin -ml-1 h-4 w-4 text-white"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="saveProduct">Simpan Produk</span>
                        <span wire:loading wire:target="saveProduct">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- Modal tambah produk end --}}

    {{-- Modal edit produk start --}}
    <div
        x-cloak
        x-show="showEditModal"
        @keydown.escape.window="showEditModal = false"
        class="fixed inset-0 z-50 flex items-center justify-center"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-neutral-900/50 backdrop-blur-sm"></div>

        <div
            class="bg-white rounded-2xl shadow-xl w-full max-w-lg relative overflow-hidden"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <div class="flex items-center justify-between p-6 border-b border-neutral-200">
                <h2 class="text-xl font-semibold text-neutral-800">
                    Edit Produk
                </h2>
                <button @click="showEditModal = false"
                        class="text-neutral-500 hover:text-primary-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="updateProduct">
                <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">

                    <div wire:loading.block wire:target="getProduct" class="animate-pulse space-y-5">

                        <div>
                            <div class="h-4 bg-neutral-200 rounded w-1/3 mb-1.5"></div>
                            <div class="h-10 bg-neutral-200 rounded w-full"></div>
                        </div>

                        <div>
                            <div class="h-4 bg-neutral-200 rounded w-1/4 mb-1.5"></div>
                            <div class="h-16 bg-neutral-200 rounded w-full"></div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="h-4 bg-neutral-200 rounded w-1/2 mb-1.5"></div>
                                <div class="h-10 bg-neutral-200 rounded w-full"></div>
                            </div>
                            <div>
                                <div class="h-4 bg-neutral-200 rounded w-1/2 mb-1.5"></div>
                                <div class="h-10 bg-neutral-200 rounded w-full"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="h-4 bg-neutral-200 rounded w-1/2 mb-1.5"></div>
                                <div class="h-10 bg-neutral-200 rounded w-full"></div>
                            </div>
                            <div>
                                <div class="h-4 bg-neutral-200 rounded w-1/2 mb-1.5"></div>
                                <div class="h-10 bg-neutral-200 rounded w-full"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="h-4 bg-neutral-200 rounded w-1/2 mb-1.5"></div>
                                <div class="h-10 bg-neutral-200 rounded w-full"></div>
                            </div>
                            <div>
                                <div class="h-4 bg-neutral-200 rounded w-1/2 mb-1.5"></div>
                                <div class="h-10 bg-neutral-200 rounded w-full"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="h-4 bg-neutral-200 rounded w-1/3"></div>
                            <div class="h-6 w-11 bg-neutral-200 rounded-full"></div>
                        </div>

                    </div>

                    <div wire:loading.remove wire:target="getProduct" class="space-y-5">

                        <div>
                            <label for="edit_name" class="block text-sm font-medium text-neutral-800 mb-1">Nama
                                Produk</label>
                            <input type="text" id="edit_name" wire:model.defer="nameDetailProduct"
                                   class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition"
                                   placeholder="Contoh: Kuota Internet 10GB">
                            @error('name') <p class="text-xs text-primary-600 mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="edit_detail" class="block text-sm font-medium text-neutral-800 mb-1">Detail
                                (Opsional)</label>
                            <textarea id="edit_detail" wire:model.defer="detailProduct" rows="2"
                                      class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition"
                                      placeholder="Deskripsi singkat produk..."></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="edit_quota_amount" class="block text-sm font-medium text-neutral-800 mb-1">Kuota
                                    (GB)</label>
                                <input type="number" id="edit_quota_amount" wire:model.defer="quota_amountDetailProduct"
                                       class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition"
                                       placeholder="10">
                                @error('quota_amount') <p
                                    class="text-xs text-primary-600 mt-1.5">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="edit_quota_type" class="block text-sm font-medium text-neutral-800 mb-1">Jenis
                                    Kuota</label>
                                <select id="edit_quota_type" wire:model.defer="quota_typeDetailProduct"
                                        class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition">
                                    <option value="">Pilih</option>
                                    <option value="gb">GB</option>
                                    <option value="mb">MB</option>
                                </select>
                                @error('quota_type') <p
                                    class="text-xs text-primary-600 mt-1.5">{{ $message }}</p @enderror
                            </div>

                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="edit_price" class="block text-sm font-medium text-neutral-800 mb-1">Harga
                                    (IDR)</label>
                                <input type="number" id="edit_price" wire:model.defer="priceDetailProduct"
                                       class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition"
                                       placeholder="50000">
                                @error('price') <p class="text-xs text-primary-600 mt-1.5">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="edit_discount" class="block text-sm font-medium text-neutral-800 mb-1">Diskon
                                    (%)</label>
                                <input type="number" id="edit_discount" wire:model.defer="discountDetailProduct"
                                       class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition"
                                       placeholder="0" value="0">
                                @error('discount') <p
                                    class="text-xs text-primary-600 mt-1.5">{{ $message }}</p> @enderror
                            </div>

                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="edit_validity_days" class="block text-sm font-medium text-neutral-800 mb-1">Masa
                                    Berlaku (hari)</label>
                                <input type="number" id="edit_validity_days"
                                       wire:model.defer="validity_daysDetailProduct"
                                       class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition"
                                       placeholder="30">
                                @error('validity_days') <p
                                    class="text-xs text-primary-600 mt-1.5">{{ $message }}</p> @enderror

                            </div>

                            <div>
                                <label for="edit_country_id"
                                       class="block text-sm font-medium text-neutral-800 mb-1">Negara</label>
                                <select id="edit_country_id" wire:model.defer="country_idDetailProduct"
                                        class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition">
                                    <option value="">Pilih negara</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                @error('country_id') <p
                                    class="text-xs text-primary-600 mt-1.5">{{ $message }}</p> @enderror
                            </div>

                        </div>

                        <div x-data="{ enabled: @entangle('is_activeDetailProduct') }"
                             class="flex items-center justify-between">
                            <span class="text-sm font-medium text-neutral-800">Aktifkan produk</span>
                            <button
                                type="button"
                                @click="enabled = !enabled"
                                :class="enabled ? 'bg-accent-600' : 'bg-neutral-200'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2"
                            >

                                <span class="sr-only">Aktifkan produk</span>
                                <span
                                    :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                ></span>
                            </button>

                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-6 border-t border-neutral-200 bg-neutral-50 rounded-b-2xl">
                    <button type="button" @click="showEditModal = false"
                            class="px-4 py-2 text-sm font-semibold rounded-lg text-neutral-700 bg-neutral-100 hover:bg-neutral-200 focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 transition-colors">
                        Batal
                    </button>


                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-75 cursor-wait"
                            wire:target="updateProduct"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-accent-600 rounded-lg hover:bg-accent-700 focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 transition-colors">

                        <svg wire:loading wire:target="updateProduct" class="animate-spin -ml-1 h-4 w-4 text-white"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>

                        <span wire:loading.remove wire:target="updateProduct">Update Produk</span>
                        <span wire:loading wire:target="updateProduct">Mengupdate...</span>
                    </button>
                </div>

            </form>

        </div>

    </div>
    {{-- Modal edit produk end --}}

</div>
