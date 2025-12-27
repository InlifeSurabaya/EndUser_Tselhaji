<div x-data="{ showFotoModal: false}" @close-modal.window="showFotoModal = false"
    class="mb-10 bg-white border border-[var(--color-border)] rounded-2xl shadow-sm overflow-hidden"
    wire:loading.class="opacity-50 pointer-events-none">
    @role(\App\Enum\RoleEnum::SUPER_ADMIN->value)
    <div class="grid grid-cols-1 lg:grid-cols-3">

        <div class="lg:col-span-1 p-6 lg:border-r border-b lg:border-b-0 border-[var(--color-border)]">
            <h3 class="text-lg font-bold text-[var(--color-primary-700)]">Lihat User</h3>
            <p class="text-sm text-[var(--color-neutral-600)] mt-1 mb-6">Informasi ini akan ditampilkan secara publik.
            </p>

            <div class="flex flex-col items-center text-center">
                <div class="relative mb-4">
                    @if ($avatar)
                    <img class="h-52 w-52 rounded-2xl shadow-sm object-cover" src="{{ $avatar->temporaryUrl() }}"
                        alt="Preview Avatar">
                    @elseif ($existingAvatar)
                    <img class="h-52 w-52 rounded-2xl shadow-sm object-cover" src="{{ Storage::url($existingAvatar) }}"
                        alt="Avatar Pengguna">
                    @else
                    <div
                        class="h-52 w-52 rounded-2xl shadow-sm bg-[var(--color-neutral-200)] flex items-center justify-center text-[var(--color-neutral-600)]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-16 h-16">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A1.75 1.75 0 0 1 18 22H6a1.75 1.75 0 0 1-1.499-1.882Z" />
                        </svg>
                    </div>
                    @endif
                    <div wire:loading wire:target="avatar"
                        class="absolute inset-0 bg-white bg-opacity-80 flex items-center justify-center rounded-full">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[var(--color-primary-600)]">
                        </div>
                    </div>
                </div>

                @if ($avatar)
                <a href="{{ $avatar->temporaryUrl() }}" target="_blank"
                    class="inline-flex items-center gap-x-1 text-sm font-medium text-[var(--color-primary-600)] hover:text-[var(--color-primary-700)]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path d="M2.062 12.348a1 1 0 0 1 0-.696 
                     10.75 10.75 0 0 1 19.876 0 
                     1 1 0 0 1 0 .696 
                     10.75 10.75 0 0 1-19.876 0" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    Lihat Foto
                </a>
                @elseif ($existingAvatar)
                <a href="{{ Storage::url($existingAvatar) }}" target="_blank"
                    class="inline-flex items-center gap-x-1 text-sm font-medium text-[var(--color-primary-600)] hover:text-[var(--color-primary-700)]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path d="M2.062 12.348a1 1 0 0 1 0-.696 
                     10.75 10.75 0 0 1 19.876 0 
                     1 1 0 0 1 0 .696 
                     10.75 10.75 0 0 1-19.876 0" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    Lihat Foto
                </a>
                @endif
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
                        <p class="text-base font-medium text-[var(--color-neutral-800)]">{{ $fullname ?: 'Belum diisi'}}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium mb-1 text-[var(--color-neutral-600)]">Tanggal Lahir</p>
                        <p class="text-base font-medium text-[var(--color-neutral-800)]">{{ $birth_date ?
                            \Carbon\Carbon::parse($birth_date)->format('d M Y') : 'Belum diisi' }}</p>
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
                        <p class="text-base font-medium text-[var(--color-neutral-800)]">{{ $phone ?: 'Belum diisi' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium mb-1 text-[var(--color-neutral-600)]">Nomor Whatsapp</p>
                        <p class="text-base font-medium text-[var(--color-neutral-800)]">{{ $waphone ?: 'Belum diisi' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium mb-1 text-[var(--color-neutral-600)]">Role</p>
                        <p class="text-base font-medium text-[var(--color-neutral-800)]">{{ strtoupper($role) ?: 'Belum
                            diisi' }}</p>
                    </div>

                    @if ($is_ban == 1)
                    <div>
                        <p class="text-sm font-bold mb-1 text-[var(--color-primary-800)]">Ban</p>
                        <p class="text-base font-medium text-[var(--color-neutral-800)]">{{ ($reason) ?: 'None' }}</p>
                    </div>
                    @endif

                </div>

                <div class="flex justify-end pt-4">
                    <button type="button" wire:click="$set('view','index')"
                        class="px-4 py-2 bg-neutral-300 hover:bg-neutral-200 rounded mr-2">Kembali</button>
                </div>
            </div>
        </div>
    </div>

    @endrole