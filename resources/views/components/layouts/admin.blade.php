<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Admin Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-100 font-sans  font-normal antialiased">

    {{-- Memanggil komponen header dari file terpisah --}}
    <livewire:components.header-admin />

    {{-- Memanggil komponen sidebar dari file terpisah --}}
    <livewire:components.sidebar-admin />

    {{-- KONTEN UTAMA --}}
    <main class="w-full pt-10 px-4 sm:px-6 md:px-8 lg:ps-72">
        {{ $slot }}
    </main>
    {{-- END KONTEN UTAMA --}}

</body>

</html>
