<nav x-data="{ open: false }" class="bg-white shadow-sm border-b border-neutral-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex-shrink-0">
                <a href="" wire:navigate class="flex items-center space-x-2">
                    <svg class="h-8 w-auto text-primary-600" viewBox="0 0 1024 341.33" fill="currentColor"
                         xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M1024 170.67c0 94.26-76.41 170.66-170.67 170.66s-170.66-76.4-170.66-170.66S764.1 0 853.33 0 1024 76.41 1024 170.67zm-170.67 85.33c-47.13 0-85.34-38.21-85.34-85.33s38.21-85.34 85.34-85.34 85.33 38.21 85.33 85.34-38.2 85.33-85.33 85.33zM512 170.67c0 94.26-76.41 170.66-170.67 170.66s-170.66-76.4-170.66-170.66S252.1 0 341.33 0 512 76.41 512 170.67zm-170.67 85.33c-47.13 0-85.34-38.21-85.34-85.33s38.21-85.34 85.34-85.34 85.33 38.21 85.33 85.34-38.2 85.33-85.33 85.33zM170.67 341.33C76.41 341.33 0 264.92 0 170.67S76.41 0 170.67 0v341.33z"/>
                    </svg>
                    <span class="font-bold text-xl text-neutral-800">MyTelkomsel</span>
                </a>
            </div>

            <div class="hidden md:flex md:items-center md:space-x-8">
                <a wire:navigate href="{{ route('dashboard') }}"
                   class="text-neutral-600 hover:text-primary-600 transition font-medium">Beranda</a>
                <a wire:navigate href="{{ route('dashboard') }}"
                   class="text-neutral-600 hover:text-primary-600 transition font-medium">Paket</a>
                <a wire:navigate href="#"
                   class="text-neutral-600 hover:text-primary-600 transition font-medium">Bantuan</a>
            </div>

            <div class="hidden md:flex items-center space-x-4">
                @guest
                    <a href="{{ route('login') }}" wire:navigate
                       class="text-neutral-600 hover:text-primary-600 transition font-medium">Masuk</a>
                    <a href="{{ route('register') }}" wire:navigate
                       class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary-700 transition">Daftar</a>
                @endguest

                @auth
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen"
                                class="flex items-center space-x-2 focus:outline-none">
                            <span class="font-medium text-neutral-700">{{ auth()->user()->email }}</span>
                            <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-transition
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20">
                            <a wire:navigate href="{{ route('user.profile') }}"
                               class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100">Profile</a>
                            <a wire:navigate href="{{ route('dashboard') }}"
                            class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100">Dashboard</a>
                            <livewire:auth.logout/>
                        </div>
                    </div>
                @endauth
            </div>

            <div class="md:hidden flex items-center">
                <button @click="open = !open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-neutral-600 hover:text-primary-600 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': !open}" class="md:hidden">
        <div class="pt-2 pb-3 space-y-1 px-2">
            <a wire:navigate href="{{ route('dashboard') }}"
               class="block px-3 py-2 rounded-md text-base font-medium text-neutral-700 hover:text-primary-600 hover:bg-neutral-100">Beranda</a>
            <a wire:navigate href="{{ route('dashboard') }}"
               class="block px-3 py-2 rounded-md text-base font-medium text-neutral-700 hover:text-primary-600 hover:bg-neutral-100">Paket</a>
            <a wire:navigate href="#"
               class="block px-3 py-2 rounded-md text-base font-medium text-neutral-700 hover:text-primary-600 hover:bg-neutral-100">Bantuan</a>
        </div>
        <div class="pt-4 pb-3 border-t border-neutral-200 px-2">
            @guest
                <a href="{{ route('login') }}" wire:navigate
                   class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-neutral-700 hover:text-primary-600 hover:bg-neutral-100">Masuk</a>
                <a href="{{ route('register') }}" wire:navigate
                   class="mt-1 block w-full text-left px-3 py-2 rounded-md text-base font-medium text-primary-600 hover:text-primary-700 hover:bg-primary-50">Daftar</a>
            @endguest
            @auth
                <div class="flex items-center px-3 mb-3">
                    <div
                        class="text-base font-medium text-neutral-800">{{ auth()->user()->userProfile()->fullname ?? auth()->user()->email }}</div>
                </div>
                <a wire:navigate href="{{ route('user.profile') }}"
                   class="block px-3 py-2 rounded-md text-base font-medium text-neutral-700 hover:text-primary-600 hover:bg-neutral-100">Profile</a>
                <a wire:navigate href="{{ route('dashboard') }}"
                   class="block px-3 py-2 rounded-md text-base font-medium text-neutral-700 hover:text-primary-600 hover:bg-neutral-100">Dashboard</a>
                <livewire:auth.logout/>
            @endauth
        </div>
    </div>
</nav>
