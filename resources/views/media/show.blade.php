@extends('layouts.media')

@php
    $metaTitle = $media->meta_title ?: $media->titre;
    $metaDescription = $media->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($media->chapo ?: $media->content), 160);
    $ogImage = $media->ogImageUrl;
    $typeLabel = \App\Models\Media::$types[$media->type] ?? $media->type;
    $mailto = 'mailto:?subject=' . rawurlencode($media->titre) . '&body=' . rawurlencode(url($media->link));
@endphp

@section('title', $metaTitle)
@section('meta_description', $metaDescription)

@section('meta')
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $metaTitle }} | Relations Publics Afrique">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:site_name" content="Relations Publics Afrique">
    <meta property="article:published_time" content="{{ optional($media->published_at)->toIso8601String() }}">
    @if ($media->auteurPrincipal)
        <meta property="article:author" content="{{ $media->auteurPrincipal->name }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
@endsection

@section('content')
    <div class="media-container media-article">
        <article>
            <header class="media-article__head">
                <a href="{{ route('media.index', ['type' => $media->type]) }}" class="media-article__type media-card__badge--{{ $media->type }}">{{ $typeLabel }}</a>
                <h1 class="media-article__title">{{ $media->titre }}</h1>
                @if ($media->chapo)
                    <p class="media-article__chapo">{{ $media->chapo }}</p>
                @endif
                <div class="media-article__meta">
                    @if ($media->auteurPrincipal)
                        <span class="media-article__author"><i class="fas fa-user-pen"></i> {{ $media->auteurPrincipal->name }}</span>
                    @endif
                    @if ($media->published_at)
                        <span><i class="fas fa-calendar"></i> {{ $media->published_at->translatedFormat('d F Y') }}</span>
                    @endif
                    @if ($media->readingTimeHuman)
                        <span><i class="fas fa-book-open"></i> {{ $media->readingTimeHuman }}</span>
                    @endif
                    @if ($media->durationHuman)
                        <span><i class="fas fa-clock"></i> {{ $media->durationHuman }}</span>
                    @endif
                    <span><i class="fas fa-eye"></i> {{ number_format((int) $media->view, 0, ',', ' ') }}</span>
                </div>
            </header>

            {{-- Bloc média : vidéo embarquée, audio (podcast) ou couverture --}}
            @if (in_array($media->media_kind, ['youtube', 'vimeo'], true) && $media->embedHtml)
                <div class="media-article__player">
                    {!! $media->embedHtml !!}
                </div>
            @elseif ($media->media_kind === 'audio' && $media->audioUrl)
                <div class="media-article__player">
                    <audio class="media-audio-player" controls preload="metadata" playsinline>
                        <source src="{{ $media->audioUrl }}" type="audio/mpeg">
                        @if ($media->subtitlesUrl)
                            <track kind="captions" srclang="fr" label="Français" src="{{ $media->subtitlesUrl }}" default>
                        @endif
                        Votre navigateur ne prend pas en charge la lecture audio.
                    </audio>
                    @if ($media->audio_downloadable)
                        <a href="{{ $media->audioUrl }}" download class="btn btn-outline-theme btn-sm mt-2">
                            <i class="fas fa-download"></i> Télécharger l'audio
                        </a>
                    @endif
                </div>
            @elseif ($media->cover_image)
                <figure class="media-article__cover">
                    <img src="{{ $media->img }}" alt="{{ $media->titre }}" loading="lazy">
                </figure>
            @endif

            <div class="media-article__body">
                {!! $media->content !!}
            </div>

            {{-- Tags --}}
            @if ($media->tags->isNotEmpty())
                <div class="media-article__tags">
                    @foreach ($media->tags as $tag)
                        <a href="{{ route('media.index', ['q' => $tag->libelle]) }}" class="media-tag">#{{ $tag->libelle }}</a>
                    @endforeach
                </div>
            @endif

            {{-- Auteur(s) / intervenants --}}
            @if ($media->auteurPrincipal || $media->auteurs->isNotEmpty())
                <section class="media-article__authors" aria-label="Auteurs et intervenants">
                    @if ($media->auteurPrincipal)
                        <div class="media-author">
                            <span class="media-author__role">Auteur</span>
                            <span class="media-author__name">{{ $media->auteurPrincipal->name }}</span>
                        </div>
                    @endif
                    @foreach ($media->auteurs as $co)
                        <div class="media-author">
                            <span class="media-author__role">{{ \App\Models\MediaAuthor::$roles[$co->pivot->role] ?? $co->pivot->role }}</span>
                            <span class="media-author__name">{{ $co->name }}</span>
                        </div>
                    @endforeach
                </section>
            @endif

            {{-- Partage --}}
            <div class="media-share" aria-label="Partager">
                <span class="media-share__label">Partager :</span>
                <a href="{{ $btnShare['linkedin'] ?? '#' }}" target="_blank" rel="noopener" aria-label="Partager sur LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                <a href="{{ $btnShare['twitter'] ?? '#' }}" target="_blank" rel="noopener" aria-label="Partager sur X"><i class="fab fa-x-twitter"></i></a>
                <a href="{{ $btnShare['facebook'] ?? '#' }}" target="_blank" rel="noopener" aria-label="Partager sur Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="{{ $btnShare['whatsapp'] ?? '#' }}" target="_blank" rel="noopener" aria-label="Partager sur WhatsApp"><i class="fab fa-whatsapp"></i></a>
                <a href="{{ $mailto }}" aria-label="Partager par email"><i class="fas fa-envelope"></i></a>
            </div>
        </article>

        {{-- Navigation précédent / suivant --}}
        @if ($precedent || $suivant)
            <nav class="media-prevnext" aria-label="Navigation entre contenus">
                <div class="media-prevnext__item">
                    @if ($precedent)
                        <a href="{{ $precedent->link }}" wire:navigate>
                            <span class="media-prevnext__dir"><i class="fas fa-arrow-left"></i> Précédent</span>
                            <span class="media-prevnext__title">{{ \Illuminate\Support\Str::limit($precedent->titre, 60) }}</span>
                        </a>
                    @endif
                </div>
                <div class="media-prevnext__item media-prevnext__item--next">
                    @if ($suivant)
                        <a href="{{ $suivant->link }}" wire:navigate>
                            <span class="media-prevnext__dir">Suivant <i class="fas fa-arrow-right"></i></span>
                            <span class="media-prevnext__title">{{ \Illuminate\Support\Str::limit($suivant->titre, 60) }}</span>
                        </a>
                    @endif
                </div>
            </nav>
        @endif

        {{-- Playlist de la série --}}
        @if ($serieEpisodes->isNotEmpty())
            <section class="media-section" aria-labelledby="serie-title">
                <h2 id="serie-title" class="media-section__title">
                    Série : <a href="{{ $media->serie->link }}">{{ $media->serie->titre }}</a>
                </h2>
                <ul class="media-playlist">
                    @foreach ($serieEpisodes as $episode)
                        <li class="media-playlist__item {{ $episode->id === $media->id ? 'is-current' : '' }}">
                            <span class="media-playlist__num">
                                @if ($episode->saison)S{{ $episode->saison }}·@endif{{ $episode->episode ? 'É'.$episode->episode : '—' }}
                            </span>
                            <span>
                                <a href="{{ $episode->link }}" class="media-playlist__title" wire:navigate>{{ $episode->titre }}</a>
                                @if ($episode->durationHuman)
                                    <span class="media-playlist__meta"> · {{ $episode->durationHuman }}</span>
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- Contenus similaires --}}
        @if ($similaires->isNotEmpty())
            <section class="media-section" aria-labelledby="similaires-title">
                <h2 id="similaires-title" class="media-section__title">À lire aussi</h2>
                <div class="row g-4">
                    @foreach ($similaires as $media)
                        <div class="col-12 col-sm-6 col-xl-3">
                            @include('components.media-card', ['media' => $media])
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @include('media.partials.newsletter')

        {{-- Commentaires (formulaire + liste : US6) --}}
        <section id="commentaires" class="media-comments" aria-labelledby="commentaires-title">
            <h2 id="commentaires-title" class="media-section__title">Commentaires</h2>
            @include('media.partials.commentaires', ['media' => $media])
        </section>
    </div>
@endsection
