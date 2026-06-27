@php
    /** @var \App\Models\Event $event */
    $cancelled = $event->status === 'cancelled';
    $statusKey = $cancelled ? 'cancelled' : $event->temporal_status;
    $statusLabel = $cancelled ? 'Annulé' : $event->temporal_status_label;

    // CTA contextuel (cf. contrat public-routes, matrice CTA) via l'accessor cta.
    $ctaHref = $event->link;
    $ctaTarget = '';
    $ctaLabel = 'Voir détails';
    $ctaClass = 'event-card__cta';
    switch ($event->cta) {
        case 'register_internal':
            $ctaLabel = 'S\'inscrire';
            break;
        case 'register_external':
            $ctaHref = $event->registration_url;
            $ctaTarget = '_blank';
            $ctaLabel = 'S\'inscrire';
            break;
        case 'view_replay':
            $ctaLabel = 'Voir le replay';
            break;
        default:
            $ctaClass = 'event-card__cta event-card__cta--ghost';
            break;
    }
@endphp

<article class="event-card">
    <a href="{{ $event->link }}" class="event-card__cover" wire:navigate>
        <img src="{{ $event->img }}" alt="{{ $event->title }}" loading="lazy">
        <span class="event-card__date">
            <span class="day">{{ $event->start_date->format('d') }}</span>
            <span class="month">{{ $event->start_date->translatedFormat('M') }}</span>
        </span>
        <span class="event-card__badge event-card__badge--{{ $statusKey }}">{{ $statusLabel }}</span>
    </a>

    <div class="event-card__body">
        <div class="event-card__meta">
            <span><i class="fal fa-calendar"></i>{{ $event->start_date->format('d/m/Y') }}</span>
            @if ($event->format_label)
                <span><i class="fal fa-map-marker-alt"></i>{{ $event->format_label }}</span>
            @endif
            @if ($event->category)
                <span><i class="fal fa-tag"></i>{{ $event->category->name }}</span>
            @endif
        </div>

        <h3 class="event-card__title">
            <a href="{{ $event->link }}" wire:navigate>{{ $event->title }}</a>
        </h3>

        @if ($event->resume_text)
            <p class="event-card__excerpt">{{ \Illuminate\Support\Str::limit($event->resume_text, 130) }}</p>
        @endif

        @if (! $cancelled && $event->temporal_status === 'upcoming')
            <div class="event-countdown event-card__countdown"
                 data-countdown
                 data-start="{{ $event->start_date->toIso8601String() }}"
                 aria-label="Compte à rebours avant l'événement"></div>
        @endif

        <div class="event-card__footer">
            <a href="{{ $ctaHref }}" class="{{ $ctaClass }}"
               @if ($ctaTarget) target="{{ $ctaTarget }}" rel="noopener" @else wire:navigate @endif>
                {{ $ctaLabel }} <i class="fal fa-arrow-right"></i>
            </a>
            @if ($event->price > 0)
                <span class="event-card__note">{{ number_format((float) $event->price, 0, ',', ' ') }} €</span>
            @else
                <span class="event-card__note">Gratuit</span>
            @endif
        </div>
    </div>
</article>
