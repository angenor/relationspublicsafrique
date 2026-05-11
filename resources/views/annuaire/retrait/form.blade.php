@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h1>Gérer votre profil sur l'annuaire RP Afrique</h1>
        <p>Bonjour {{ trim(($profil->prenom ?? '').' '.($profil->nom ?? '')) }},</p>
        <p>
            Vous figurez actuellement sur l'annuaire public de Relations Publiques Afrique.
            Conformément au RGPD, vous pouvez :
        </p>

        <ul>
            <li><strong>Confirmer</strong> votre présence (aucune action requise — il vous suffit de fermer cette page).</li>
            <li><strong>Demander une modification</strong> en écrivant à <a href="mailto:contact@relationspublicsafrique.com">contact@relationspublicsafrique.com</a>.</li>
            <li><strong>Demander le retrait</strong> immédiat de votre profil de l'annuaire public.</li>
        </ul>

        <form method="POST" action="{{ url()->signedRoute('annuaire.retrait.confirmer', ['token' => $token]) }}">
            @csrf
            <button type="submit" class="btn btn-danger"
                    onclick="return confirm('Confirmez-vous le retrait définitif de votre profil de l\'annuaire ?')">
                Confirmer le retrait
            </button>
        </form>
    </div>
@endsection
