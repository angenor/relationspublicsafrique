@extends('layouts.default')

@php
    $parent = $breadcrumbParent ?? ['label' => $pageTitle, 'url' => route('appartenir')];
    $masquerEmail = (bool) ($profil->masquer_email ?? false);
    $masquerTel = (bool) ($profil->masquer_tel ?? false);
    $emailVisible = ! $masquerEmail && ! empty($profil->email);
    $telVisible = ! $masquerTel && ! empty($profil->tel);
    $bioPrincipale = ! empty($profil->bio_longue) ? $profil->bio_longue : ($profil->bio ?? '');
@endphp

@section('content')

    <div class="breadcumb-wrapper " data-bg-src="{{ asset('front/assets/img/breadcumb/breadcumb-bg.png') }}">
        <div class="container z-index-common">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">{{ $pageTitle }}</h1>
                <p class="breadcumb-text">{{ $pagesousTitle }}</p>
                <div class="breadcumb-menu-wrap">
                    <ul class="breadcumb-menu">
                        <li><a href="{{ url('/') }}">Accueil</a></li>
                        <li><a href="{{ $parent['url'] }}" wire:navigate>{{ $parent['label'] }}</a></li>
                        <li>{{ $profil->fullname }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <section class="space-top space-extra-bottom">
        <div class="container">
            <div class="row justify-content-center align-items-center gx-80 mb-lg-4 pb-3">
                <div class="col-lg-5 col-xl-auto order-lg-2 mb-4 mb-lg-0 pb-2 pb-lg-0">
                    <div class="img-box1 style3">
                        <div class="vs-circle">
                            <div class="mega-hover">
                                <img src="{{ $profil->photo_url ?? $profil->img }}"
                                     alt="{{ $profil->fullname }}"
                                     id="profilimg-show"
                                     style="height: 300px ; width: 300px">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl order-lg-1 mb-4 mb-md-0">
                    <div class="team-details">
                        <h2 class="team-name h2">{{ $profil->fullname }}</h2>

                        @if (! empty($profil->fonction))
                            <p class="team-degi">{{ $profil->fonction }}</p>
                        @elseif (! empty($profil->title))
                            <p class="team-degi">{{ $profil->title }}</p>
                        @endif

                        @if (! empty($profil->organisation))
                            <p class="team-experi">{{ $profil->organisation }}</p>
                        @endif

                        @if (! empty($profil->ville) || ! empty(optional($profil->pays)->name))
                            <p class="team-experi">
                                {{ $profil->ville }}@if (! empty($profil->ville) && ! empty(optional($profil->pays)->name)), @endif{{ optional($profil->pays)->name }}
                            </p>
                        @endif

                        @if (! empty($profil->adresse))
                            <p class="team-experi">{{ $profil->adresse }}</p>
                        @endif

                        @if ($emailVisible)
                            <p class="team-experi">
                                <i class="fal fa-envelope me-2"></i>
                                <a href="mailto:{{ $profil->email }}">{{ $profil->email }}</a>
                            </p>
                        @endif

                        @if ($telVisible)
                            <p class="team-experi">
                                <i class="fal fa-phone me-2"></i>
                                <a href="tel:{{ $profil->tel }}">{{ $profil->tel }}</a>
                            </p>
                        @endif

                        <div class="social-style2">
                            @if (! empty($profil->facebook))
                                <a target="_blank" rel="noopener noreferrer" href="{{ $profil->facebook }}">
                                    <i class="fab fa-facebook-f m-lg-2"></i>
                                </a>
                            @endif
                            @if (! empty($profil->twitter))
                                <a target="_blank" rel="noopener noreferrer" href="{{ $profil->twitter }}">
                                    <i class="fab fa-twitter m-lg-2"></i>
                                </a>
                            @endif
                            @if (! empty($profil->linkding))
                                <a target="_blank" rel="noopener noreferrer" href="{{ $profil->linkding }}">
                                    <i class="fab fa-linkedin-in m-lg-2"></i>
                                </a>
                            @endif
                            @if (! empty($profil->site))
                                <a target="_blank" rel="noopener noreferrer" href="{{ $profil->site }}">
                                    <i class="fal fa-globe m-lg-2"></i>
                                </a>
                            @endif
                            @if (! empty($profil->youtube))
                                <a target="_blank" rel="noopener noreferrer" href="{{ $profil->youtube }}">
                                    <i class="fab fa-youtube m-lg-2"></i>
                                </a>
                            @endif

                            {{-- Liens externes additionnels (FR-001 annuaire) --}}
                            @foreach ($profil->liensExternes ?? [] as $lien)
                                @php
                                    $iconesLien = [
                                        'linkedin' => 'fab fa-linkedin-in',
                                        'site_web' => 'fal fa-globe',
                                        'portfolio' => 'fal fa-briefcase',
                                        'facebook' => 'fab fa-facebook-f',
                                        'twitter' => 'fab fa-twitter',
                                        'youtube' => 'fab fa-youtube',
                                        'autre' => 'fal fa-link',
                                    ];
                                    $iconeLien = $iconesLien[$lien->type] ?? 'fal fa-link';
                                @endphp
                                <a target="_blank" rel="noopener noreferrer" href="{{ $lien->url }}" title="{{ $lien->libelle ?? ucfirst($lien->type) }}">
                                    <i class="{{ $iconeLien }} m-lg-2"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            @if (! empty($bioPrincipale))
                <h2 class="border-title2 mb-4">A Propos</h2>
                <div class="profil-bio">
                    {!! nl2br(e($bioPrincipale)) !!}
                </div>
            @endif

            @if ($profil->domainesExpertise && $profil->domainesExpertise->count() > 0)
                <h2 class="border-title2 mb-4 mt-5">Domaines d'expertise</h2>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    @foreach ($profil->domainesExpertise as $domaine)
                        <a href="{{ route('annuaire.index', ['domaines' => [$domaine->slug]]) }}"
                           class="badge rounded-pill px-3 py-2 text-decoration-none"
                           style="background: var(--vs-theme-color, #0d6efd); color:#fff;">
                            {{ $domaine->libelle }}
                        </a>
                    @endforeach
                </div>
            @endif

            @if ($profil->tags && $profil->tags->count() > 0)
                <h2 class="border-title2 mb-4 mt-4">Tags</h2>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    @foreach ($profil->tags as $tag)
                        <span class="badge rounded-pill bg-light text-dark px-3 py-2">#{{ $tag->libelle }}</span>
                    @endforeach
                </div>
            @endif

        </div>
    </section>

@endsection
