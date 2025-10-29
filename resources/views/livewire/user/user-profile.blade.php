<div class="max-w-7xl mx-auto my-7 px-4 sm:px-6 lg:px-8">

    @if(!$user->hasVerifiedEmail())
        <div class="mb-4 p-4 rounded-lg bg-yellow-50 border border-yellow-200 flex items-center justify-between">
            <div>
                <p class="text-sm text-yellow-700">
                    Email Anda belum terverifikasi.
                    <br>Silakan verifikasi untuk keamanan akun.
                </p>
            </div>
            <button wire:click="sendVerificationEmail"
                    class="text-sm font-medium text-primary-600 hover:text-primary-700 underline"
                    wire:loading.attr="disabled"
                    wire:target="sendVerificationEmail">
                Kirim Ulang Verifikasi
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri: Info Pengguna & Avatar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md border border-neutral-200 overflow-hidden">
                <div class="p-6">
                    <form wire:submit.prevent="updateProfile">
                        <div class="flex flex-col items-center">
                            <!-- Avatar Preview -->
                            <div class="relative mb-4">
                                @if ($avatar)
                                    <!-- Preview Avatar Baru -->
                                    <img class="h-32 w-32 rounded-full object-cover" src="{{ $avatar->temporaryUrl() }}"
                                         alt="Preview Avatar">
                                @elseif ($existingAvatar)
                                    <!-- Avatar Saat Ini -->
                                    <img class="h-32 w-32 rounded-full object-cover"
                                         src="{{ Storage::url($existingAvatar) }}" alt="Avatar Pengguna">
                                @else
                                    <!-- Avatar Default -->
                                    <div
                                        class="h-32 w-32 rounded-full bg-neutral-200 flex items-center justify-center text-neutral-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor" class="w-16 h-16">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A1.75 1.75 0 0 1 18 22H6a1.75 1.75 0 0 1-1.499-1.882Z"/>
                                        </svg>
                                    </div>
                                @endif
                                <!-- Indikator Loading Avatar -->
                                <div wire:loading wire:target="avatar"
                                     class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-full">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                                </div>
                            </div>

                            <h2 class="text-xl font-semibold text-neutral-900">{{ $user->userProfile->fullname ?? 'Nama Pengguna' }}</h2>
                            <p class="text-sm text-neutral-600 mb-4">{{ $user->email }}</p>

                            <!-- Input Upload Avatar -->
                            <div>
                                <label for="avatar-upload"
                                       class="cursor-pointer text-sm font-medium text-accent-600 hover:text-accent-700">
                                    Ganti Foto
                                </label>
                                <input id="avatar-upload" type="file" wire:model="avatar" class="sr-only">
                                @error('avatar')
                                <span class="text-sm text-error mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Tab Pengaturan -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md border border-neutral-200">

                <!-- Navigasi Tab -->
                <div class="border-b border-neutral-200">
                    <nav class="flex gap-x-6 px-6" role="tablist">
                        <button type="button"
                                class="hs-tab-active:border-primary-600 hs-tab-active:text-primary-600 py-4 px-1 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm text-neutral-600 hover:text-primary-600"
                                data-hs-tab="#profile-tab">
                            Profil
                        </button>
                        <button type="button"
                                class="hs-tab-active:border-primary-600 hs-tab-active:text-primary-600 py-4 px-1 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm text-neutral-600 hover:text-primary-600"
                                data-hs-tab="#security-tab">
                            Keamanan
                        </button>
                        <button type="button"
                                class="hs-tab-active:border-primary-600 hs-tab-active:text-primary-600 py-4 px-1 inline-flex items-center gap-x-2 border-b-2 border-transparent text-sm text-neutral-600 hover:text-primary-600"
                                data-hs-tab="#orders-tab">
                            Pesanan Saya
                        </button>
                    </nav>
                </div>

                <!-- Konten Tab -->
                <div class="p-6 hs-tab-content">
                    <!-- Tab Profil -->
                    <div id="profile-tab">
                        <form wire:submit.prevent="updateProfile">
                            <div class="space-y-4">
                                <!-- Nama Lengkap -->
                                <div>
                                    <label for="fullname" class="block text-sm font-medium mb-2 text-neutral-700">Nama
                                        Lengkap</label>
                                    <input type="text" id="fullname" wire:model.defer="fullname"
                                           class="py-3 px-4 block w-full border border-neutral-300 rounded-lg text-sm focus:border-primary-500 focus:ring-primary-500"
                                           placeholder="Masukkan nama lengkap Anda">
                                    @error('fullname')
                                    <span class="text-sm text-error mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Jenis Kelamin -->
                                <div>
                                    <label for="gender" class="block text-sm font-medium mb-2 text-neutral-700">Jenis
                                        Kelamin</label>
                                    <select id="gender" wire:model.defer="gender"
                                            class="py-3 px-4 block w-full border border-neutral-300 rounded-lg text-sm focus:border-primary-500 focus:ring-primary-500">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="male">Laki-laki</option>
                                        <option value="female">Perempuan</option>
                                        <option value="other">Lainnya</option>
                                    </select>
                                    @error('gender')
                                    <span class="text-sm text-error mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Tanggal Lahir -->
                                <div>
                                    <label for="birth_date" class="block text-sm font-medium mb-2 text-neutral-700">Tanggal
                                        Lahir</label>
                                    <input type="date" id="birth_date" wire:model.defer="birth_date"
                                           class="py-3 px-4 block w-full border border-neutral-300 rounded-lg text-sm focus:border-primary-500 focus:ring-primary-500">
                                    @error('birth_date')
                                    <span class="text-sm text-error mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Nomor Telepon -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium mb-2 text-neutral-700">Nomor
                                        Telepon</label>
                                    <input type="tel" id="phone" wire:model.defer="phone"
                                           class="py-3 px-4 block w-full border border-neutral-300 rounded-lg text-sm focus:border-primary-500 focus:ring-primary-500"
                                           placeholder="cth: 08123456789">
                                    @error('phone')
                                    <span class="text-sm text-error mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Alamat -->
                                <div>
                                    <label for="address"
                                           class="block text-sm font-medium mb-2 text-neutral-700">Alamat</label>
                                    <textarea id="address" wire:model.defer="address" rows="3"
                                              class="py-3 px-4 block w-full border border-neutral-300 rounded-lg text-sm focus:border-primary-500 focus:ring-primary-500"
                                              placeholder="Masukkan alamat lengkap Anda"></textarea>
                                    @error('address')
                                    <span class="text-sm text-error mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Tombol Simpan Profil -->
                            <div class="mt-6 flex justify-end">
                                <button type="submit"
                                        class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50 disabled:pointer-events-none"
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="updateProfile">
                                        Simpan Perubahan
                                    </span>
                                    <span wire:loading wire:target="updateProfile">
                                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                                        Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tab Keamanan -->
                    <div id="security-tab" class="hidden">
                        <form wire:submit.prevent="updatePassword">
                            <div class="space-y-4">
                                <!-- Kata Sandi Saat Ini -->
                                <div>
                                    <label for="current_password"
                                           class="block text-sm font-medium mb-2 text-neutral-700">Kata Sandi Saat
                                        Ini</label>
                                    <input type="password" id="current_password" wire:model.defer="current_password"
                                           class="py-3 px-4 block w-full border border-neutral-300 rounded-lg text-sm focus:border-primary-500 focus:ring-primary-500"
                                           required>
                                    @error('current_password')
                                    <span class="text-sm text-error mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Kata Sandi Baru -->
                                <div>
                                    <label for="new_password" class="block text-sm font-medium mb-2 text-neutral-700">Kata
                                        Sandi Baru</label>
                                    <input type="password" id="new_password" wire:model.defer="new_password"
                                           class="py-3 px-4 block w-full border border-neutral-300 rounded-lg text-sm focus:border-primary-500 focus:ring-primary-500"
                                           required>
                                    @error('new_password')
                                    <span class="text-sm text-error mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Konfirmasi Kata Sandi Baru -->
                                <div>
                                    <label for="new_password_confirmation"
                                           class="block text-sm font-medium mb-2 text-neutral-700">Konfirmasi Kata Sandi
                                        Baru</label>
                                    <input type="password" id="new_password_confirmation"
                                           wire:model.defer="new_password_confirmation"
                                           class="py-3 px-4 block w-full border border-neutral-300 rounded-lg text-sm focus:border-primary-500 focus:ring-primary-500"
                                           required>
                                </div>
                            </div>

                            <!-- Tombol Simpan Kata Sandi -->
                            <div class="mt-6 flex justify-end">
                                <button type="submit"
                                        class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50 disabled:pointer-events-none"
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="updatePassword">
                                        Perbarui Kata Sandi
                                    </span>
                                    <span wire:loading wire:target="updatePassword">
                                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                                        Memperbarui...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tab Pesanan -->
                    <div id="orders-tab" class="hidden">
                        <div class="text-center py-10">
                            <h3 class="text-lg font-semibold text-neutral-800">Riwayat Pesanan Anda</h3>
                            <p class="mt-2 text-sm text-neutral-600">Semua pesanan Anda akan muncul di sini.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

