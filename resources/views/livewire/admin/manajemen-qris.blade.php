<div class="p-4 sm:p-6 lg:p-8 space-y-6 bg-neutral-50 min-h-screen">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral-900">Manajemen QRIS</h1>
        <p class="text-sm text-neutral-600">Perbarui atau unggah gambar QRIS statis yang akan ditampilkan di halaman pembayaran.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-1">
            <div class="bg-white border border-neutral-200 rounded-lg shadow-sm p-5">
                <h3 class="text-lg font-semibold text-neutral-900 mb-4">QRIS Aktif Saat Ini</h3>

                @if ($currentQris)
                    <div class="space-y-3">
                        <img src="{{ asset('storage/' . $currentQris->file) }}" alt="QRIS Aktif" class="w-full rounded-md border border-neutral-300">
                        <p class="text-xs text-neutral-500 text-center">
                            Diunggah pada: {{ $currentQris->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                @else
                    <div class="flex items-center justify-center h-48 border-2 border-dashed border-neutral-300 rounded-md bg-neutral-50">
                        <p class="text-neutral-500">Belum ada QRIS aktif.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="lg:col-span-2">
            <form wire:submit="save" class="bg-white border border-neutral-200 rounded-lg shadow-sm p-5">
                <h3 class="text-lg font-semibold text-neutral-900 mb-4">Unggah QRIS Baru</h3>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-neutral-800 mb-1 block">File Gambar QRIS</label>

                        @if ($newQris)
                            <div class="relative group">
                                <img src="{{ $newQris->temporaryUrl() }}" alt="Preview QRIS" class="w-full max-w-sm mx-auto rounded-md border border-neutral-300">
                                <button wire:click="clearPreview" type="button" class="absolute top-2 right-2 bg-neutral-800 bg-opacity-50 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity" aria-label="Hapus preview">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <div wire:loading wire:target="newQris" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-md">
                                    <p class="text-neutral-600">Loading preview...</p>
                                </div>
                            </div>
                        @else
                            <label for="qris-upload" class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-neutral-300 border-dashed rounded-lg cursor-pointer bg-neutral-50 hover:bg-neutral-100 transition-colors">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-neutral-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                    </svg>
                                    <p class="mt-2 text-sm text-neutral-600">
                                        <span class="font-semibold text-accent-600">Klik untuk memilih</span> atau tarik dan lepas
                                    </p>
                                    <p class="text-xs text-neutral-500">PNG, JPG, atau WEBP (Maks. 2MB)</p>
                                </div>
                                <input id="qris-upload" wire:model="newQris" type="file" class="sr-only">
                            </label>
                        @endif

                        @error('newQris')
                        <p class="text-sm text-primary-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2 text-right">
                        <button type="submit"
                                :disabled="$newQris"
                                class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-semibold rounded-md text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-wait">

                            <span wire:loading.remove wire:target="save">
                                Simpan dan Aktifkan
                            </span>
                            <span wire:loading wire:target="save">
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
