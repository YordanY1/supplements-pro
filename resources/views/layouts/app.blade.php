<!DOCTYPE html>
<html lang="bg">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">


    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen flex flex-col antialiased bg-light font-primary text-dark">
    <livewire:components.header />

    <main class="flex flex-1 flex-col pt-20">
        {{ $slot }}
    </main>

    <livewire:components.footer />
    @livewireScripts
</body>


</html>
