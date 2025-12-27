<div class="max-w-6xl mx-auto my-8 px-4 sm:px-6 lg:px-8 text-[var(--color-foreground)]">

    @if(!$user->hasVerifiedEmail())
        <div
            class="mb-5 p-4 flex items-center justify-between bg-[var(--color-primary-50)] border border-[var(--color-primary-200)] rounded-xl shadow-sm">
            <p class="text-sm text-[var(--color-primary-700)]">
                Email Anda belum terverifikasi.<br>Silakan verifikasi untuk keamanan akun.
            </p>
            <button wire:click="sendVerificationEmail"
                    class="text-sm font-semibold text-[var(--color-primary-600)] hover:text-[var(--color-primary-700)] underline"
                    wire:loading.attr="disabled"
                    wire:target="sendVerificationEmail">
                Kirim Ulang
            </button>
        </div>
    @endif

    <div class="space-y-6">

        <div class="bg-white border border-[var(--color-border)] rounded-2xl shadow-sm overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3">

                <div class="lg:col-span-1 p-6 lg:border-r border-b lg:border-b-0 border-[var(--color-border)]">
                    <h3 class="text-lg font-semibold text-[var(--color-neutral-900)]">Profil</h3>
                    <p class="text-sm text-[var(--color-neutral-600)] mt-1 mb-6">Informasi ini akan ditampilkan secara
                        publik.</p>

                    <div class="flex flex-col items-center text-center">
                        <div class="relative mb-4">
                            @if ($avatar)
                                <img class="h-32 w-32 rounded-full object-cover" src="{{ $avatar->temporaryUrl() }}"
                                     alt="Preview Avatar">
                            @elseif ($existingAvatar)
                                <img class="h-32 w-32 rounded-full object-cover"
                                     src="{{ Storage::url($existingAvatar) }}" alt="Avatar Pengguna">
                            @else
                                <div
                                    class="h-32 w-32 rounded-full bg-[var(--color-neutral-200)] flex items-center justify-center text-[var(--color-neutral-600)]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="w-16 h-16">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A1.75 1.75 0 0 1 18 22H6a1.75 1.75 0 0 1-1.499-1.882Z"/>
                                    </svg>
                                </div>
                            @endif
                            <div wire:loading wire:target="avatar"
                                 class="absolute inset-0 bg-white bg-opacity-80 flex items-center justify-center rounded-full">
                                <div
                                    class="animate-spin rounded-full h-8 w-8 border-b-2 border-[var(--color-primary-600)]"></div>
                            </div>
                        </div>

                        <h2 class="text-lg font-semibold">{{ $user->userProfile->fullname ?? 'Nama Pengguna' }}</h2>
                        <p class="text-sm text-[var(--color-neutral-600)] mb-4">{{ $user->email }}</p>

                        <label for="avatar-upload"
                               class="cursor-pointer text-sm font-medium text-[var(--color-accent-600)] hover:text-[var(--color-accent-700)]">
                            Ganti Foto
                        </label>
                        <input id="avatar-upload" type="file" wire:model="avatar" class="sr-only">
                        @error('avatar')
                        <span class="text-sm text-[var(--color-error)] mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="lg:col-span-2 p-6">
                    <form wire:submit.prevent="updateProfile" class="space-y-4">
                        <div class="grid grid-cols-1">
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
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                                <label for="birth_date"
                                       class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Tanggal
                                    Lahir</label>
                                <input type="date" id="birth_date" wire:model.defer="birth_date"
                                       class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)]">
                                @error('birth_date')
                                <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="phone"
                                       class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Nomor
                                    Telkomsel</label>
                                <input id="phone" type="tel" wire:model.defer="phone"
                                       class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)]"
                                       placeholder="08123456789">
                                @error('phone')
                                <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="waphone"
                                       class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Nomor
                                    Whatsapp</label>
                                <input id="waphone" type="tel" wire:model.defer="waphone"
                                       class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)]"
                                       placeholder="08123456789">
                                @error('waphone')
                                <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        {{-- Input Alamat jika diperlukan --}}
                        {{-- <div>
                            <label for="address" class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Alamat</label>
                            <textarea id="address" wire:model.defer="address" rows="3"
                                      class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)]"
                                      placeholder="Masukkan alamat lengkap Anda"></textarea>
                            @error('address')
                            <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                        </div> --}}

                        <div class="flex justify-end pt-4">
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

        <div class="bg-white border border-[var(--color-border)] rounded-2xl shadow-sm overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3">

                <div class="lg:col-span-1 p-6 lg:border-r border-b lg:border-b-0 border-[var(--color-border)]">
                    <h3 class="text-lg font-semibold text-[var(--color-neutral-900)]">Keamanan Akun</h3>
                    <p class="text-sm text-[var(--color-neutral-600)] mt-1">
                        Perbarui kata sandi Anda secara berkala untuk menjaga keamanan akun.
                    </p>
                </div>

                <div class="lg:col-span-2 p-6">
                    <form wire:submit.prevent="updatePassword" class="space-y-4">
                        <div>
                            <label for="current_password"
                                   class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Kata Sandi
                                Saat Ini</label>
                            <input id="current_password" type="password" wire:model.defer="current_password"
                                   class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)]"
                                   required>
                            @error('current_password')
                            <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="new_password"
                                       class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Kata Sandi
                                    Baru</label>
                                <input id="new_password" type="password" wire:model.defer="new_password"
                                       class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)]"
                                       required>
                                @error('new_password')
                                <span class="text-sm text-[var(--color-error)]">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="new_password_confirmation"
                                       class="block text-sm font-medium mb-1 text-[var(--color-neutral-700)]">Konfirmasi
                                    Kata Sandi</label>
                                <input id="new_password_confirmation" type="password"
                                       wire:model.defer="new_password_confirmation"
                                       class="w-full py-2.5 px-3 border border-[var(--color-neutral-300)] rounded-lg focus:ring-2 focus:ring-[var(--color-primary-500)]"
                                       required>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                    class="bg-[var(--color-primary-600)] hover:bg-[var(--color-primary-700)] text-white font-semibold py-2.5 px-5 rounded-lg transition-all duration-200 flex items-center gap-2"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="updatePassword">Perbarui Kata Sandi</span>
                                <span wire:loading wire:target="updatePassword">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                                    Memperbarui...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white border border-[var(--color-border)] rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-[var(--color-neutral-900)] mb-4">Riwayat Pesanan</h3>
            <div class="text-center text-[var(--color-neutral-600)] py-8">
                <p>Belum ada pesanan yang tercatat.</p>
            </div>
        </div>

    </div>
</div>
