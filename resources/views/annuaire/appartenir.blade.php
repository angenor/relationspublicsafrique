@extends('layouts.default')
@section('title', $pageTitle)

@section('content')
    <!--==============================
    Breadcumb
============================== -->
    <div class="breadcumb-wrapper" data-bg-src="{{asset('front/assets/img/breadcumb/breadcumb-bg.png')}}">
        <div class="container z-index-common">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">{{$pageTitle}}</h1>
                <p class="breadcumb-text">{{$pagesousTitle}}</p>
                <div class="breadcumb-menu-wrap">
                    <ul class="breadcumb-menu">
                        <li><a href="{{url('/')}}">Accueil</a></li>
                        <li><a href="{{route('annuaire.consulter')}}">Annuaire</a></li>
                        <li>{{$pageTitle}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!--==============================
    About Area
==============================-->
    <section class="space-bottom">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="title-area text-center">
                        <div class="sec-icon">
                            <div class="vs-circle"></div>
                        </div>
                        <span class="sec-subtitle">{{$pageTitle}}</span>
                        <h2 class="sec-title h1">{{$pageTitle}}</h2>
                        <p class="sec-text">Découvrez les professionnels et agences de communication d'Afrique</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--==============================
    Filter Section
==============================-->
    <section class="space-bottom">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="filter-form bg-white p-4 rounded shadow-sm">
                        <form method="GET" action="{{ route('appartenir') }}" class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Rechercher par nom</label>
                                <input type="text"
                                       class="form-control"
                                       id="name"
                                       name="name"
                                       value="{{ $name }}"
                                       placeholder="Nom ou prénom">
                            </div>
                            <div class="col-md-4">
                                <label for="pays" class="form-label">Pays</label>
                                <select class="form-select" id="pays" name="pays">
                                    <option value="">Sélectionnez un pays</option>
                                    @foreach($paysList as $paysItem)
                                        <option value="{{ $paysItem->id }}"
                                                {{ $pays == $paysItem->id ? 'selected' : '' }}>
                                            {{ $paysItem->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Rechercher
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--==============================
    Team Area
==============================-->
    <section class="space-bottom">
        <div class="container">
            <div class="row">
                @forelse($profils as $profil)
                    <div class="col-6 col-sm-6 col-lg-4 col-xxl-3">
                        <div class="team-style2 has-border">
                            <div class="team-content">
                                <h5 class="team-name h5 text-wrap truncate" style="font-size: medium">
                                    <a href="{{route('profil.show',[$profil->slug,$profil->id])}}" wire:navigate>
                                        {{$profil->fullname}}
                                    </a>
                                </h5>
                                <p class="team-degi">{{$profil->fonction ?? 'Spécialiste'}}</p>

                                <div class="team-img">
                                    <a href="{{route('profil.show',[$profil->slug,$profil->id])}}" wire:navigate>
                                        <img src="{{$profil->img}}"
                                             alt="{{$profil->fullname}}">
                                    </a>
                                </div>

                                <p class="team-experi">
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{$profil->pays->name ?? 'Non renseigné'}}
                                </p>

                                @if($profil->domaine)
                                    <p class="team-experi">
                                        <i class="fas fa-tag"></i>
                                        {{$profil->domaine}}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h3>Aucun membre trouvé</h3>
                                <p>Essayez de modifier vos critères de recherche</p>
                                <a href="{{ route('appartenir') }}" class="btn btn-primary">
                                    <i class="fas fa-refresh"></i> Voir tous les membres
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($profils->hasPages())
                <div class="row">
                    <div class="col-12">
                        <div class="pagination-wrapper text-center mt-4">
                            {{ $profils->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!--==============================
    CTA Section
==============================-->
    <section class="space-top space-extra-bottom bg-primary">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <div class="title-area text-white">
                        <h2 class="sec-title h1 text-white">Rejoignez notre communauté</h2>
                        <p class="sec-text text-white">
                            Faites partie de la plus grande communauté de communicants d'Afrique.
                            Partagez votre expertise et développez votre réseau professionnel.
                        </p>
                        <div class="btn-group">
                            <a href="{{ route('annuaire.rejoindre') }}" class="btn btn-light">
                                <i class="fas fa-user-plus"></i> Rejoindre l'annuaire
                            </a>
                            <a href="{{ route('annuaire.suggerer') }}" class="btn btn-outline-light">
                                <i class="fas fa-handshake"></i> Suggérer une adhésion
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
<style>
/* Styles pour la page Appartenir */
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

.empty-state {
    padding: 3rem;
    color: #6c757d;
}

.empty-icon {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    font-weight: 700;
    margin-bottom: 1rem;
    color: #495057;
}

.empty-state p {
    margin-bottom: 1.5rem;
    font-size: 1.1rem;
}

.pagination-wrapper {
    margin-top: 2rem;
}

.btn-group .btn {
    margin: 0 0.5rem;
}

@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }

    .btn-group .btn {
        margin: 0.5rem 0;
        width: 100%;
    }

    .filter-form .row {
        margin: 0;
    }

    .filter-form .col-md-2 {
        margin-top: 1rem;
    }
}
</style>
@endsection
