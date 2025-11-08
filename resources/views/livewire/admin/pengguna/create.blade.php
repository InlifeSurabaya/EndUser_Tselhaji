<div wire:loading.class="opacity-50 pointer-events-none">
  <h2 class="text-lg font-bold mb-3">Tambah Pengguna</h2>
  <form wire:submit.prevent="store" class="space-y-3">
    <input type="email" wire:model="email" placeholder="Email" class="border p-2 w-full rounded">
    <input type="password" wire:model="password" placeholder="Password" class="border p-2 w-full rounded">
    <input type="text" wire:model="fullname" placeholder="Nama Lengkap" class="border p-2 w-full rounded">
    <input type="text" wire:model="phone" placeholder="No Telepon" class="border p-2 w-full rounded">
    <input type="text" wire:model="address" placeholder="Alamat" class="border p-2 w-full rounded">

    <div class="flex justify-end">
      <button type="button" wire:click="$set('view','index')" class="px-4 py-2 bg-gray-300 rounded mr-2">Kembali</button>
      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
    </div>
  </form>
</div>
