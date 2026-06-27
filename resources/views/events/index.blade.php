@extends('layouts.default')

@section('title', 'Événements')

@section('meta')
    <meta name="description" content="Découvrez les événements organisés par Relations Publiques Afrique : conférences, ateliers, webinaires et rencontres professionnelles à travers le continent.">
@endsection

@section('css')
    @vite(['resources/css/events.css', 'resources/js/events.js'])
@endsection

@section('content')
    {{-- Hero (bandeau thème événements, cohérent avec /annuaire/consulter) --}}
    <div class="breadcumb-wrapper events-breadcrumb" data-bg-src="{{ asset('front/assets/img/breadcumb/breadcumb-bg.png') }}">
        <div class="container z-index-common">
            <div class="breadcumb-content">
                <span class="events-hero__eyebrow"><i class="fal fa-calendar-star"></i> Agenda RPA</span>
                <h1 class="breadcumb-title">Nos événements</h1>
                <p class="breadcumb-text">
                    Conférences, ateliers, webinaires et rencontres portés par Relations Publiques
                    Afrique — à venir comme passés.
                </p>

                <div class="events-hero__stats" role="list" aria-label="Répartition des événements">
                    <div class="events-hero__stat" role="listitem">
                        <span class="events-hero__num">{{ $upcomingCount }}</span>
                        <span class="events-hero__lbl">À venir</span>
                    </div>
                    <div class="events-hero__stat" role="listitem">
                        <span class="events-hero__num">{{ $ongoingCount }}</span>
                        <span class="events-hero__lbl">En cours</span>
                    </div>
                    <div class="events-hero__stat" role="listitem">
                        <span class="events-hero__num">{{ $pastCount }}</span>
                        <span class="events-hero__lbl">Passés</span>
                    </div>
                </div>

                <div class="events-hero__actions">
                    <a href="{{ route('lome.tour.register') }}" class="events-hero__btn">
                        <i class="fal fa-ticket-alt"></i> S'inscrire à Lomé COM' TOUR
                    </a>
                    <a href="#liste-evenements" class="events-hero__link">
                        Parcourir les événements <i class="fal fa-arrow-down"></i>
                    </a>
                </div>

                <div class="breadcumb-menu-wrap">
                    <ul class="breadcumb-menu">
                        <li><a href="{{ url('/') }}">Accueil</a></li>
                        <li>Événements</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <section class="events-page" id="liste-evenements">
        <div class="container">
            <livewire:events.grille-events />
        </div>
    </section>
@endsection
