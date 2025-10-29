<div class="min-h-screen flex items-center justify-center bg-background px-4 py-12">

    <div class="max-w-md w-full bg-white rounded-xl shadow-lg border border-border overflow-hidden">

        <div class="p-6 sm:p-8 text-center bg-neutral-100/50 border-b border-border">
            <div class="w-16 h-16 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                </svg>
            </div>
            <h2 class="mt-4 text-2xl font-bold text-foreground">
                Verifikasi Alamat Email Anda
            </h2>
        </div>

        <div class="p-6 sm:p-8">

            <p class="text-neutral-700 text-center">
                Terima kasih telah mendaftar! Sebelum melanjutkan, silakan periksa email Anda dan klik tautan verifikasi yang kami kirimkan.
            </p>

            <p class="mt-5 text-neutral-600 text-center text-sm">
                Jika Anda tidak menerima email,
            </p>

            {{-- Menggunakan wire:submit untuk memanggil method 'sendVerification' --}}
            <form wire:submit="sendVerification" class="w-full mt-2">
                <button type="submit"
                        class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-primary-600 text-white hover:bg-primary-700 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all"
                        wire:loading.attr="disabled"
                        wire:target="sendVerification">

                    <span wire:loading wire:target="sendVerification" class="animate-spin inline-block w-4 h-4 border-[3px] border-current border-t-transparent text-white rounded-full" role="status" aria-label="loading"></span>

                    <span wire:loading.remove wire:target="sendVerification">
                        Kirim Ulang Email Verifikasi
                    </span>
                    <span wire:loading wire:target="sendVerification">
                        Mengirim...
                    </span>
                </button>
            </form>
        </div>

        <div class="p-4 bg-neutral-50 border-t border-border text-center">
            {{-- Menggunakan wire:submit untuk memanggil method 'logout' --}}
            <form wire:submit="logout" class="inline">
                <button type="submit"
                        class="text-sm font-medium text-neutral-600 hover:text-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded-md"
                        wire:loading.attr="disabled"
                        wire:target="logout">

                    <span wire:loading.remove wire:target="logout">Logout</span>
                    <span wire:loading wire:target="logout">Keluar...</span>
                </button>
            </form>
        </div>
    </div>
</div>
