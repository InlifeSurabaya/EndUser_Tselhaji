<div x-data="{ showFotoModal: false}"
    @close-modal.window="
        showFotoModal = false
     "
    class="mb-10 bg-white border border-[var(--color-border)] rounded-2xl shadow-sm overflow-hidden" wire:loading.class="opacity-50 pointer-events-none">
    @role(\App\Enum\RoleEnum::SUPER_ADMIN->value)
    <div class="grid grid-cols-1 lg:grid-cols-3">

        <div class="lg:col-span-1 p-6 lg:border-r border-b lg:border-b-0 border-[var(--color-border)]">
            <h3 class="text-lg font-bold text-[var(--color-primary-700)]">Lihat User</h3>
            <p class="text-sm text-[var(--color-neutral-600)] mt-1 mb-6">Informasi ini akan ditampilkan secara publik.</p>

            <div class="flex flex-col items-center text-center">
                <div class="relative mb-4">
                    @if ($avatar)
                    <img class="h-52 w-52 rounded-2xl shadow-sm object-cover" src="{{ $avatar->temporaryUrl() }}"
                        alt="Preview Avatar">
                    @elseif ($existingAvatar)
                    <img class="h-52 w-52 rounded-2xl shadow-sm object-cover"
                        src="{{ Storage::url($existingAvatar) }}" alt="Avatar Pengguna">
                    @else
                    <div
                        class="h-52 w-52 rounded-2xl shadow-sm bg-[var(--color-neutral-200)] flex items-center justify-center text-[var(--color-neutral-600)]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-16 h-16">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A1.75 1.75 0 0 1 18 22H6a1.75 1.75 0 0 1-1.499-1.882Z" />
                        </svg>
                    </div>
                    @endif
                    <div wire:loading wire:target="avatar"
                        class="absolute inset-0 bg-white bg-opacity-80 flex items-center justify-center rounded-full">
                        <div
                            class="animate-spin rounded-full h-8 w-8 border-b-2 border-[var(--color-primary-600)]"></div>
                    </div>
                </div>

                <button @click="showFotoModal = ! showFotoModal" type="button"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-x-1 cursor-pointer text-sm font-medium text-[var(--color-primary-600)] hover:text-[var(--color-primary-700)] disabled:opacity-50 disabled:pointer-events-none transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5 lucide lucide-eye-icon lucide-eye">
                        <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                        <circle cx="12" cy="12" r="3" />
                    </svg> Lihat Foto
                </button>
            </div>
        </div>

        <div class="lg:col-span-2 p-6">
            <div class="space-y-4">

                <div class="pb-0.5">
                    <p class="items-start font-bold text-base text-primary-700">Biodata</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <p class="text-sm font-medium mb-1 text-[var(--color-neutral-600)]">Email</p>
                        <p class="text-base font-medium text-[var(--color-neutral-800)]">{{ $email }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium mb-1 text-[var(--color-neutral-600)]">Nama Lengkap</p>
                        <p class="text-base font-medium text-[var(--color-neutral-800)]">{{ $fullname ?: 'Belum diisi'}}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium mb-1 text-[var(--color-neutral-600)]">Tanggal Lahir</p>
                        <p class="text-base font-medium text-[var(--color-neutral-800)]">{{ $birth_date ? \Carbon\Carbon::parse($birth_date)->format('d M Y') : 'Belum diisi' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium mb-1 text-[var(--color-neutral-600)]">Jenis Kelamin</p>
                        <p class="text-base font-medium text-[var(--color-neutral-800)]">
                            @if ($gender === 'male') Laki-laki
                            @elseif ($gender === 'female') Perempuan
                            @elseif ($gender === 'other') Lainnya
                            @else Belum diisi
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium mb-1 text-[var(--color-neutral-600)]">Nomor Telkomsel</p>
                        <p class="text-base font-medium text-[var(--color-neutral-800)]">{{ $phone ?: 'Belum diisi' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium mb-1 text-[var(--color-neutral-600)]">Nomor Whatsapp</p>
                        <p class="text-base font-medium text-[var(--color-neutral-800)]">{{ $phoneWa ?: 'Belum diisi' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium mb-1 text-[var(--color-neutral-600)]">Role</p>
                        <p class="text-base font-medium text-[var(--color-neutral-800)]">{{ $role ?: 'Belum diisi' }}</p>
                    </div>

                </div>

                <div class="flex justify-end pt-4">
                    <button type="button" wire:click="$set('view','index')" class="px-4 py-2 bg-neutral-300 hover:bg-neutral-200 rounded mr-2">Kembali</button>
                </div>
            </div>
        </div>
    </div>

    <div id="hs-scale-animation-modal" class="hs-overlay hidden fixed top-0 start-0 size-full z-80 overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="hs-scale-animation-modal-label">
        <div class="hs-overlay-animation-target hs-overlay-open:scale-100 hs-overlay-open:opacity-100 scale-95 opacity-0 ease-in-out transition-all duration-200 sm:max-w-2xl sm:w-full m-3 sm:mx-auto flex items-center justify-center">
            <div class="w-full bg-white border border-gray-200 rounded-xl shadow-lg pointer-events-auto">
                <div class="flex justify-between items-center p-3 border-b border-gray-200">
                    <h3 id="hs-scale-animation-modal-label" class="font-bold text-gray-800">
                        Foto Pengguna
                    </h3>
                    <button type="button" class="size-8 inline-flex justify-center items-center rounded-full hover:bg-gray-200" aria-label="Close" data-hs-overlay="#hs-scale-animation-modal">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M18 6 6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-4 flex justify-center items-center bg-gray-50">
                    @if ($avatar)
                    <img src="{{ $avatar->temporaryUrl() }}" alt="Preview Avatar" class="max-h-[80vh] rounded-lg shadow-lg">
                    @elseif ($existingAvatar)
                    <img src="{{ Storage::url($existingAvatar) }}" alt="Avatar Pengguna" class="max-h-[80vh] rounded-lg shadow-lg">
                    @else
                    <div class="text-center text-gray-500">Tidak ada foto tersedia</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @endrole

    {{-- Modal edit produk start --}}
    <div
        x-cloak
        x-show="showFotoModal"
        @keydown.escape.window="showFotoModal = false"
        class="fixed inset-0 z-[9999] flex items-center justify-center"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-neutral-900/50 backdrop-blur-sm" x-show="showFotoModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"></div>

        <div
            class="bg-white rounded-2xl shadow-xl w-full max-w-none mx-5 relative overflow-hidden"
            style="height: 90vh;"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90">
            <div class="flex items-center justify-between p-2 border-b border-neutral-200 text-center">
                <h2 class="text-xl font-semibold text-neutral-800">
                    Lihat Foto
                </h2>
                <button @click="showFotoModal = false"
                    class="text-neutral-500 hover:text-primary-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div>
                <div class="p-4 space-y-5 max-h-[850vh] overflow-y-auto">

                    <div wire:loading.remove wire:target="getProduct" class="space-y-5">
                        <div class="flex justify-center items-center bg-neutral-50">
                            @if ($avatar)
                            <img src="{{ Storage::url($existingAvatar) }}" class="max-h-[80vh] max-w-full object-contain rounded-lg shadow-lg">
                            @elseif ($existingAvatar)
                            <img src="{{ Storage::url($existingAvatar) }}" alt="Avatar Pengguna" class="max-h-[80vh] max-w-full object-contain rounded-lg shadow-lg">
                            @else
                            <div class="text-center text-gray-500">Tidak ada foto tersedia</div>
                            @endif
                        </div>
                    </div>

                </div>

            </div>

        </div>
        {{-- Modal edit produk end --}}

    </div>