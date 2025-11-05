{{-- SIDEBAR --}}
<div
    id="sidebar"
    class="fixed top-0 left-0 bottom-0 z-[60] w-64 bg-neutral-900 border-e border-neutral-800 pt-7 pb-10 overflow-y-auto
           transition-all duration-300 transform hidden hs-overlay hs-overlay-vertical hs-overlay-open:translate-x-0
           -translate-x-full lg:block lg:translate-x-0 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full
           [&::-webkit-scrollbar-track]:bg-neutral-700 [&::-webkit-scrollbar-thumb]:bg-neutral-500">

    <div class="px-6">
        <a class="flex-none text-xl font-semibold text-white" href="#" aria-label="Brand">
            MyTelkomsel Admin
        </a>
    </div>

    <nav class="p-6 flex flex-col gap-y-1 text-sm text-neutral-400">
        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-x-3.5 py-2 px-2.5 rounded-lg transition
                {{ Route::is('admin.dashboard') ? 'bg-[var(--color-primary-600)] text-white' : 'hover:bg-neutral-800 hover:text-white' }}">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                <polyline points="9 22 9 12 15 12 15 22" />
            </svg>
            Dashboard
        </a>

        {{-- Manajemen QRIS --}}
        <a href="{{ route('admin.manajemen-qris') }}"
           wire:navigate
           class="flex items-center gap-x-3.5 py-2 px-2.5 rounded-lg transition
                {{ Route::is('admin.manajemen-qris') ? 'bg-[var(--color-primary-600)] text-white' : 'hover:bg-neutral-800 hover:text-white' }}">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                <line x1="16" x2="16" y1="2" y2="6" />
                <line x1="8" x2="8" y1="2" y2="6" />
                <line x1="3" x2="21" y1="10" y2="10" />
                <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01" />
            </svg>
            Manajemen Qris
        </a>

        {{-- Manajemen Produk --}}
        <a href=""
           wire:navigate
           class="flex items-center gap-x-3.5 py-2 px-2.5 rounded-lg transition
                {{ Route::is('manajemen-produk') ? 'bg-[var(--color-primary-600)] text-white' : 'hover:bg-neutral-800 hover:text-white' }}">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                <line x1="16" x2="16" y1="2" y2="6" />
                <line x1="8" x2="8" y1="2" y2="6" />
                <line x1="3" x2="21" y1="10" y2="10" />
            </svg>
            Manajemen Produk
        </a>

        {{-- Pesanan --}}
        <a href=""
           wire:navigate
           class="flex items-center gap-x-3.5 py-2 px-2.5 rounded-lg transition
                {{ Route::is('pesanan') ? 'bg-[var(--color-primary-600)] text-white' : 'hover:bg-neutral-800 hover:text-white' }}">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
            </svg>
            Pesanan
        </a>

        {{-- Pengguna --}}
        <a href=""
           wire:navigate
           class="flex items-center gap-x-3.5 py-2 px-2.5 rounded-lg transition
                {{ Route::is('pengguna') ? 'bg-[var(--color-primary-600)] text-white' : 'hover:bg-neutral-800 hover:text-white' }}">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
            Pengguna
        </a>
    </nav>
</div>
{{-- END SIDEBAR --}}
