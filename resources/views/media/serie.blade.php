@extends('layouts.media')

@section('title', $serie->titre)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($serie->description), 160))

@section('content')
    <div class="media-container">
        <header class="media-serie__head">
            @if ($serie->cover_image)
                <img src="{{ $serie->img }}" alt="{{ $serie->titre }}" class="media-serie__cover">
            @endif
            <div>
                @if ($serie->type)
                    <span class="media-article__type media-card__badge--{{ $serie->type === 'video' ? 'video' : 'podcast' }}">
                        {{ \App\Models\MediaSerie::$types[$serie->type] ?? $serie->type }}
                    </span>
                @endif
                <h1 class="media-page-head__title">{{ $serie->titre }}</h1>
                @if ($serie->description)
                    <p class="media-page-head__sub">{{ $serie->description }}</p>
                @endif
                <p class="text-muted small mb-0">{{ $episodes->count() }} épisode(s)</p>
            </div>
        </header>

        @if ($episodes->isEmpty())
            <p class="text-muted">Aucun épisode publié pour le moment.</p>
        @else
            <ul class="media-playlist">
                @foreach ($episodes as $episode)
                    <li class="media-playlist__item">
                        <span class="media-playlist__num">
                            @if ($episode->saison)S{{ $episode->saison }}·@endif{{ $episode->episode ? 'É'.$episode->episode : '—' }}
                        </span>
                        <span>
                            <a href="{{ $episode->link }}" class="media-playlist__title" wire:navigate>{{ $episode->titre }}</a>
                            <span class="media-playlist__meta">
                                @if ($episode->auteurPrincipal){{ $episode->auteurPrincipal->name }}@endif
                                @if ($episode->durationHuman) · {{ $episode->durationHuman }}@endif
                            </span>
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
