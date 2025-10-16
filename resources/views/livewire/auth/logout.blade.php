<div>
    {{-- Ini untuk dropdown di desktop --}}
    <a href="#" wire:click="logout" class="hidden md:block w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100">
        Keluar
    </a>
    {{-- Ini untuk menu mobile --}}
    <a href="#" wire:click="logout" class="md:hidden mt-1 block px-3 py-2 rounded-md text-base font-medium text-neutral-700 hover:text-primary-600 hover:bg-neutral-100">
        Keluar
    </a>
</div>
