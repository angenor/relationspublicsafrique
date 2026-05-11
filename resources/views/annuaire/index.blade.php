@extends('layouts.default')
@section('title', 'Annuaire')

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
