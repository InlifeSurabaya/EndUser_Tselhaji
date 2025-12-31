<div class="p-4 sm:p-6 lg:p-8 space-y-6 bg-neutral-50 min-h-screen">

  {{-- HEADER SECTION --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h1 class="text-2xl font-bold text-neutral-900 uppercase">{{ $subtitle }}</h1>
      <p class="text-sm text-neutral-600 mt-1">Informasi dari semua users.</p>
    </div>

    <div class="mt-4 sm:mt-0">
      @role(\App\Enum\RoleEnum::SUPER_ADMIN->value)
      <button wire:click="createPage"
        class="inline-flex items-center justify-center gap-2 px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 transition-all"
        wire:loading.class="opacity-50 pointer-events-none">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          class="size-5 lucide lucide-user-plus-icon lucide-user-plus">
          <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
          <circle cx="9" cy="7" r="4" />
          <line x1="19" x2="19" y1="8" y2="14" />
          <line x1="22" x2="16" y1="11" y2="11" />
        </svg>
        Tambah User
      </button>
      @endrole
    </div>
  </div>

  {{-- FILTER & SEARCH SECTION --}}
  <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center" wire:loading.class="opacity-50 pointer-events-none">

    {{-- 1. Per Page --}}
    <div class="md:col-span-3 flex items-center gap-2">
      <label for="perPage" class="text-sm text-neutral-700 shrink-0">Tampilkan</label>
      <select id="perPage" wire:model.live.debounce.300ms="perPage"
        class="h-10 px-3 bg-white border border-neutral-300 rounded-lg text-sm focus:border-accent-500 focus:ring-accent-500">
        <option value="10">10</option>
        <option value="20">20</option>
        <option value="30">30</option>
        <option value="50">50</option>
      </select>
      <span class="text-sm text-neutral-700 shrink-0">entri</span>
    </div>

    {{-- 2. Search --}}
    <div class="md:col-span-6 relative">
      <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18a7.5 7.5 0 006.15-3.35z" />
        </svg>
      </span>

      <input wire:model.live.debounce.300ms="nameItem" type="text"
        class="h-10 pl-10 pr-10 block w-full border border-neutral-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
        placeholder="Cari user…">

      {{-- Tombol Clear Search --}}
      @if ($nameItem)
      <button wire:click="$set('nameItem', '')"
        class="absolute inset-y-0 right-0 flex items-center pr-3 text-neutral-400 hover:text-neutral-700">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
      @endif
    </div>

    {{-- 3. Role Filter --}}
    <div class="md:col-span-3">
      <div class="grid grid-cols-2">
        <div class="p-auto m-auto">
          <select wire:model.live.debounce.300ms="roleFilter"
            class="h-10 px-3 bg-white border border-neutral-300 rounded-lg text-sm focus:border-accent-500 focus:ring-accent-500 w-full">
            <option value="">Semua Role</option> {{-- Menambahkan opsi default --}}
            <option value="{{ \App\Enum\RoleEnum::USER->value }}">User</option>
            <option value="{{ \App\Enum\RoleEnum::SUPER_ADMIN->value }}">Super Admin</option>
            {{-- <option value="">Vendor</option> --}}
          </select>
        </div>

        <div class="p-auto m-auto">
          <select wire:model.live.debounce.300ms="banFilter"
            class="h-10 px-3 bg-white border border-neutral-300 rounded-lg text-sm focus:border-accent-500 focus:ring-accent-500 w-full">
            <option value="">Semua Status</option>
            <option value="active">Normal</option>
            <option value="banned">Banned</option>
          </select>
        </div>
      </div>
    </div>

  </div>

  {{-- TABLE SECTION --}}
  <div class="flex flex-col" wire:loading.class="opacity-50 pointer-events-none">
    <div class="-m-1.5 overflow-x-auto">
      <div class="p-1.5 min-w-full inline-block align-middle">
        <div class="border border-neutral-200 rounded-lg shadow-sm overflow-hidden bg-white">
          <table class="min-w-full divide-y divide-neutral-200">
            <thead class="bg-neutral-100">
              <tr>
                <th scope="col" class="px-6 py-3 text-start text-xs font-semibold text-neutral-600 uppercase">Name</th>
                <th scope="col" class="px-6 py-3 text-start text-xs font-semibold text-neutral-600 uppercase">Email</th>
                <th scope="col" class="px-6 py-3 text-start text-xs font-semibold text-neutral-600 uppercase">No
                  Telkomsel</th>
                <th scope="col" class="px-6 py-3 text-start text-xs font-semibold text-neutral-600 uppercase">User
                  Dibuat</th>
                <th scope="col" class="px-6 py-3 text-start text-xs font-semibold text-neutral-600 uppercase">Role</th>
                <th scope="col" class="px-6 py-3 text-end text-xs font-semibold text-neutral-600 uppercase">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200">
              @forelse ($users as $user)
              <tr class="hover:bg-neutral-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900">
                  {{ $user->userProfile->fullname ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">
                  {{ $user->email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">
                  {{ $user->userProfile->phone ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700">
                  {{ $user->created_at ?? 'Aneh' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                  @forelse ($user->roles as $role)
                  <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium 
                  @if ($user->isBanned())
                      bg-primary-800 text-primary-100"> BANNED
                    @else
                    @if($role->name === \App\Enum\RoleEnum::SUPER_ADMIN->value)
                    bg-primary-100 text-primary-800
                    @else
                    bg-accent-100 text-accent-800
                    @endif
                    ">
                    {{ strtoupper($role->name) }}
                    @endif
                  </span>
                  @empty
                  <span class="text-neutral-400 text-xs">None</span>
                  @endforelse
                </td>

                {{-- BUTTON ACTIONS --}}
                <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                  @role(\App\Enum\RoleEnum::SUPER_ADMIN->value)

                  {{-- Tombol Lihat (Eye) --}}
                  <button wire:click="showPage({{ $user->id }})"
                    class="py-2 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-highlight-300 text-highlight-700 hover:bg-highlight-200 focus:outline-hidden focus:bg-highlight-200 disabled:opacity-50 disabled:pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="size-4 lucide lucide-eye-icon lucide-eye">
                      <path
                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                  </button>

                  {{-- Tombol Edit (Pencil) --}}
                  <button wire:click="editPage({{ $user->id }})"
                    class="py-2 px-2 mx-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-accent-300 text-accent-700 hover:bg-accent-200 focus:outline-hidden focus:bg-accent-200 disabled:opacity-50 disabled:pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="size-4 lucide lucide-pencil-icon lucide-pencil">
                      <path
                        d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                      <path d="m15 5 4 4" />
                    </svg>
                  </button>

                  {{-- Tombol Ban --}}
                  @if ($user->isBanned())
                  {{-- UNBAN BUTTON --}}
                  <button @click="Swal.fire({
                              title: 'Unban User?',
                              text: 'User akan dapat mengakses sistem kembali.',
                              icon: 'question',
                              input: 'text',
                              inputLabel: 'Alasan Ban',
                              inputPlaceholder: 'Masukkan alasan ban...',
                              showCancelButton: true,
                              confirmButtonText: 'Ya, Unban',
                              cancelButtonText: 'Batal',
                              preConfirm: (reason) => {
                                  if (!reason) {
                                      Swal.showValidationMessage('Alasan wajib diisi');
                                  }
                                  return reason;
                              }
                          }).then((r) => {
                              if (r.isConfirmed) {
                                  $wire.unbanUser({{ $user->id }}, r.value)
                              }
                          })" class="py-2 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg
                          bg-emerald-300 text-emerald-700 hover:bg-emerald-200">

                    {{-- icon unlock --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 7a3 3 0 00-6 0v4m-2 0h10v10H7V11z" />
                    </svg>
                  </button>
                  @else
                  {{-- BAN BUTTON --}}
                  <button @click="Swal.fire({
                              title: 'Ban User?',
                              text: 'User akan diblokir dan tidak bisa mengakses sistem.',
                              icon: 'warning',
                              input: 'text',
                              inputLabel: 'Alasan Ban',
                              inputPlaceholder: 'Masukkan alasan ban...',
                              showCancelButton: true,
                              confirmButtonText: 'Ya, Ban User',
                              cancelButtonText: 'Batal',
                              preConfirm: (reason) => {
                                  if (!reason) {
                                      Swal.showValidationMessage('Alasan wajib diisi');
                                  }
                                  return reason;
                              }
                          }).then((r) => {
                              if (r.isConfirmed) {
                                  $wire.banUser({{ $user->id }}, r.value)
                              }
                          })" class="py-2 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg
                          bg-primary-300 text-primary-700 hover:bg-primary-200">

                    {{-- icon ban --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <circle cx="12" cy="12" r="10" />
                      <path d="M4.9 4.9 19.1 19.1" />
                    </svg>
                  </button>
                  @endif

                  @endrole
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="px-6 py-4 text-center text-sm text-neutral-500">
                  Tidak ada data user ditemukan.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- PAGINATION SECTION --}}
  <div class="mt-4" wire:loading.class="opacity-50 pointer-events-none">
    {{ $users->links() }}
  </div>

</div>