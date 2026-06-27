@extends('layouts.front')

@section('title', 'Projets')

@section('meta')
    <meta name="description" content="Découvrez les projets portés par Relations Publiques Afrique : communication, plaidoyer, formation et mobilisation citoyenne à travers le continent.">
@endsection

@section('css')
    @vite(['resources/css/projets.css', 'resources/js/projets.js'])
@endsection

@section('content')
    <section class="projets-page">
        <div class="container">
            <header class="projets-page__head">
                <h1 class="projets-page__title">Nos projets</h1>
                <p class="projets-page__sub">
                    Explorez les initiatives portées par Relations Publiques Afrique à travers le continent.
                </p>
            </header>

            <livewire:projets.grille-projets />
        </div>
    </section>
@endsection
