<div class="flex min-h-screen bg-neutral-50 text-neutral-800">

    <div class="hidden lg:flex lg:w-1/2 flex-col justify-center items-center p-12 text-white bg-gradient-to-br from-primary-600 to-highlight-700">
        <div class="mb-6">
            <svg width="150" height="auto" viewBox="0 0 1024 341.33" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M1024 170.67c0 94.26-76.41 170.66-170.67 170.66s-170.66-76.4-170.66-170.66S764.1 0 853.33 0 1024 76.41 1024 170.67zm-170.67 85.33c-47.13 0-85.34-38.21-85.34-85.33s38.21-85.34 85.34-85.34 85.33 38.21 85.33 85.34-38.2 85.33-85.33 85.33zM512 170.67c0 94.26-76.41 170.66-170.67 170.66s-170.66-76.4-170.66-170.66S252.1 0 341.33 0 512 76.41 512 170.67zm-170.67 85.33c-47.13 0-85.34-38.21-85.34-85.33s38.21-85.34 85.34-85.34 85.33 38.21 85.33 85.34-38.2 85.33-85.33 85.33zM170.67 341.33C76.41 341.33 0 264.92 0 170.67S76.41 0 170.67 0v341.33z" />
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-center mb-2">Selamat Datang Kembali</h1>
        <p class="text-lg text-center text-white/80">Akses semua layanan digital Telkomsel di satu tempat.</p>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-md">

            <div class="text-center mb-10 lg:hidden">
                <h1 class="text-3xl font-bold text-primary-600">Masuk Akun</h1>
                <p class="text-neutral-600 mt-2">Gunakan nomor Telkomsel Anda.</p>
            </div>

            <h2 class="text-2xl font-bold text-neutral-900 hidden lg:block">Masuk ke Akun Anda</h2>
            <p class="text-neutral-600 mt-2 mb-8 hidden lg:block">Selamat datang! Silakan masukkan detail Anda.</p>

            <form wire:submit="login" class="mt-8 space-y-6">
                <div>
                    <label for="phone" class="block text-sm font-medium text-neutral-700 mb-2">Nomor Ponsel</label>
                    <input wire:model.live="phone" id="phone" type="tel" placeholder="Contoh: 081234567890" required
                           class="py-3 px-4 block w-full **border-neutral-400** rounded-lg text-sm placeholder:text-neutral-400 focus:border-accent-500 focus:ring-accent-500 disabled:opacity-50 disabled:pointer-events-none @error('phone') !border-error focus:!ring-error/50 @enderror">
                    @error('phone') <p class="text-sm text-error mt-2">{{ $message }}</p> @enderror
                </div>

                <div>
                    <div class="flex justify-between items-center">
                        <label for="password" class="block text-sm font-medium text-neutral-700 mb-2">Password</label>
                        <a href="#" class="text-sm font-medium text-accent-600 hover:text-accent-800 transition">Lupa password?</a>
                    </div>
                    <input wire:model="password" id="password" type="password" placeholder="Masukkan password Anda" required
                           class="py-3 px-4 block w-full **border-neutral-400** rounded-lg text-sm placeholder:text-neutral-400 focus:border-accent-500 focus:ring-accent-500 disabled:opacity-50 disabled:pointer-events-none @error('password') !border-error focus:!ring-error/50 @enderror">
                    @error('password') <p class="text-sm text-error mt-2">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all">
                    <div wire:loading.remove wire:target="login">
                        <span>Masuk</span>
                    </div>
                    <div wire:loading wire:target="login">
                        <span class="animate-spin inline-block w-4 h-4 border-[3px] border-current border-t-transparent text-white rounded-full" role="status" aria-label="loading"></span>
                        <span>Memproses...</span>
                    </div>
                </button>
            </form>

            <p class="mt-8 text-center text-sm text-neutral-500">
                Belum punya akun?
                <a href="#" class="font-semibold text-accent-600 hover:text-accent-800 transition">Daftar di sini</a>
            </p>
        </div>
    </div>
</div>
