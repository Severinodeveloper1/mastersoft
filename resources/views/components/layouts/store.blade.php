@props(['title' => null, 'description' => null])

<!DOCTYPE html>
<html lang="es-PE">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? "{$title} | {$company->name}" : "{$company->name} | Tecnología y cómputo" }}</title>
    @if ($description)
        <meta name="description" content="{{ $description }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Roboto+Flex:wght@400;500;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex min-h-screen flex-col pb-16 md:pb-0">
    <x-store.header />

    <main class="flex-1">
        {{ $slot }}
    </main>

    <x-store.footer />
    <x-store.mobile-rail />
    <x-store.whatsapp-fab />

    @livewireScripts
</body>
</html>
