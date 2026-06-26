@extends('layouts.media')

@section('title', 'Explorer')
@section('meta_description', 'Recherchez et filtrez tous les contenus de la newsroom : articles, interviews, podcasts, vidéos, reportages.')

@section('content')
    <div class="media-container">
        <header class="media-page-head">
            <h1 class="media-page-head__title">Explorer</h1>
            <p class="media-page-head__sub">Recherchez, filtrez et triez tous les contenus.</p>
        </header>

        <livewire:media.grille-medias />
    </div>
@endsection
