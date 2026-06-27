@extends('layouts.front')

@section('title', $projet->meta_titre ?: $projet->titre)

@section('meta')
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($projet->meta_description ?: $projet->resume ?: ''), 160) }}">
    <link rel="canonical" href="{{ route('projets.show', $projet->slug) }}">
@endsection

@section('css')
    @vite(['resources/css/projets.css', 'resources/js/projets.js'])
@endsection

@section('content')
    <article class="projet-detail">
        {{-- En-tête : visuel principal + identité --}}
        <header class="projet-detail__hero">
            <div class="projet-detail__hero-media">
                <img src="{{ $projet->visuel_principal_url }}" alt="{{ $projet->titre }}" loading="eager">
                <span class="projet-card__badge projet-card__badge--{{ $projet->statut }}">{{ $projet->statut_label }}</span>
            </div>
            <div class="container">
                <div class="projet-detail__hero-body">
                    <nav class="projet-detail__crumbs" aria-label="Fil d'Ariane">
                        <a href="{{ route('projets.index') }}" wire:navigate>Projets</a>
                        <span aria-hidden="true">/</span>
                        <span>{{ $projet->titre }}</span>
                    </nav>
                    <h1 class="projet-detail__title">{{ $projet->titre }}</h1>
                    <div class="projet-detail__meta">
                        @foreach ($projet->categories as $cat)
                            <span class="projet-card__tag">{{ $cat->name }}</span>
                        @endforeach
                        @if ($projet->zone_label)
                            <span class="projet-card__zone"><i class="fas fa-map-marker-alt"></i> {{ $projet->zone_label }}</span>
                        @endif
                    </div>
                    @if ($projet->resume)
                        <p class="projet-detail__resume">{{ $projet->resume }}</p>
                    @endif
                </div>
            </div>
        </header>

        <div class="container projet-detail__container">
            <div class="projet-detail__content">
                @if ($projet->contexte)
                    <section class="projet-detail__section" aria-labelledby="sec-contexte">
                        <h2 id="sec-contexte">Contexte &amp; problématique</h2>
                        <div class="projet-detail__prose">{!! nl2br(e($projet->contexte)) !!}</div>
                    </section>
                @endif

                @if ($projet->objectifs)
                    <section class="projet-detail__section" aria-labelledby="sec-objectifs">
                        <h2 id="sec-objectifs">Objectifs</h2>
                        <div class="projet-detail__prose">{!! nl2br(e($projet->objectifs)) !!}</div>
                    </section>
                @endif

                @if ($projet->description)
                    <section class="projet-detail__section" aria-labelledby="sec-description">
                        <h2 id="sec-description">Description détaillée</h2>
                        <div class="projet-detail__prose">{!! $projet->description !!}</div>
                    </section>
                @endif

                @if ($projet->activites)
                    <section class="projet-detail__section" aria-labelledby="sec-activites">
                        <h2 id="sec-activites">Activités réalisées</h2>
                        <div class="projet-detail__prose">{!! $projet->activites !!}</div>
                    </section>
                @endif

                {{-- Chiffres clés (FR-013) — masqués si vides --}}
                @if ($projet->resultats->isNotEmpty())
                    <section class="projet-detail__section" aria-labelledby="sec-resultats">
                        <h2 id="sec-resultats">Chiffres clés</h2>
                        <div class="projet-resultats">
                            @foreach ($projet->resultats as $resultat)
                                <div class="projet-resultat">
                                    @if ($resultat->icone)
                                        <i class="{{ $resultat->icone }}" aria-hidden="true"></i>
                                    @endif
                                    <span class="projet-resultat__valeur">{{ $resultat->valeur }}</span>
                                    @if ($resultat->unite)
                                        <span class="projet-resultat__unite">{{ $resultat->unite }}</span>
                                    @endif
                                    <span class="projet-resultat__libelle">{{ $resultat->libelle }}</span>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Partenaires (FR-022) — masqués si vides --}}
                @if ($projet->partenaires->isNotEmpty())
                    <section class="projet-detail__section" aria-labelledby="sec-partenaires">
                        <h2 id="sec-partenaires">Partenaires</h2>
                        <div class="projet-partenaires">
                            @foreach ($projet->partenaires as $partenaire)
                                @php $logo = '<img src="'.e($partenaire->logo_url).'" alt="'.e($partenaire->nom).'" loading="lazy">'; @endphp
                                @if ($partenaire->url)
                                    <a class="projet-partenaire" href="{{ $partenaire->url }}" target="_blank" rel="noopener" title="{{ $partenaire->nom }}">{!! $logo !!}</a>
                                @else
                                    <span class="projet-partenaire" title="{{ $partenaire->nom }}">{!! $logo !!}</span>
                                @endif
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Galerie photos / vidéos (FR-014) — masquée si vide, tolérante aux médias indisponibles --}}
                @if ($projet->medias->isNotEmpty())
                    <section class="projet-detail__section" aria-labelledby="sec-galerie">
                        <h2 id="sec-galerie">Galerie</h2>
                        <div class="projet-galerie">
                            @foreach ($projet->medias as $media)
                                @if ($media->type === 'video')
                                    @php $embed = $media->embed_html; @endphp
                                    @if ($embed !== '')
                                        <figure class="projet-galerie__item projet-galerie__item--video">
                                            {!! $embed !!}
                                            @if ($media->legende)<figcaption>{{ $media->legende }}</figcaption>@endif
                                        </figure>
                                    @elseif ($media->chemin_url)
                                        <figure class="projet-galerie__item projet-galerie__item--video">
                                            <video controls preload="metadata" src="{{ $media->chemin_url }}"></video>
                                            @if ($media->legende)<figcaption>{{ $media->legende }}</figcaption>@endif
                                        </figure>
                                    @endif
                                @elseif ($media->chemin_url)
                                    <figure class="projet-galerie__item projet-galerie__item--image">
                                        <a href="{{ $media->chemin_url }}"
                                           class="projet-galerie__link"
                                           data-projet-lightbox
                                           data-legende="{{ $media->legende }}">
                                            <img src="{{ $media->chemin_url }}" alt="{{ $media->legende ?: $projet->titre }}" loading="lazy">
                                        </a>
                                        @if ($media->legende)<figcaption>{{ $media->legende }}</figcaption>@endif
                                    </figure>
                                @endif
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Témoignages (FR-011/FR-012) — section entièrement masquée si aucune ligne --}}
                @if ($projet->temoignages->isNotEmpty())
                    <section class="projet-detail__section" aria-labelledby="sec-temoignages">
                        <h2 id="sec-temoignages">Témoignages</h2>
                        <div class="projet-temoignages">
                            @foreach ($projet->temoignages as $temoignage)
                                <blockquote class="projet-temoignage">
                                    <p class="projet-temoignage__contenu">« {{ $temoignage->contenu }} »</p>
                                    <footer class="projet-temoignage__auteur">
                                        @if ($temoignage->photo_url)
                                            <img src="{{ $temoignage->photo_url }}" alt="{{ $temoignage->auteur }}" loading="lazy">
                                        @endif
                                        <span>
                                            <strong>{{ $temoignage->auteur }}</strong>
                                            @if ($temoignage->fonction || $temoignage->organisation)
                                                <em>{{ collect([$temoignage->fonction, $temoignage->organisation])->filter()->implode(', ') }}</em>
                                            @endif
                                        </span>
                                    </footer>
                                </blockquote>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            {{-- Appel à l'action : Nous contacter (R10/FR-015) --}}
            <aside class="projet-detail__cta">
                <h2>Un projet, une collaboration&nbsp;?</h2>
                <p>Échangeons sur ce projet et sur la manière dont nous pouvons travailler ensemble.</p>
                <a class="vs-btn" href="{{ route('contact', ['projet' => $projet->slug]) }}" wire:navigate>Nous contacter</a>
            </aside>
        </div>
    </article>
@endsection
