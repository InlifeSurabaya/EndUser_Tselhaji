<div class="min-h-screen flex items-center justify-center py-10 px-4 bg-[var(--color-background)]">

    <div class="w-full max-w-lg">
        <div class="bg-white rounded-xl shadow-md border border-[var(--color-border)]">
            <div class="p-4 sm:p-6 lg:p-8">
                <div class="text-center">
                    <h1 class="text-xl sm:text-2xl font-bold text-[var(--color-neutral-900)] mb-2">
                        Lupa Password Anda?
                    </h1>
                    <p class="text-sm text-[var(--color-neutral-600)] leading-relaxed">
                        Tidak masalah! Masukkan email Anda dan kami akan mengirimkan
                        link untuk mengatur ulang password.
                    </p>
                </div>

                <hr class="my-6 border-dashed border-[var(--color-border)]">

                <form wire:submit="sendResetLink" class="space-y-5">

                    <div class="space-y-1.5">
                        <label for="email"
                               class="block text-sm font-medium text-[var(--color-neutral-700)]">
                            Alamat Email
                        </label>
                        <input type="email" id="email" wire:model.blur="email"
                               class="py-3 px-4 block w-full border border-[var(--color-border)] rounded-lg
                           text-sm focus:border-[var(--color-primary-600)]
                           focus:ring-[var(--color-primary-600)]"
                               required autofocus placeholder="anda@email.com">

                        @error('email')
                        <span class="text-xs text-[var(--color-error)]">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full py-3 px-4 inline-flex justify-center items-center gap-2
                        text-sm font-semibold rounded-lg border border-transparent
                        bg-[var(--color-primary-600)] text-white
                        hover:bg-[var(--color-primary-700)]
                        disabled:opacity-50 disabled:pointer-events-none">

                        <span wire:loading.remove wire:target="sendResetLink">
                            Kirim Link Reset Password
                        </span>

                        <span wire:loading wire:target="sendResetLink">
                            <span
                                class="animate-spin inline-block size-4 border-[3px] border-current border-t-transparent rounded-full"
                                role="status" aria-label="loading"></span>
                            Mengirim...
                        </span>
                    </button>
                </form>
            </div>
        </div>

        <div class="text-center mt-6">
            <p class="text-sm text-[var(--color-neutral-600)]">
                Ingat password Anda?
                <a href="{{ route('login') }}" wire:navigate
                   class="font-medium text-[var(--color-primary-600)] hover:text-[var(--color-primary-700)]">
                    Login di sini
                </a>
            </p>
        </div>
    </div>
</div>
