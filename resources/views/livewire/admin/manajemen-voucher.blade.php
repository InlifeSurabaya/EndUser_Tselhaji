<div>
    <div x-data="{ showModalTambah: false, showModalEdit: false }"
         x-on:close-modal.window="showModalTambah = false; showModalEdit = false"
    >
        <!-- Header Halaman -->
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Manajemen Voucher</h2>
            <p class="text-gray-600">Kelola, tambah, edit, dan hapus voucher untuk promo.</p>
        </div>

        <!-- Toolbar: Filter dan Tombol Tambah -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-4">
            <!-- Filter Pencarian -->
            <div class="flex items-center gap-4">
                <div class="relative">
                    <label for="hs-table-search" class="sr-only">Search</label>
                    <input type="text" wire:model.live.debounce.300ms="searchCode"
                           class="py-2 px-3 ps-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Cari berdasarkan kode voucher...">
                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <select wire:model.live="perPage"
                            class="py-2 px-3 block border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="10">10 per halaman</option>
                        <option value="25">25 per halaman</option>
                        <option value="50">50 per halaman</option>
                    </select>
                </div>
            </div>

            <!-- Tombol Tambah Voucher -->
            <button @click="showModalTambah = true" type="button"
                    class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                <svg class="flex-shrink-0 h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round">
                    <path d="M5 12h14"/>
                    <path d="M12 5v14"/>
                </svg>
                Tambah Voucher
            </button>
        </div>

        <!-- Tabel Voucher -->
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="border border-neutral-200 rounded-lg overflow-hidden shadow">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Kode
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Nilai
                                    Diskon
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Tipe
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Tgl Mulai
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Tgl
                                    Berakhir
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Limit
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Digunakan
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase">
                                    Aksi
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            @forelse ($vouchers as $voucher)
                                <tr wire:key="{{ $voucher->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">{{ $voucher->code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                        @if ($voucher->discount_type == 'percentage')
                                            {{ $voucher->discount_value }}%
                                        @else
                                            Rp {{ number_format($voucher->discount_value, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 capitalize">{{ $voucher->discount_type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ (new \DateTime($voucher->start_date))->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ (new \DateTime($voucher->end_date))->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $voucher->usage_limit ?? '∞' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $voucher->used_count ?? 0 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($voucher->is_active && new \DateTime($voucher->end_date) >= now())
                                            <span
                                                class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                                                Aktif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Kedaluwarsa/Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-end">
                                        <button @click="showModalEdit = true"
                                                wire:click="getVoucher({{ $voucher->id }})" type="button"
                                                class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-blue-600 hover:text-blue-800">
                                            Edit
                                        </button>
                                        <button wire:click="deleteVoucherAlert({{ $voucher->id }})" type="button"
                                                class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-red-600 hover:text-red-800 ms-2">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Tidak ada data voucher ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination Links -->
        <div class="mt-4">
            {{ $vouchers->links() }}
        </div>

        {{--        Modal Tambah Voucher --}}
        <div x-show="showModalTambah" class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-neutral-900/50 backdrop-blur-sm" x-show="showModalTambah"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

            <!-- Modal Content -->
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl mx-auto transition-all"
                 x-show="showModalTambah" x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <form wire:submit.prevent="saveVoucher">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center py-3 px-4 border-b">
                        <h3 class="font-bold text-gray-800">Tambah Voucher Baru</h3>
                        <button @click="showModalTambah = false; $wire.clearForms()" type="button"
                                class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 overflow-y-auto" style="max-height: 70vh;">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Kode Voucher -->
                            <div class="sm:col-span-2">
                                <label for="code" class="block text-sm font-medium mb-2">Kode Voucher</label>
                                <input type="text" id="code" wire:model="code"
                                       class="py-3 px-4 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('code')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Tipe Diskon -->
                            <div>
                                <label for="discount_type" class="block text-sm font-medium mb-2">Tipe Diskon</label>
                                <select id="discount_type" wire:model="discount_type"
                                        class="py-3 px-4 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="percentage">Persentase (%)</option>
                                    <option value="fixed">Tetap (Rp)</option>
                                </select>
                                @error('discount_type')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Nilai Diskon -->
                            <div>
                                <label for="discount_value" class="block text-sm font-medium mb-2">Nilai Diskon</label>
                                <input type="number" id="discount_value" wire:model="discount_value"
                                       class="py-3 px-4 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="text-xs text-gray-500">Isi angka (mis: 10 untuk 10% atau 10000 untuk Rp
                                    10.000)
                                </span>
                                @error('discount_value')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Tanggal Mulai -->
                            <div>
                                <label for="start_date" class="block text-sm font-medium mb-2">Tanggal Mulai</label>
                                <input type="date" id="start_date" wire:model="start_date"
                                       class="py-3 px-4 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('start_date')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Tanggal Berakhir -->
                            <div>
                                <label for="end_date" class="block text-sm font-medium mb-2">Tanggal Berakhir</label>
                                <input type="date" id="end_date" wire:model="end_date"
                                       class="py-3 px-4 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('end_date')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Batas Penggunaan -->
                            <div class="sm:col-span-2">
                                <label for="usage_limit" class="block text-sm font-medium mb-2">Batas Penggunaan
                                    (Opsional)</label>
                                <input type="number" id="usage_limit" wire:model="usage_limit"
                                       class="py-3 px-4 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="Kosongkan jika tidak ada batas">
                                @error('usage_limit')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Status Aktif -->
                            <div class="sm:col-span-2">
                                <div class="flex items-center">
                                    <input id="is_active" wire:model="is_active" type="checkbox"
                                           class="shrink-0 mt-0.5 border border-gray-200 rounded text-blue-600 focus:ring-blue-500">
                                    <label for="is_active" class="text-sm text-gray-500 ms-3">Aktifkan Voucher</label>
                                </div>
                                @error('is_active')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t">
                        <button @click="showModalTambah = false; $wire.clearForms()" type="button"
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">
                            <span wire:loading.remove wire:target="saveVoucher">Simpan</span>
                            <span wire:loading wire:target="saveVoucher">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>


        {{--        Modal Edit Voucher  --}}
        <div x-show="showModalEdit" class="fixed inset-0 z-50 flex items-center justify-center"
        >
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-neutral-900/50 backdrop-blur-sm" x-show="showModalEdit"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

            <!-- Modal Content -->
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl mx-auto transition-all"
                 x-show="showModalEdit" x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <form wire:submit.prevent="updateVoucher">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center py-3 px-4 border-b">
                        <h3 class="font-bold text-gray-800">Edit Voucher</h3>
                        <button @click="showModalEdit = false; $wire.clearForms()" type="button"
                                class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 overflow-y-auto" style="max-height: 70vh;">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Kode Voucher -->
                            <div class="sm:col-span-2">
                                <label for="codeDetailVoucher" class="block text-sm font-medium mb-2">Kode
                                    Voucher</label>
                                <input type="text" id="codeDetailVoucher" wire:model="codeDetailVoucher"
                                       class="py-3 px-4 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('codeDetailVoucher')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Tipe Diskon -->
                            <div>
                                <label for="discount_typeDetailVoucher" class="block text-sm font-medium mb-2">Tipe
                                    Diskon</label>
                                <select id="discount_typeDetailVoucher" wire:model="discount_typeDetailVoucher"
                                        class="py-3 px-4 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="percentage">Persentase (%)</option>
                                    <option value="fixed">Tetap (Rp)</option>
                                </select>
                                @error('discount_typeDetailVoucher')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Nilai Diskon -->
                            <div>
                                <label for="discount_valueDetailVoucher" class="block text-sm font-medium mb-2">Nilai
                                    Diskon</label>
                                <input type="number" id="discount_valueDetailVoucher"
                                       wire:model="discount_valueDetailVoucher"
                                       class="py-3 px-4 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="text-xs text-gray-500">Isi angka (mis: 10 untuk 10% atau 10000 untuk Rp
                                    10.000)
                                </span>
                                @error('discount_valueDetailVoucher')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Tanggal Mulai -->
                            <div>
                                <label for="start_dateDetailVoucher" class="block text-sm font-medium mb-2">Tanggal
                                    Mulai</label>
                                <input type="date" id="start_dateDetailVoucher" wire:model="start_dateDetailVoucher"
                                       class="py-3 px-4 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('start_dateDetailVoucher')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Tanggal Berakhir -->
                            <div>
                                <label for="end_dateDetailVoucher" class="block text-sm font-medium mb-2">Tanggal
                                    Berakhir</label>
                                <input type="date" id="end_dateDetailVoucher" wire:model="end_dateDetailVoucher"
                                       class="py-3 px-4 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('end_dateDetailVoucher')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Batas Penggunaan -->
                            <div class="sm:col-span-2">
                                <label for="usage_limitDetailVoucher" class="block text-sm font-medium mb-2">Batas
                                    Penggunaan (Opsional)</label>
                                <input type="number" id="usage_limitDetailVoucher" wire:model="usage_limitDetailVoucher"
                                       class="py-3 px-4 block w-full border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="Kosongkan jika tidak ada batas">
                                @error('usage_limitDetailVVoucher')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Status Aktif -->
                            <div class="sm:col-span-2">
                                <div class="flex items-center">
                                    <input id="is_activeDetailVoucher" wire:model="is_activeDetailVoucher"
                                           type="checkbox"
                                           class="shrink-0 mt-0.5 border border-gray-200 rounded text-blue-600 focus:ring-blue-500">
                                    <label for="is_activeDetailVoucher" class="text-sm text-gray-500 ms-3">Aktifkan
                                        Voucher</label>
                                </div>
                                @error('is_activeDetailVoucher')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t">
                        <button @click="showModalEdit = false; $wire.clearForms()" type="button"
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700">
                            <span wire:loading.remove wire:target="updateVoucher">Update</span>
                            <span wire:loading wire:target="updateVoucher">Updating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
