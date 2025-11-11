<div class="flex min-h-screen bg-neutral-50 text-neutral-800">

    {{-- Kolom Kiri - Branding --}}
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-center items-center p-12 text-white bg-gradient-to-br from-primary-600 to-highlight-700">
        <div class="mb-6">
            {{-- Logo Telkomsel --}}
            <img src="{{ asset('images/logo/logo-telkomsel-text-putih.png') }}" alt="Logo" class="h-20 w-auto">
        </div>
        <h1 class="text-3xl font-bold text-center mb-2">Bergabung dengan MyTelkomsel</h1>
        <p class="text-lg text-center text-white/80">Nikmati berbagai layanan digital dan penawaran eksklusif.</p>
    </div>

    {{-- Kolom Kanan - Form Register --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-md">

            {{-- Header untuk Mobile --}}
            <div class="text-center mb-10 lg:hidden">
                <h1 class="text-3xl font-bold text-primary-600">Daftar Akun</h1>
                <p class="text-neutral-600 mt-2">Buat akun MyTelkomsel Anda.</p>
            </div>

            {{-- Header untuk Desktop --}}
            <h2 class="text-2xl font-bold text-neutral-900 hidden lg:block">Buat Akun Baru</h2>
            <p class="text-neutral-600 mt-2 mb-8 hidden lg:block">Isi data diri Anda untuk membuat akun.</p>

            <form wire:submit="register" class="mt-8 space-y-5">
                {{-- Input Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-neutral-700 mb-2">Email</label>
                    <input wire:model.live="email" id="email" type="email" placeholder="Contoh: user@example.com" required
                           class="py-3 px-4 block w-full border border-neutral-300 rounded-lg text-sm placeholder:text-neutral-400 focus:border-primary-500 disabled:opacity-50 disabled:pointer-events-none transition @error('email') !border-error @enderror">
                    @error('email') <p class="text-sm text-error mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- Input Password dengan Toggle --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-neutral-700 mb-2">Password</label>
                    <div class="relative">
                        <input wire:model="password" id="hs-toggle-password" type="password" placeholder="Minimal 8 karakter" required
                               class="py-3 px-4 block w-full border border-neutral-300  rounded-lg text-sm placeholder:text-neutral-400 focus:border-primary-500 disabled:opacity-50 disabled:pointer-events-none transition @error('password') !border-error @enderror">
                        <button type="button" data-hs-toggle-password='{"target": "#hs-toggle-password"}' class="absolute top-0 end-0 p-3.5 rounded-e-md text-neutral-400 hover:text-primary-600 transition">
                            <svg class="flex-shrink-0 size-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path class="hs-password-active:hidden" d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                                <path class="hs-password-active:hidden" d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
                                <path class="hs-password-active:hidden" d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                                <line class="hs-password-active:hidden" x1="2" x2="22" y1="2" y2="22"></line>
                                <path class="hidden hs-password-active:block" d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                <circle class="hidden hs-password-active:block" cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    @error('password') <p class="text-sm text-error mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- Input Konfirmasi Password dengan Toggle --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-neutral-700 mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <input wire:model="password_confirmation" id="hs-toggle-password-confirmation" type="password" placeholder="Ulangi password Anda" required
                               class="py-3 px-4 block w-full border border-neutral-300  rounded-lg text-sm placeholder:text-neutral-400 focus:border-primary-500 disabled:opacity-50 disabled:pointer-events-none transition">
                        <button type="button" data-hs-toggle-password='{"target": "#hs-toggle-password-confirmation"}' class="absolute top-0 end-0 p-3.5 rounded-e-md text-neutral-400 hover:text-primary-600 transition">
                            <svg class="flex-shrink-0 size-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path class="hs-password-active:hidden" d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                                <path class="hs-password-active:hidden" d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
                                <path class="hs-password-active:hidden" d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                                <line class="hs-password-active:hidden" x1="2" x2="22" y1="2" y2="22"></line>
                                <path class="hidden hs-password-active:block" d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                <circle class="hidden hs-password-active:block" cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation') <p class="text-sm text-error mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- Tombol Submit --}}
                <button type="submit"
                        class="w-full py-3 px-4 mt-2 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all">
                    <div wire:loading.remove wire:target="register">
                        <span>Daftar Sekarang</span>
                    </div>
                    <div wire:loading wire:target="register" class="flex items-center gap-x-2">
                        <span class="animate-spin inline-block w-4 h-4 border-[3px] border-current border-t-transparent text-white rounded-full" role="status" aria-label="loading"></span>
                        <span>Proses...</span>
                    </div>
                </button>
            </form>

            {{-- Link Login --}}
            <p class="mt-8 text-center text-sm text-neutral-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" wire:navigate class="font-semibold text-accent-600 hover:text-accent-800 transition">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
