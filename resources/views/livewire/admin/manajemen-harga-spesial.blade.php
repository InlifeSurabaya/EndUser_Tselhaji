<div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">

    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">
            Manajemen Potongan Harga
        </h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Atur persentase diskon otomatis untuk setiap segmen pelanggan.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        @foreach($segments as $segment)
            <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-900 dark:border-neutral-700 dark:shadow-neutral-700/70">

                <div class="p-4 md:p-5 border-b dark:border-neutral-700 {{ $segment->color() }} bg-opacity-10 rounded-t-xl">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        {{-- Icon (Opsional) --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tag"><path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l5 5c1.26 1.26.3.3 5 5a2 2 0 0 0 2.828 0l7.172-7.172a2 2 0 0 0 0-2.828z"/><path d="M7 7h.01"/></svg>

                        {{ $segment->label() }}
                    </h3>
                </div>

                <div class="p-4 md:p-5">
                    <div class="space-y-4">
                        <div>
                            <label for="input-{{ $segment->value }}" class="block text-sm font-medium mb-2 dark:text-white">
                                Persentase Potongan
                            </label>

                            <div class="relative">
                                <input
                                    type="number"
                                    id="input-{{ $segment->value }}"
                                    wire:model="discounts.{{ $segment->value }}"
                                    class="py-3 px-4 ps-4 pe-10 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                    placeholder="0"
                                >
                                <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none z-20 pe-4">
                                    <span class="text-gray-500">%</span>
                                </div>
                            </div>

                            @error('discounts.' . $segment->value)
                            <span class="text-xs text-red-600 mt-2 block">
                                {{ $message }}
                            </span>
                            @enderror
                        </div>

                        <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-100 dark:bg-neutral-800 dark:border-neutral-700">
                            <div class="flex gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 mt-0.5 min-w-[16px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>

                                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                                    Target: <span class="font-medium text-gray-800 dark:text-gray-200">{{ $segment->description() }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 border-t border-gray-200 rounded-b-xl py-3 px-4 md:px-5 dark:bg-neutral-900 dark:border-neutral-700">
                    <div class="flex justify-end">
                        <button
                            type="button"
                            wire:click="updateHarga('{{ $segment->value }}')"
                            wire:loading.attr="disabled"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                        >
                            <span wire:loading.remove wire:target="updateHarga('{{ $segment->value }}')">Simpan Perubahan</span>
                            <span wire:loading wire:target="updateHarga('{{ $segment->value }}')">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</div>
