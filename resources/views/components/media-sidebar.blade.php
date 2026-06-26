@php
    $categoriesMedia = $categoriesMedia
        ?? \App\Models\Category::query()->where('online', 1)->where('type', 'media')->orderBy('position')->get();
    $current = request()->route()?->getName();
    $typesMedia = \App\Models\Media::$types;
    $activeType = request('type');
    $activeCat = request('cat');
@endphp

<aside class="media-sidebar" id="media-sidebar" aria-label="Navigation de la section Média">
    <div class="media-sidebar__brand">
        <a href="{{ route('media.home') }}" wire:navigate>
            <img src="{{ asset('logos/logo1.png') }}" alt="Relations Publics Afrique — Média" class="media-sidebar__logo">
        </a>
        <span class="media-sidebar__tagline">Newsroom</span>
    </div>

    <nav class="media-sidebar__nav">
        <a class="media-sidebar__link {{ $current === 'media.home' ? 'is-active' : '' }}"
           href="{{ route('media.home') }}" wire:navigate>
            <i class="fas fa-house"></i> <span>Accueil</span>
        </a>
        <a class="media-sidebar__link {{ $current === 'media.index' && ! $activeType && ! $activeCat ? 'is-active' : '' }}"
           href="{{ route('media.index') }}">
            <i class="fas fa-compass"></i> <span>Explorer</span>
        </a>

        <p class="media-sidebar__heading">Formats</p>
        @foreach ($typesMedia as $key => $label)
            <a class="media-sidebar__link {{ $activeType === $key ? 'is-active' : '' }}"
               href="{{ route('media.index', ['type' => $key]) }}">
                <span>{{ $label }}</span>
            </a>
        @endforeach

        @if ($categoriesMedia->isNotEmpty())
            <p class="media-sidebar__heading">Thématiques</p>
            @foreach ($categoriesMedia as $cat)
                <a class="media-sidebar__link {{ $activeCat === $cat->slug ? 'is-active' : '' }}"
                   href="{{ route('media.index', ['cat' => $cat->slug]) }}">
                    <span>{{ $cat->name }}</span>
                </a>
            @endforeach
        @endif
    </nav>

    <div class="media-sidebar__foot">
        <a href="{{ route('accueil') }}" class="small">← Site principal RP Afrique</a>
    </div>
</aside>
