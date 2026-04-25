@extends('layouts.default')

@section('content')
    <div class="breadcumb-wrapper events-breadcrumb" data-bg-src="{{asset('front/assets/img/breadcumb/breadcumb-bg.png')}}">
        <div class="container z-index-common">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Connexion</h1>
                <p class="breadcumb-text">Accédez à votre espace personnel</p>
                <div class="breadcumb-menu-wrap">
                    <ul class="breadcumb-menu">
                        <li><a href="{{url('/')}}">Accueil</a></li>
                        <li>Connexion</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <section class="space-top space-extra-bottom">
        <div class="container">
            <div class="row gx-80">
                <div class="col-lg-6 col-xl-6 mb-30 mb-lg-0">
                    <h2 class="h1 mt-n2">Connectez-vous</h2>
                    <p class="contact-info">
                        <i class="fas fa-user"></i>
                        Accédez à votre espace personnel pour gérer votre profil et vos activités.
                    </p>
                    <p class="contact-info">
                        <i class="fas fa-shield-alt"></i>
                        Vos données sont sécurisées et protégées.
                    </p>
                    <p class="contact-info">
                        <i class="fas fa-clock"></i>
                        Accès 24h/24, 7j/7 à votre compte.
                    </p>
                    <div class="mega-hover rounded-20 mt-4 mt-lg-5 mb-4">
                        <img src="{{asset('front/assets/img/about/contact-1.jpg')}}" alt="connexion" class="w-100">
                    </div>
                </div>
                <div class="col-lg-6 col-xl-6">
                    <connexion></connexion>
                </div>
            </div>
        </div>
    </section>
@endsection
