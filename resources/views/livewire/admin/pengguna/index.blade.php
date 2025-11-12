<div class="flex flex-col pb-10">

  <div class="pb-5">
    <h1 class="text-2xl font-bold text-neutral-800 uppercase">{{ $subtitle }}</h2>
      <p class="text-sm text-neutral-600 mt-1">Informasi dari semua users.</p>
  </div>

  <div class="-m-1.5 overflow-x-auto" wire:loading.class="opacity-50 pointer-events-none">
    <div class="p-1.5 min-w-full inline-block align-middle">
      <div class="border border-neutral-300 rounded-2xl shadow-sm divide-y divide-neutral-400 bg-neutral-50">
        <div class="flex justify-between flex-wrap items-center px-4 py-3 gap-3">
          {{-- Per Page (kolom 3/12) --}}
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

          <div class="flex items-center gap-2 ml-auto">

            @role(\App\Enum\RoleEnum::SUPER_ADMIN->value)
            <button wire:click="createPage" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-accent-100 text-accent-700 hover:bg-accent-50 focus:outline-hidden focus:bg-accent-100 disabled:opacity-50 disabled:pointer-events-none">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5 lucide lucide-user-plus-icon lucide-user-plus">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <line x1="19" x2="19" y1="8" y2="14" />
                <line x1="22" x2="16" y1="11" y2="11" />
              </svg>Tambah User
            </button>
            @endrole

            {{-- Role Filter (kolom 3/12) --}}
            <div class="md:col-span-3">
              <select wire:model.live.debounce.300ms="roleFilter"
                class="h-10 px-3 bg-white border border-neutral-300 rounded-lg text-sm focus:border-accent-500 focus:ring-accent-500 w-full">
                <option value="{{ \App\Enum\RoleEnum::USER->value }}">Pelanggan</option>
                <option value="{{ \App\Enum\RoleEnum::SUPER_ADMIN->value }}">Admin</option>
                <option value="">Vendor</option>
              </select>
            </div>

            {{-- Search (kolom 6/12) --}}
            <div class="span-6 relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18a7.5 7.5 0 006.15-3.35z" />
                </svg>
              </span>

              <input
                wire:model.live.debounce.300ms="nameItem"
                type="text"
                class="h-10 pl-10 pr-10 block w-full border border-neutral-300 rounded-lg text-sm
                   focus:border-blue-500 focus:ring-blue-500"
                placeholder="Cari user…">

              {{-- @if ($nameItem) --}}
              <button
                wire:click="$set('nameItem', '')"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-neutral-400 hover:text-neutral-700">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
              {{-- @endif --}}
            </div>

          </div>
        </div>

        <div class="overflow-hidden">
          <table class="min-w-full divide-y text-neutral-700">
            <thead class="bg-neutral-100 font-bold border-0">
              <tr>
                <th scope="col" class="px-6 py-3 text-start text-xs uppercase">Name</th>
                <th scope="col" class="px-6 py-3 text-start text-xs uppercase">Email</th>
                <th scope="col" class="px-6 py-3 text-start text-xs uppercase">No Telkomsel</th>
                <th scope="col" class="px-6 py-3 text-start text-xs uppercase">User Dibuat</th>
                <th scope="col" class="px-6 py-3 text-start text-xs uppercase">Role</th>
                <th scope="col" class="px-6 py-3 text-center text-xs uppercase">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-neutral-400 bg-neutral-50">
              @foreach ($users as $user)
              <tr class="border-neutral-300">
                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium">{{ $user->userProfile->fullname ?? '-' }}</td>
                <td class="px-6 py-3 whitespace-nowrap text-sm">{{ $user->email }}</td>
                <td class="px-6 py-3 whitespace-nowrap text-sm">{{ $user->userProfile->phone ?? '-' }}</td>
                <td class="px-6 py-3 whitespace-nowrap text-sm">{{ $user->created_at ?? 'Aneh' }}</td>
                <td class="px-6 py-3 whitespace-nowrap text-sm">
                  <!-- <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-highlight-100 text-highlight-800">User</span> -->
                  <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-accent-100 text-accent-800">Pelanggan</span>
                </td>
                <td class="px-6 py-3 whitespace-nowrap text-end text-sm font-medium">
                  @role(\App\Enum\RoleEnum::SUPER_ADMIN->value)
                  <button wire:click="showPage({{ $user->id }})" class="py-2 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-highlight-300 text-highlight-700 hover:bg-highlight-200 focus:outline-hidden focus:bg-highlight-200 disabled:opacity-50 disabled:pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 lucide lucide-eye-icon lucide-eye">
                      <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                      <circle cx="12" cy="12" r="3" />
                    </svg></button>

                  <button wire:click="editPage({{ $user->id }})" class="py-2 px-2 mx-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-accent-300 text-accent-700 hover:bg-accent-200 focus:outline-hidden focus:bg-accent-200 disabled:opacity-50 disabled:pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 lucide lucide-pencil-icon lucide-pencil">
                      <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                      <path d="m15 5 4 4" />
                    </svg></button>

                  <button
                    @click="Swal.fire({
                      title: 'Hapus Data?',
                      text: 'Data akan dihapus permanen!',
                      icon: 'warning',
                      showCancelButton: true,
                      confirmButtonText: 'Ya, hapus!',
                      cancelButtonText: 'Batal'
                    }).then((r) => { if (r.isConfirmed) { $wire.delete({{ $user->id }}) } })"
                    class="py-2 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-primary-300 text-primary-700 hover:bg-primary-200 focus:outline-hidden focus:bg-primary-200 disabled:opacity-50 disabled:pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 lucide lucide-trash-icon lucide-trash">
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                      <path d="M3 6h18" />
                      <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                    </svg></button>
                  @endrole
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="my-2 mx-5">
          {{ $users->links() }}
        </div>

      </div>
    </div>
  </div>
</div>