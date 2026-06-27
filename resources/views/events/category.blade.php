@extends('layouts.default')

@section('title', 'Événements — '.$category->name)

@section('css')
    @vite(['resources/css/events.css', 'resources/js/events.js'])
@endsection

@section('content')
    <section class="events-page">
        <div class="container">
            <header class="events-page__head">
                <nav class="event-detail__crumbs" aria-label="Fil d'Ariane">
                    <a href="{{ route('events.index') }}" wire:navigate>Événements</a>
                    <span aria-hidden="true">/</span>
                    <span>{{ $category->name }}</span>
                </nav>
                <h1 class="events-page__title">{{ $category->name }}</h1>
                <p class="events-page__sub">Les événements publiés de la catégorie « {{ $category->name }} ».</p>
            </header>

            @if ($events->isEmpty())
                <div class="alert alert-info">Aucun événement publié dans cette catégorie pour le moment.</div>
            @else
                <div class="row g-4">
                    @foreach ($events as $event)
                        <div class="col-12 col-sm-6 col-lg-4">
                            @include('components.event-card', ['event' => $event])
                        </div>
                    @endforeach
                </div>
                <nav class="mt-4" aria-label="Pagination des événements">
                    {{ $events->onEachSide(1)->links() }}
                </nav>
            @endif
        </div>
    </section>
@endsection
