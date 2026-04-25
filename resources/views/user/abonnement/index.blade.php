@extends('layouts.default')

@section('content')
<div class="breadcumb-wrapper" data-bg-src="{{asset('front/assets/img/breadcumb/breadcumb-bg.png')}}">
    <div class="container z-index-common">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Mes Abonnements</h1>
            <div class="breadcumb-menu-wrap">
                <ul class="breadcumb-menu">
                    <li><a href="{{url('/')}}">Accueil</a></li>
                    <li><a href="{{ route('tableau-de-bord') }}">Tableau de bord</a></li>
                    <li>Mes Abonnements</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<section class="space-top space-extra-bottom">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Mes Abonnements</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-5">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Aucun abonnement actif</h4>
                            <p class="text-muted">Vous n'avez actuellement aucun abonnement actif.</p>
                            <a href="{{ route('events.index') }}" class="btn btn-primary">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Découvrir nos événements
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('title')
    Mes Abonnements - Relations Publiques Afrique
@endsection
