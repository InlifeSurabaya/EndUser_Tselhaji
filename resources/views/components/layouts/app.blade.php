<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Tsel Haji' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-neutral-50 font-sans antialiased">
    <div class="flex flex-col min-h-screen">
        <livewire:components.navbar/>

        <main class="flex-grow">
            {{ $slot }}
        </main>

        <livewire:components.footer/>
    </div>
</body>
</html>
