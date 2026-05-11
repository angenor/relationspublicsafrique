@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h1>Retrait confirmé</h1>
        <p>
            Votre profil <strong>{{ trim(($profil->prenom ?? '').' '.($profil->nom ?? '')) }}</strong>
            a été retiré de l'annuaire public.
        </p>
        <p>Un email de confirmation vous a été envoyé.</p>
    </div>
@endsection
