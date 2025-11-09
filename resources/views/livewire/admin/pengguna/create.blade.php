<div class="bg-white border border-[var(--color-border)] rounded-2xl shadow-sm overflow-hidden" wire:loading.class="opacity-50 pointer-events-none">
    <div class="grid grid-cols-1 lg:grid-cols-3">

        <div class="lg:col-span-1 p-6 lg:border-r border-b lg:border-b-0 border-[var(--color-border)]">
            <h3 class="text-lg font-bold text-[var(--color-primary-700)]">Tambah User</h3>
            <p class="text-sm text-[var(--color-neutral-600)] mt-1 mb-6">Informasi ini akan ditampilkan secara
                publik.</p>

            <div class="flex flex-col items-center text-center">
                <div class="relative mb-4">
                    <div
                        class="h-32 w-32 rounded-full bg-[var(--color-neutral-200)] flex items-center justify-center text-[var(--color-neutral-600)]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-16 h-16">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A1.75 1.75 0 0 1 18 22H6a1.75 1.75 0 0 1-1.499-1.882Z" />
                        </svg>
                    </div>
                    <div wire:loading wire:target="avatar"
                        class="absolute inset-0 bg-white bg-opacity-80 flex items-center justify-center rounded-full">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[var(--color-primary-600)]"></div>
                    </div>
                </div>
                <p class="text-sm text-[var(--color-neutral-600)] mb-4">Tambahkan foto di edit atau menu profil</p>
            </div>
        </div>

        <div class="lg:col-span-2 p-6">
            <form wire:submit.prevent="store" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label for="email"
                            class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Email</label>
                        <input id="email" type="text" wire:model="email"
                            class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)] focus:border-[var(--color-primary-500)]"
                            placeholder="Email">
                        @error('email')
                        <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="fullname"
                            class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Nama
                            Lengkap</label>
                        <input id="fullname" type="text" wire:model="fullname"
                            class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)] focus:border-[var(--color-primary-500)]"
                            placeholder="Nama lengkap Anda">
                        @error('fullname')
                        <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password"
                            class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Password</label>
                        <input id="password" type="password" wire:model="password"
                            class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)] focus:border-[var(--color-primary-500)]"
                            placeholder="Password">
                        @error('password')
                        <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" wire:model="password_confirmation"
                            class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)] focus:border-[var(--color-primary-500)]"
                            placeholder="Konfirmasi Password">
                        @error('password_confirmation')
                        <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="gender"
                            class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Jenis
                            Kelamin</label>
                        <select id="gender" wire:model="gender"
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
                        <input id="phone" type="tel" wire:model="phone"
                            class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)]"
                            placeholder="081xxx...">
                        @error('phone')
                        <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="role"
                            class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Role</label>
                        <select id="role" wire:model="role"
                            class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)] focus:border-[var(--color-primary-500)]">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
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
</div>