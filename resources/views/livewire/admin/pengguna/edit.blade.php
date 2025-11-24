<div class="mb-10 bg-white border border-[var(--color-border)] rounded-2xl shadow-sm overflow-hidden" wire:loading.class="opacity-50 pointer-events-none">
    @role(\App\Enum\RoleEnum::SUPER_ADMIN->value)
    <div class="grid grid-cols-1 lg:grid-cols-3">

        <div class="lg:col-span-1 p-6 lg:border-r border-b lg:border-b-0 border-[var(--color-border)]">
            <h3 class="text-lg font-bold text-[var(--color-primary-700)]">Edit User</h3>
            <p class="text-sm text-[var(--color-neutral-600)] mt-1 mb-6">Informasi ini akan ditampilkan secara
                publik.</p>

            <div class="flex flex-col items-center text-center">
                <div class="relative mb-4">
                    @if ($avatar)
                    <img class="h-42 w-42 rounded-full object-cover" src="{{ $avatar->temporaryUrl() }}"
                        alt="Preview Avatar">
                    @elseif ($existingAvatar)
                    <img class="h-42 w-42 rounded-full object-cover"
                        src="{{ Storage::url($existingAvatar) }}" alt="Avatar Pengguna">
                    @else
                    <div
                        class="h-42 w-42 rounded-full bg-[var(--color-neutral-200)] flex items-center justify-center text-[var(--color-neutral-600)]">
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

                <label for="avatar-upload"
                    class="inline-flex items-center gap-x-1 cursor-pointer text-sm font-medium text-[var(--color-primary-600)] hover:text-[var(--color-primary-700)] disabled:opacity-50 disabled:pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5 lucide lucide-image-up-icon lucide-image-up">
                        <path d="M10.3 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10l-3.1-3.1a2 2 0 0 0-2.814.014L6 21" />
                        <path d="m14 19.5 3-3 3 3" />
                        <path d="M17 22v-5.5" />
                        <circle cx="9" cy="9" r="2" />
                    </svg>Ganti Foto
                </label>
                <input id="avatar-upload" type="file" wire:model="avatar" class="sr-only">
                @error('avatar')
                <span class="text-sm text-[var(--color-error)] mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="lg:col-span-2 p-6">
            <form wire:submit.prevent="update" class="space-y-4">

                <div class="pb-0.5">
                    <p class="items-start font-bold text-base text-primary-700">Biodata</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label for="email"
                            class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Email</label>
                        <input id="email" type="text" wire:model.defer="email"
                            class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)] focus:border-[var(--color-primary-500)]"
                            placeholder="Email">
                        @error('email')
                        <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="fullname"
                            class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Nama
                            Lengkap</label>
                        <input id="fullname" type="text" wire:model.defer="fullname"
                            class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)] focus:border-[var(--color-primary-500)]"
                            placeholder="Nama lengkap Anda">
                        @error('fullname')
                        <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="birth_date"
                            class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Tanggal
                            Lahir</label>
                        <input type="date" id="birth_date" wire:model.defer="birth_date"
                            class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)]">
                        @error('birth_date')
                        <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="gender"
                            class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Jenis
                            Kelamin</label>
                        <select id="gender" wire:model.defer="gender"
                            class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)] focus:border-[var(--color-primary-500)]">
                            <option value="">Pilih...</option>
                            <option value="male">Laki-laki</option>
                            <option value="female">Perempuan</option>
                            <option value="other">Lainnya</option>
                        </select>
                        @error('gender')
                        <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="phone"
                            class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Nomor Telkomsel</label>
                        <input id="phone" type="tel" wire:model.defer="phone"
                            class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)]"
                            placeholder="081xxx...">
                        @error('phone')
                        <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="phoneWa"
                            class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Nomor Whatsapp</label>
                        <input id="phoneWa" type="tel" wire:model.defer="phoneWa"
                            class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)]"
                            placeholder="081xxx...">
                        @error('phoneWa')
                        <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                    </div>

                </div>

                <div class="pt-2 pb-0.5">
                    <p class="items-start font-bold text-base text-primary-700">Keamanan</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password"
                            class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Password</label>
                        <input id="password" type="password" wire:model.defer="password"
                            class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)] focus:border-[var(--color-primary-500)]"
                            placeholder="Password">
                        @error('password')
                        <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" wire:model.defer="password_confirmation"
                            class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)] focus:border-[var(--color-primary-500)]"
                            placeholder="Konfirmasi Password">
                        @error('password_confirmation')
                        <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="role"
                            class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Role</label>
                        <select id="role" wire:model.defer="role"
                            class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)] focus:border-[var(--color-primary-500)]">
                            <option value="">Pilih...</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                        @error('role')
                        <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                    </div>
                </div>


                <div class="flex justify-end pt-4">
                    <button type="button" wire:click="$set('view','index')" class="px-4 py-2 bg-neutral-300 hover:bg-neutral-200 rounded mr-2">Kembali</button>
                    <button type="submit"
                        class="bg-[var(--color-primary-600)] hover:bg-[var(--color-primary-700)] text-white font-semibold py-2.5 px-5 rounded-lg transition-all duration-200 flex items-center gap-2"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="updateProfile">Simpan Perubahan</span>
                        <span wire:loading wire:target="updateProfile">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>

    </div>
    @endrole
</div>