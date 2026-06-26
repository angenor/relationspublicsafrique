@extends('layouts.media')

@section('title', 'Média')
@section('meta_description', 'Articles, interviews, podcasts, vidéos et reportages de Relations Publics Afrique.')

@section('content')
    <div class="media-container">

        <header class="media-page-head">
            <h1 class="media-page-head__title">Média</h1>
            <p class="media-page-head__sub">La newsroom des relations publiques africaines.</p>
        </header>

        {{-- À la une --}}
        @if ($aLaUne->isNotEmpty())
            <section class="media-section" aria-labelledby="aluneune-title">
                <h2 id="aluneune-title" class="media-section__title">À la une</h2>
                <div class="media-une">
                    @foreach ($aLaUne as $index => $media)
                        <div class="media-une__item {{ $index === 0 ? 'media-une__item--lead' : '' }}">
                            @include('components.media-card', ['media' => $media])
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <div class="media-cols">
            <div class="media-cols__main">
                {{-- Dernières publications --}}
                <section class="media-section" aria-labelledby="dernieres-title">
                    <h2 id="dernieres-title" class="media-section__title">Dernières publications</h2>
                    @if ($dernieres->isEmpty())
                        <p class="text-muted">Aucun contenu publié pour le moment.</p>
                    @else
                        <div class="row g-4">
                            @foreach ($dernieres as $media)
                                <div class="col-12 col-sm-6 col-xl-4">
                                    @include('components.media-card', ['media' => $media])
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="mt-4">
                        <a href="{{ route('media.index') }}" class="btn btn-theme">Explorer tous les contenus</a>
                    </div>
                </section>
            </div>

            <aside class="media-cols__aside">
                {{-- Tendances --}}
                @if ($tendances->isNotEmpty())
                    <section class="media-widget" aria-labelledby="tendances-title">
                        <h2 id="tendances-title" class="media-widget__title">Tendances</h2>
                        <ul class="media-list">
                            @foreach ($tendances as $media)
                                @include('components.media-ligne', ['media' => $media])
                            @endforeach
                        </ul>
                    </section>
                @endif

                {{-- Sélection éditoriale --}}
                @if ($selection->isNotEmpty())
                    <section class="media-widget" aria-labelledby="selection-title">
                        <h2 id="selection-title" class="media-widget__title">Sélection de la rédaction</h2>
                        <ul class="media-list">
                            @foreach ($selection as $media)
                                @include('components.media-ligne', ['media' => $media])
                            @endforeach
                        </ul>
                    </section>
                @endif
            </aside>
        </div>

        @include('media.partials.newsletter')
    </div>
@endsection
