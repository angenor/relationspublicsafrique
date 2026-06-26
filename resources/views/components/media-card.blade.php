@php
    $typeLabel = \App\Models\Media::$types[$media->type] ?? $media->type;
    $auteur = $media->auteurPrincipal?->name;
@endphp

<article class="media-card">
    <a href="{{ $media->link }}" class="media-card__cover" wire:navigate>
        <img src="{{ $media->img }}" alt="{{ $media->titre }}" loading="lazy">
        <span class="media-card__badge media-card__badge--{{ $media->type }}">{{ $typeLabel }}</span>
    </a>
    <div class="media-card__body">
        <h3 class="media-card__title">
            <a href="{{ $media->link }}" wire:navigate>{{ $media->titre }}</a>
        </h3>
        @if ($media->chapo)
            <p class="media-card__excerpt">{{ \Illuminate\Support\Str::limit($media->chapo, 110) }}</p>
        @endif
        <div class="media-card__meta">
            @if ($auteur)
                <span class="media-card__author">{{ $auteur }}</span>
            @endif
            @if ($media->durationHuman)
                <span><i class="fas fa-clock"></i> {{ $media->durationHuman }}</span>
            @elseif ($media->readingTimeHuman)
                <span><i class="fas fa-book-open"></i> {{ $media->readingTimeHuman }}</span>
            @endif
        </div>
    </div>
</article>
