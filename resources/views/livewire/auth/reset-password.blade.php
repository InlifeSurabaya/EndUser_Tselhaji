<div class="min-h-screen flex items-center justify-center py-10 px-4 bg-[var(--color-background)]">

    <div class="w-full max-w-lg">
        <div class="bg-white rounded-xl shadow-md border border-[var(--color-border)]">
            <div class="p-4 sm:p-6 lg:p-8">
                <div class="text-center">
                    <h1 class="text-xl sm:text-2xl font-bold text-[var(--color-neutral-900)] mb-2">
                        Atur Password Baru
                    </h1>
                    <p class="text-sm text-[var(--color-neutral-600)] leading-relaxed">
                        Masukkan email Anda (untuk konfirmasi) dan password baru Anda.
                    </p>
                </div>

                <hr class="my-6 border-dashed border-[var(--color-border)]">

                <form wire:submit="resetPassword" class="space-y-5">

                    <input type="hidden" wire:model="token">

                    <div class="space-y-1.5">
                        <label for="email"
                               class="block text-sm font-medium text-[var(--color-neutral-700)]">
                            Alamat Email
                        </label>
                        <input type="email" id="email" wire:model.blur="email"
                               class="py-3 px-4 block w-full border border-[var(--color-border)] rounded-lg
                               text-sm bg-[var(--color-neutral-100)] text-[var(--color-neutral-700)]
                               focus:border-[var(--color-primary-600)] focus:ring-[var(--color-primary-600)]"
                               required readonly placeholder="anda@email.com">

                        @error('email')
                        <span class="text-xs text-[var(--color-error)]">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="password"
                               class="block text-sm font-medium text-[var(--color-neutral-700)]">
                            Password Baru
                        </label>
                        <input type="password" id="password" wire:model.blur="password"
                               class="py-3 px-4 block w-full border border-[var(--color-border)] rounded-lg
                               text-sm focus:border-[var(--color-primary-600)]
                               focus:ring-[var(--color-primary-600)]"
                               required autofocus placeholder="Minimal 8 karakter">

                        @error('password')
                        <span class="text-xs text-[var(--color-error)]">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="password_confirmation"
                               class="block text-sm font-medium text-[var(--color-neutral-700)]">
                            Konfirmasi Password Baru
                        </label>
                        <input type="password" id="password_confirmation" wire:model.blur="password_confirmation"
                               class="py-3 px-4 block w-full border border-[var(--color-border)] rounded-lg
                               text-sm focus:border-[var(--color-primary-600)]
                               focus:ring-[var(--color-primary-600)]"
                               required placeholder="Ulangi password baru">
                    </div>

                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full py-3 px-4 inline-flex justify-center items-center gap-2
                            text-sm font-semibold rounded-lg border border-transparent
                            bg-[var(--color-primary-600)] text-white
                            hover:bg-[var(--color-primary-700)]
                            disabled:opacity-50 disabled:pointer-events-none">

                        <span wire:loading.remove wire:target="resetPassword">
                            Reset Password
                        </span>

                        <span wire:loading wire:target="resetPassword">
                            <span
                                class="animate-spin inline-block size-4 border-[3px] border-current border-t-transparent rounded-full"
                                role="status" aria-label="loading"></span>
                            Memproses...
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
