@extends('layouts.default')

@section('title', $event->title)

@section('meta')
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($event->resume_text), 160) }}">
    <link rel="canonical" href="{{ $event->link }}">
@endsection

@section('css')
    @vite(['resources/css/events.css', 'resources/js/events.js'])
@endsection

@section('content')
    @php
        $cancelled = $event->status === 'cancelled';
        $statusKey = $cancelled ? 'cancelled' : $event->temporal_status;
        $statusLabel = $cancelled ? 'Annulé' : $event->temporal_status_label;
        $cta = $event->cta;
        $alreadyRegistered = auth()->check() && $event->isUserRegistered(auth()->id());
    @endphp

    <article class="event-detail">
        {{-- En-tête : visuel + identité --}}
        <header class="event-detail__hero">
            <div class="event-detail__hero-media">
                <img src="{{ $event->img }}" alt="{{ $event->title }}" loading="eager">
                <span class="event-card__badge event-card__badge--{{ $statusKey }}">{{ $statusLabel }}</span>
            </div>
            <div class="container">
                <div class="event-detail__hero-body">
                    <nav class="event-detail__crumbs" aria-label="Fil d'Ariane">
                        <a href="{{ route('events.index') }}" wire:navigate>Événements</a>
                        <span aria-hidden="true">/</span>
                        <span>{{ $event->title }}</span>
                    </nav>
                    <h1 class="event-detail__title">{{ $event->title }}</h1>
                    <div class="event-detail__meta">
                        <span><i class="fal fa-calendar"></i>{{ $event->start_date->translatedFormat('d F Y') }}</span>
                        <span><i class="fal fa-clock"></i>{{ $event->start_date->format('H\hi') }}</span>
                        @if ($event->format_label)
                            <span><i class="fal fa-map-marker-alt"></i>{{ $event->format_label }}</span>
                        @endif
                        @if ($event->category)
                            <span><i class="fal fa-tag"></i>{{ $event->category->name }}</span>
                        @endif
                    </div>
                    @if (! $cancelled && $event->temporal_status === 'upcoming')
                        <div class="event-countdown"
                             data-countdown
                             data-start="{{ $event->start_date->toIso8601String() }}"
                             aria-label="Compte à rebours avant l'événement"></div>
                    @endif
                    @if ($event->resume)
                        <p class="event-detail__resume">{{ $event->resume }}</p>
                    @endif
                </div>
            </div>
        </header>

        <div class="container event-detail__container">
            <div class="event-detail__content">
                @if ($event->description)
                    <section class="event-detail__section" aria-labelledby="sec-description">
                        <h2 id="sec-description">Présentation</h2>
                        <div class="event-detail__prose">{!! $event->description !!}</div>
                    </section>
                @endif

                @if ($event->objectifs)
                    <section class="event-detail__section" aria-labelledby="sec-objectifs">
                        <h2 id="sec-objectifs">Objectifs</h2>
                        <div class="event-detail__prose">{!! nl2br(e($event->objectifs)) !!}</div>
                    </section>
                @endif

                @if ($event->programme)
                    <section class="event-detail__section" aria-labelledby="sec-programme">
                        <h2 id="sec-programme">Programme</h2>
                        <div class="event-detail__prose">{!! $event->programme !!}</div>
                    </section>
                @endif

                {{-- Intervenants — section masquée si aucune ligne (FR-010) --}}
                @if ($event->speakers->isNotEmpty())
                    @include('events.partials.speakers', ['event' => $event])
                @endif

                @if ($event->public_cible)
                    <section class="event-detail__section" aria-labelledby="sec-public">
                        <h2 id="sec-public">Public cible</h2>
                        <div class="event-detail__prose">{!! nl2br(e($event->public_cible)) !!}</div>
                    </section>
                @endif

                {{-- Médias post-événement — uniquement si présents (événement clos) --}}
                @if ($event->medias->isNotEmpty())
                    @include('events.partials.galerie', ['event' => $event])
                @endif

                @if ($event->compte_rendu)
                    <section class="event-detail__section" aria-labelledby="sec-compte-rendu">
                        <h2 id="sec-compte-rendu">Compte rendu</h2>
                        <div class="event-detail__prose">{!! $event->compte_rendu !!}</div>
                    </section>
                @endif

                {{-- Événements similaires --}}
                @if ($similarEvents->isNotEmpty())
                    <section class="event-similar" aria-labelledby="sec-similar">
                        <h2 id="sec-similar">Événements similaires</h2>
                        <div class="row g-4">
                            @foreach ($similarEvents as $similar)
                                <div class="col-12 col-sm-6 col-lg-4">
                                    @include('components.event-card', ['event' => $similar])
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            {{-- Colonne inscription / informations pratiques + CTA dynamique --}}
            <aside class="event-detail__aside" id="inscription">
                <h2>Informations pratiques</h2>
                <ul class="event-detail__infos">
                    <li><i class="fal fa-calendar"></i><span>{{ $event->start_date->translatedFormat('d F Y · H\hi') }}</span></li>
                    <li><i class="fal fa-flag-checkered"></i><span>Fin : {{ $event->end_date->translatedFormat('d F Y · H\hi') }}</span></li>
                    <li><i class="fal fa-map-marker-alt"></i><span>{{ $event->format_label ?: 'Lieu à préciser' }}</span></li>
                    @if ($event->pays)
                        <li><i class="fal fa-globe-africa"></i><span>{{ $event->pays->name }}</span></li>
                    @endif
                    @if ($event->max_participants)
                        <li><i class="fal fa-users"></i><span>{{ $event->current_participants }} / {{ $event->max_participants }} inscrits</span></li>
                    @endif
                    @if ($event->registration_deadline)
                        <li><i class="fal fa-hourglass-half"></i><span>Clôture des inscriptions : {{ $event->registration_deadline->translatedFormat('d/m/Y') }}</span></li>
                    @endif
                    <li><i class="fal fa-ticket-alt"></i><span>{{ $event->price > 0 ? number_format((float) $event->price, 2, ',', ' ').' €' : 'Gratuit' }}</span></li>
                </ul>

                @if ($cancelled)
                    <p class="event-cta-note"><i class="fal fa-ban"></i> Cet événement a été annulé.</p>
                @elseif ($cta === 'register_external')
                    <a class="event-cta-btn" href="{{ $event->registration_url }}" target="_blank" rel="noopener">
                        S'inscrire <i class="fal fa-external-link"></i>
                    </a>
                @elseif ($cta === 'register_internal')
                    @if ($alreadyRegistered)
                        <p class="event-cta-note"><i class="fal fa-check-circle"></i> Vous êtes déjà inscrit à cet événement.</p>
                    @elseif (auth()->check())
                        <button type="button" class="event-cta-btn" data-event-register data-url="{{ route('events.register', $event) }}">
                            S'inscrire
                        </button>
                        <div class="event-cta-feedback" data-event-register-feedback role="status" aria-live="polite"></div>
                    @else
                        <a class="event-cta-btn" href="{{ route('login') }}">Se connecter pour s'inscrire</a>
                    @endif
                @elseif ($cta === 'view_replay')
                    <a class="event-cta-btn" href="#medias">Voir replay / photos / documents</a>
                @else
                    @if ($event->temporal_status === 'past')
                        <p class="event-cta-note">Cet événement est terminé.</p>
                    @elseif ($event->registration_mode === 'internal' && $event->registration_deadline && $event->registration_deadline->isPast())
                        <p class="event-cta-note">Les inscriptions sont closes.</p>
                    @elseif ($event->max_participants && $event->current_participants >= $event->max_participants)
                        <p class="event-cta-note">Cet événement est complet.</p>
                    @else
                        <p class="event-cta-note">Inscriptions non disponibles pour le moment.</p>
                    @endif
                @endif
            </aside>
        </div>
    </article>
@endsection
