<div class="min-h-[80vh] flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl w-full">

        <div class="text-center mb-8">
            <h1 class="block text-3xl font-bold text-neutral-900">
                Bantu Kami
                <span class="text-primary-600">Mengenali Anda</span>
            </h1>
            <p class="mt-2 text-lg text-neutral-600">
                Ceritakan sedikit rencana perjalanan Anda agar kami dapat memberikan rekomendasi paket roaming terbaik.
            </p>
        </div>

        <div class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 sm:p-7">

                <form wire:submit="createUserPreference">
                    <div class="grid gap-y-6">

                        <div>
                            <label for="plannedBudget" class="block text-sm font-semibold mb-2 text-neutral-800">
                                Rencana Budget (Rupiah)
                            </label>
                            <div
                                x-data="{
            budget: @entangle('plannedBudget'), // Sinkronisasi dengan Livewire
            formatRupiah(value) {
                if (!value) return '';
                // Format angka ke format IDR (Rp 100.000)
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(value);
            },
            handleInput(e) {
                // 1. Ambil input mentah, hapus semua karakter kecuali angka
                let rawValue = e.target.value.replace(/[^0-9]/g, '');

                // 2. Jika kosong, set null agar validasi required jalan
                if (rawValue === '') {
                    this.budget = null;
                    e.target.value = '';
                    return;
                }

                // 3. Update property Livewire dengan angka murni (integer)
                this.budget = parseInt(rawValue);

                // 4. Update tampilan input field menjadi format Rupiah
                e.target.value = this.formatRupiah(rawValue);
            }
        }"
                                x-init="$el.querySelector('input').value = formatRupiah(budget)"
                            >
                                <input
                                    type="text"
                                    id="plannedBudget"
                                    inputmode="numeric"
                                    @input="handleInput"
                                    class="py-3 px-4 block w-full border-neutral-200 rounded-lg text-sm focus:border-primary-500 focus:ring-primary-500 disabled:opacity-50 disabled:pointer-events-none placeholder-neutral-400 text-neutral-900 bg-neutral-50 border focus:bg-white transition-all"
                                    placeholder="Contoh: Rp 500.000"
                                    required
                                >
                            </div>
                            <p class="text-xs text-neutral-500 mt-2">
                                *Perkiraan maksimal biaya yang ingin Anda keluarkan.
                            </p>
                            @error('plannedBudget')
                            <span class="text-xs text-error mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="plannedDuration" class="block text-sm font-semibold mb-2 text-neutral-800">
                                    Durasi Perjalanan
                                </label>
                                <div class="relative">
                                    <input
                                        type="number"
                                        id="plannedDuration"
                                        wire:model="plannedDuration"
                                        class="[&::-webkit-inner-spin-button]:appearance-none py-3 px-4 block w-full border-neutral-200 rounded-lg text-sm focus:border-primary-500 focus:ring-primary-500 disabled:opacity-50 disabled:pointer-events-none placeholder-neutral-400 text-neutral-900 bg-neutral-50 border focus:bg-white transition-all"
                                        placeholder="7"
                                        required
                                    >
                                    <div
                                        class="absolute inset-y-0 end-0 flex items-center pointer-events-none z-20 pe-4">
                                        <span class="text-neutral-500 text-sm">Hari</span>
                                    </div>
                                </div>
                                @error('plannedDuration')
                                <span class="text-xs text-error mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="plannedQuota" class="block text-sm font-semibold mb-2 text-neutral-800">
                                    Kebutuhan Data
                                </label>
                                <div class="relative">
                                    <input
                                        type="number"
                                        id="plannedQuota"
                                        wire:model="plannedQuota"
                                        class="[&::-webkit-inner-spin-button]:appearance-none py-3 px-4 block w-full border-neutral-200 rounded-lg text-sm focus:border-primary-500 focus:ring-primary-500 disabled:opacity-50 disabled:pointer-events-none placeholder-neutral-400 text-neutral-900 bg-neutral-50 border focus:bg-white transition-all"
                                        placeholder="10"
                                        required
                                    >
                                    <div
                                        class="absolute inset-y-0 end-0 flex items-center pointer-events-none z-20 pe-4">
                                        <span class="text-neutral-500 text-sm">GB</span>
                                    </div>
                                </div>
                                @error('plannedQuota')
                                <span class="text-xs text-error mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <button
                                type="submit"
                                class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50 disabled:pointer-events-none shadow-md hover:shadow-lg transition-all"
                                wire:loading.attr="disabled"
                                wire:target="createUserPreference"
                            >
                                <span wire:loading.remove wire:target="createUserPreference">
                                    Simpan & Cari Rekomendasi
                                </span>
                                <span wire:loading wire:target="createUserPreference"
                                      class="animate-spin inline-block size-4 border-[3px] border-current border-t-transparent text-white rounded-full"
                                      role="status" aria-label="loading"></span>
                                <span wire:loading wire:target="createUserPreference">
                                    Memproses...
                                </span>
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <p class="mt-6 text-center text-sm text-neutral-500">
            Data ini membantu kami menggunakan model
            <span class="font-medium text-neutral-700">Machine Learning</span>
            untuk mencocokkan profil perjalanan Anda.
        </p>
    </div>
</div>
