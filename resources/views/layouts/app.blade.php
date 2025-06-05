<!DOCTYPE html>
<html lang="bg">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen flex flex-col antialiased bg-card text-text">
    <livewire:components.header />

    <main class="flex flex-1 flex-col">
        {{ $slot }}
    </main>
    <livewire:components.footer />
    @livewireScripts
</body>

</html>
