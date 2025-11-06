<div x-data="{ open: false }" class="p-4 sm:p-6 lg:p-8 space-y-6 bg-neutral-50 min-h-screen">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900">Manajemen Produk</h1>
            <p class="text-sm text-neutral-600">Tambah, edit, atau hapus produk kuota digital.</p>
        </div>

        <div class="mt-4 sm:mt-0">
            <button  @click="open = ! open" type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 transition-all">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Produk
            </button>
        </div>
    </div>

    <div class="flex items-center justify-between">
        {{-- Button Tampilan Per page --}}
        <div class="flex items-center gap-2">
            <label for="perPage" class="text-sm text-neutral-700">Tampilkan</label>
            <select id="perPage" wire:model.live="perPage"
                    class="py-2 px-3 pe-9 block w-auto bg-white border border-neutral-300 rounded-lg text-sm focus:border-accent-500 focus:ring-accent-500">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="50">50</option>
            </select>
            <span class="text-sm text-neutral-700">entri</span>
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-600 uppercase">Nama Produk</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-600 uppercase">Negara</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-600 uppercase">Harga</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-600 uppercase">Kuota</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-neutral-600 uppercase">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-neutral-600 uppercase">Aksi</th>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">{{ $product->quota_amount }} GB</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if ($product->is_active)
                                        <span class="inline-flex items-center gap-1.5 py-1 px-2 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 py-1 px-2 rounded-full text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right space-x-2">
                                    <button wire:click="edit({{ $product->id }})" type="button" class="font-semibold text-accent-600 hover:text-accent-700">Edit</button>
                                    <button wire:click="confirmDelete({{ $product->id }})" type="button" class="font-semibold text-primary-600 hover:text-primary-700">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-neutral-500">Tidak ada data produk ditemukan.</td>
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
        x-show="open"
        @keydown.escape.window="open = false"
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
            @click.outside="open = false"
            class="bg-white rounded-2xl shadow-xl w-full max-w-lg relative"

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
                <button @click="open = false" class="text-neutral-500 hover:text-primary-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
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
                        <label for="detail" class="block text-sm font-medium text-neutral-800 mb-1">Detail (Opsional)</label>
                        <textarea id="detail" wire:model.defer="detail" rows="2"
                                  class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition"
                                  placeholder="Deskripsi singkat produk..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="quota_amount" class="block text-sm font-medium text-neutral-800 mb-1">Kuota (GB)</label>
                            <input type="number" id="quota_amount" wire:model.defer="quota_amount"
                                   class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition"
                                   placeholder="10">
                            @error('quota_amount') <p class="text-xs text-primary-600 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="quota_type" class="block text-sm font-medium text-neutral-800 mb-1">Jenis Kuota</label>
                            <select id="quota_type" wire:model.defer="quota_type"
                                    class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition">
                                <option value="">Pilih</option>
                                <option value="data">Data</option>
                                <option value="voice">Voice</option>
                                <option value="sms">SMS</option>
                            </select>
                            @error('quota_type') <p class="text-xs text-primary-600 mt-1.5">{{ $message }}</p @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-neutral-800 mb-1">Harga (IDR)</label>
                            <input type="number" id="price" wire:model.defer="price"
                                   class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition"
                                   placeholder="50000">
                            @error('price') <p class="text-xs text-primary-600 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="discount" class="block text-sm font-medium text-neutral-800 mb-1">Diskon (%)</label>
                            <input type="number" id="discount" wire:model.defer="discount"
                                   class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition"
                                   placeholder="0" value="0">
                            @error('discount') <p class="text-xs text-primary-600 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="validity_days" class="block text-sm font-medium text-neutral-800 mb-1">Masa Berlaku (hari)</label>
                            <input type="number" id="validity_days" wire:model.defer="validity_days"
                                   class="p-3 block w-full text-sm rounded-lg border-transparent bg-neutral-100 focus:bg-white focus:border-accent-500 focus:ring-accent-500 transition"
                                   placeholder="30">
                            @error('validity_days') <p class="text-xs text-primary-600 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="country_id" class="block text-sm font-medium text-neutral-800 mb-1">Negara</label>
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
                    <button type="button" @click="open = false"
                            class="px-4 py-2 text-sm font-semibold rounded-lg text-neutral-700 bg-neutral-100 hover:bg-neutral-200 focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 transition-colors">
                        Batal
                    </button>

                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-75 cursor-wait"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-accent-600 rounded-lg hover:bg-accent-700 focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 transition-colors">

                        <svg wire:loading wire:target="saveProduct" class="animate-spin -ml-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="saveProduct">Simpan Produk</span>
                        <span wire:loading wire:target="saveProduct">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- Modal tambah produk end --}}

</div>
