@extends('layouts.default')

@section('title', 'Calendrier des événements')

@section('css')
    @vite(['resources/css/events.css', 'resources/js/events.js'])
@endsection

@section('content')
    <section class="events-page">
        <div class="container">
            <header class="events-page__head">
                <h1 class="events-page__title">Calendrier des événements</h1>
                <p class="events-page__sub">Tous les événements publiés, classés par date.</p>
            </header>

            @if ($events->isEmpty())
                <div class="alert alert-info">Aucun événement publié pour le moment.</div>
            @else
                @php $parMois = $events->groupBy(fn ($event) => $event->start_date->translatedFormat('F Y')); @endphp
                @foreach ($parMois as $mois => $mesEvents)
                    <section class="events-group">
                        <h2 class="events-group__title">{{ ucfirst($mois) }} <span class="events-group__count">{{ $mesEvents->count() }}</span></h2>
                        <div class="row g-4">
                            @foreach ($mesEvents as $event)
                                <div class="col-12 col-sm-6 col-lg-4">
                                    @include('components.event-card', ['event' => $event])
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            @endif
        </div>
    </section>
@endsection
