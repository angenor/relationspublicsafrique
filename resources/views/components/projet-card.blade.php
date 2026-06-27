@php
    $zone = $projet->zone_label;
@endphp

<article class="projet-card">
    <a href="{{ route('projets.show', $projet->slug) }}" class="projet-card__cover" wire:navigate>
        <img src="{{ $projet->visuel_card_url }}" alt="{{ $projet->titre }}" loading="lazy">
        <span class="projet-card__badge projet-card__badge--{{ $projet->statut }}">{{ $projet->statut_label }}</span>
    </a>
    <div class="projet-card__body">
        <div class="projet-card__meta">
            @foreach ($projet->categories as $cat)
                <span class="projet-card__tag">{{ $cat->name }}</span>
            @endforeach
            @if ($zone)
                <span class="projet-card__zone"><i class="fas fa-map-marker-alt"></i> {{ $zone }}</span>
            @endif
        </div>
        <h3 class="projet-card__title">
            <a href="{{ route('projets.show', $projet->slug) }}" wire:navigate>{{ $projet->titre }}</a>
        </h3>
        @if ($projet->resume)
            <p class="projet-card__excerpt">{{ \Illuminate\Support\Str::limit($projet->resume, 140) }}</p>
        @endif
        <a href="{{ route('projets.show', $projet->slug) }}" class="projet-card__cta" wire:navigate>
            Découvrir <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</article>
