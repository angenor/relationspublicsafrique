@extends('layouts.front')

@section('title', 'Événements')

@section('meta')
    <meta name="description" content="Découvrez les événements organisés par Relations Publiques Afrique : conférences, ateliers, webinaires et rencontres professionnelles à travers le continent.">
@endsection

@section('css')
    @vite(['resources/css/events.css', 'resources/js/events.js'])
@endsection

@section('content')
    <section class="events-page">
        <div class="container">
            <header class="events-page__head">
                <h1 class="events-page__title">Nos événements</h1>
                <p class="events-page__sub">
                    Conférences, ateliers, webinaires et rencontres : explorez les événements
                    portés par Relations Publiques Afrique, à venir comme passés.
                </p>
                <div class="events-page__comtour">
                    <a href="{{ route('lome.tour.register') }}" class="event-card__cta">
                        S'inscrire à Lomé COM' TOUR <i class="fal fa-arrow-right"></i>
                    </a>
                </div>
            </header>

            <livewire:events.grille-events />
        </div>
    </section>
@endsection
