@extends('layouts.default')
@section('title', 'Annuaire')

@section('css')
    <style>
        /* Cards de l'annuaire — style aligné avec relationspublicsafrique.org/annuaire */
        .filter-form {
            border: 1px solid #e9ecef;
            border-radius: 10px;
        }

        .team-style2 {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .team-style2:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .team-img {
            width: 100%;
            height: 200px;
            overflow: hidden;
            border-radius: 10px;
        }

        .team-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }

        .team-name a {
            color: inherit;
            text-decoration: none;
        }

        .team-name a:hover {
            color: var(--vs-theme-color, #0d6efd);
        }

        .team-name.truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
    </style>
@endsection

@section('content')
    <div class="breadcumb-wrapper events-breadcrumb" data-bg-src="{{ asset('front/assets/img/breadcumb/breadcumb-bg.png') }}">
        <div class="container z-index-common">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Annuaire</h1>
                <p class="breadcumb-text">Consulter les profils des professionnels et de la communauté</p>
                <div class="breadcumb-menu-wrap">
                    <ul class="breadcumb-menu">
                        <li><a href="{{ url('/') }}">Accueil</a></li>
                        <li>Annuaire</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <section class="space py-5">
        <div class="container">
            @livewire('annuaire.profil-grid')
        </div>
    </section>
@endsection
