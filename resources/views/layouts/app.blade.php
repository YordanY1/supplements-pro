<!DOCTYPE html>
<html lang="{{ app()->getLocale() ?: 'bg' }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="google" content="notranslate">
    <title>{{ $title ?? 'Holistica – Хранителни добавки за ритъм, баланс и здраве' }}</title>

    <meta name="description"
        content="{{ $description ?? 'Holistica предлага висококачествени хранителни добавки, витамини, минерали и натурални продукти за цялостно здраве, енергия и хормонален баланс.' }}">

    @if (!empty($author))
        <meta name="author" content="{{ $author }}">
    @endif

    <meta name="robots"
        content="{{ $robots ?? 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1' }}">

    <meta name="theme-color" content="#ceb1a1">
    <meta name="language" content="bg">
    <meta name="referrer" content="no-referrer-when-downgrade">

    <link rel="canonical" href="{{ request()->url() }}">

    <meta property="og:title" content="{{ $title ?? 'Holistica' }}">
    <meta property="og:description" content="{{ $description ?? '' }}">
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:image" content="{{ $image ?? asset('images/logo-removebg.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="bg_BG">
    <meta property="og:site_name" content="Holistica">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? 'Holistica' }}">
    <meta name="twitter:description" content="{{ $description ?? '' }}">
    <meta name="twitter:image" content="{{ $image ?? asset('images/logo-removebg.png') }}">
    <meta name="twitter:site" content="@Holistica">
    <meta name="twitter:creator" content="@Holistica">

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">


    <link rel="preload" as="image" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">

    @if (!empty($organizationSchema))
        <script type="application/ld+json">
            {!! json_encode(array_merge(['@context' => 'https://schema.org'], $organizationSchema), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endif


    @if (!empty($websiteSchema))
        <script type="application/ld+json">
            {!! json_encode(array_merge(['@context' => 'https://schema.org'], $websiteSchema), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endif


    @if (!empty($productSchema))
        <script type="application/ld+json">
            {!! json_encode(array_merge(['@context' => 'https://schema.org'], $productSchema), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endif

    @if (!empty($articleSchema))
        <script type="application/ld+json">
            {!! json_encode(array_merge(['@context' => 'https://schema.org'], $articleSchema), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endif

    @if (!empty($breadcrumb))
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($breadcrumb)->map(function($item, $i){
                return [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'name' => $item['name'],
                    'item' => $item['url'],
                ];
            })
        ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endif


    @if (!empty($itemListSchema))
        <script type="application/ld+json">
            {!! json_encode(array_merge(['@context' => 'https://schema.org'], $itemListSchema), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endif

    @if (!empty($faqSchema))
        <script type="application/ld+json">
            {!! json_encode(array_merge(['@context' => 'https://schema.org'], $faqSchema), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endif


    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>


<body class="bg-light text-dark font-primary antialiased">

    <livewire:components.header />

    <main class="pt-20 min-h-screen">
        {{ $slot }}
    </main>

    <livewire:components.footer />
    <livewire:components.cookie-consent />

    @livewireScripts

</body>

</html>
