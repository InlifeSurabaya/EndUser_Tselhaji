{{-- HEADER --}}
<div>
    <header
        class="sticky top-0 inset-x-0 flex flex-wrap sm:justify-start sm:flex-nowrap z-[48] w-full bg-white border-b text-sm py-2.5 sm:py-4 lg:ps-64">
        <nav class="flex basis-full items-center w-full mx-auto px-4 sm:px-6 md:px-8" aria-label="Global">
            <div class="me-5 lg:me-0 lg:hidden">
                {{-- Logo untuk Tampilan Mobile --}}
                <a wire:navigate class="flex-none text-xl font-semibold" href="{{ route('admin.dashboard') }}" aria-label="Brand">MyTelkomsel</a>
            </div>

            <div class="w-full flex items-center justify-end gap-x-3 ms-auto sm:order-3">
                {{-- Menu Profil Pengguna --}}
                <div class="flex flex-row items-center justify-end gap-2">
                    <div x-data="{ dropdownOpen: false }" class="relative inline-block">
                        <button @click="dropdownOpen = !dropdownOpen"
                                class="inline-flex items-center justify-center h-9 w-9 rounded-full bg-neutral-200 text-neutral-700 font-semibold focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <span class="text-sm">A</span>
                        </button>
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform -translate-y-2"
                             class="absolute right-0 w-48 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                            <div class="py-1">
                                <a href="#" wire:navigate
                                   class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100">Profil</a>
                                <a href="#" wire:navigate
                                   class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100">Pengaturan</a>
                                <div class="border-t border-neutral-200 my-1"></div>
                                {{-- Komponen Logout Livewire --}}
                                <livewire:auth.logout/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    {{-- END HEADER --}}

    {{-- SIDEBAR TRIGGER UNTUK MOBILE --}}
    <div class="sticky top-0 inset-x-0 z-20 bg-white border-y px-4 sm:px-6 md:px-8 lg:hidden">
        <div class="flex items-center py-4">
            {{-- Tombol Hamburger untuk memicu sidebar Preline --}}
            <button type="button" class="text-neutral-500 hover:text-neutral-600" data-hs-overlay="#sidebar"
                    aria-controls="sidebar" aria-label="Toggle navigation">
                <span class="sr-only">Toggle Navigation</span>
                <svg class="w-5 h-5" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                          d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                </svg>
            </button>
        </div>
    </div>

</div>
{{-- END SIDEBAR TRIGGER --}}
