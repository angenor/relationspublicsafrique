@extends('layouts.front')

@section('title', 'Recherche d\'événements')

@section('css')
    @vite(['resources/css/events.css', 'resources/js/events.js'])
@endsection

@section('content')
    <section class="events-page">
        <div class="container">
            <header class="events-page__head">
                <h1 class="events-page__title">Recherche d'événements</h1>
                <p class="events-page__sub">
                    @if ($query)
                        Résultats pour « {{ $query }} ».
                    @else
                        Saisissez un terme pour rechercher un événement.
                    @endif
                </p>
                <form class="d-flex gap-2 mt-2" action="{{ route('events.search') }}" method="GET" role="search">
                    <input type="search" name="q" class="form-control" value="{{ $query }}"
                           placeholder="Titre, lieu, mot-clé…" aria-label="Rechercher un événement">
                    <button type="submit" class="event-card__cta">Rechercher</button>
                </form>
            </header>

            @if ($events->isEmpty())
                <div class="alert alert-info">Aucun événement ne correspond à votre recherche.</div>
            @else
                <div class="row g-4">
                    @foreach ($events as $event)
                        <div class="col-12 col-sm-6 col-lg-4">
                            @include('components.event-card', ['event' => $event])
                        </div>
                    @endforeach
                </div>
                <nav class="mt-4" aria-label="Pagination des résultats">
                    {{ $events->onEachSide(1)->appends(['q' => $query])->links() }}
                </nav>
            @endif
        </div>
    </section>
@endsection
