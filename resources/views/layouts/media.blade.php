<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Média') | Relations Publics Afrique</title>
    <meta name="description" content="@yield('meta_description', 'La newsroom de Relations Publics Afrique : articles, interviews, podcasts, vidéos et reportages.')">

    <link rel="shortcut icon" href="{{ asset('logos/faveicon.png') }}" type="image/x-icon">

    @yield('meta')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('front/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/assets/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/assets/css/theme-variables.css') }}">

    @livewireStyles
    @vite(['resources/css/media.css', 'resources/js/media.js'])

    @yield('css')
</head>
<body class="media-body">
    <div class="media-layout">
        <button class="media-burger d-lg-none" type="button" aria-label="Ouvrir le menu"
                aria-controls="media-sidebar"
                onclick="document.body.classList.toggle('media-sidebar-open')">
            <i class="fas fa-bars"></i>
        </button>

        <x-media-sidebar />

        <div class="media-sidebar-backdrop d-lg-none"
             onclick="document.body.classList.remove('media-sidebar-open')"
             aria-hidden="true"></div>

        <main class="media-main" id="media-main">
            @yield('content')

            <footer class="media-footer">
                <div class="media-footer__inner">
                    <p class="mb-0 small text-muted">
                        © {{ date('Y') }} Relations Publics Afrique —
                        <a href="{{ route('accueil') }}">Site principal</a>
                    </p>
                </div>
            </footer>
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
