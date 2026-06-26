@extends('layouts.media')

@section('title', 'Abonnement confirmé')

@section('content')
    <div class="media-container" style="max-width: 640px;">
        <div class="text-center py-5">
            <i class="fas fa-circle-check" style="font-size: 3rem; color: var(--success-color, #28a745);"></i>
            <h1 class="media-page-head__title mt-3">Abonnement confirmé</h1>
            <p class="media-page-head__sub">
                Merci ! Vous recevrez désormais les nouveautés de la newsroom RP Afrique.
            </p>
            <a href="{{ route('media.home') }}" class="btn btn-theme mt-3" wire:navigate>Découvrir les contenus</a>
        </div>
    </div>
@endsection
