@extends('layouts.media')

@section('title', 'Désabonnement')

@section('content')
    <div class="media-container" style="max-width: 640px;">
        <div class="text-center py-5">
            <i class="fas fa-circle-info" style="font-size: 3rem; color: var(--media-muted, #6b7280);"></i>
            <h1 class="media-page-head__title mt-3">Désabonnement effectué</h1>
            <p class="media-page-head__sub">
                Vous ne recevrez plus nos alertes. Vous pouvez vous réabonner à tout moment.
            </p>
            <a href="{{ route('media.home') }}" class="btn btn-theme mt-3" wire:navigate>Retour à la newsroom</a>
        </div>
    </div>
@endsection
