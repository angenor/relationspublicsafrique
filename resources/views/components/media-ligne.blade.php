@php
    $typeLabel = \App\Models\Media::$types[$media->type] ?? $media->type;
@endphp

<li class="media-ligne">
    <a href="{{ $media->link }}" class="media-ligne__link" wire:navigate>
        <img src="{{ $media->img }}" alt="" class="media-ligne__thumb" loading="lazy" width="96" height="64">
        <span class="media-ligne__body">
            <span class="media-ligne__type media-card__badge--{{ $media->type }}">{{ $typeLabel }}</span>
            <span class="media-ligne__title">{{ $media->titre }}</span>
            <span class="media-ligne__meta">
                @if ($media->auteurPrincipal){{ $media->auteurPrincipal->name }} · @endif
                {{ optional($media->published_at)->format('d/m/Y') }}
            </span>
        </span>
    </a>
</li>
