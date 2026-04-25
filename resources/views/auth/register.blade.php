@extends('layouts.default')

@section('content')
    <div class="breadcumb-wrapper events-breadcrumb" data-bg-src="{{asset('front/assets/img/breadcumb/breadcumb-bg.png')}}">
        <div class="container z-index-common">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Inscription</h1>
                <p class="breadcumb-text">Rejoignez notre communauté</p>
                <div class="breadcumb-menu-wrap">
                    <ul class="breadcumb-menu">
                        <li><a href="{{url('/')}}">Accueil</a></li>
                        <li>Inscription</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <section class="space-top space-extra-bottom">
        <div class="container">
            <div class="row gx-80">
                <div class="col-lg-6 col-xl-6 mb-30 mb-lg-0">
                    <h2 class="h1 mt-n2">Rejoignez-nous</h2>
                    <p class="contact-info">
                        <i class="fas fa-user-plus"></i>
                        Créez votre compte pour accéder à tous nos services et événements.
                    </p>
                    <p class="contact-info">
                        <i class="fas fa-calendar-alt"></i>
                        Participez à nos événements exclusifs et formations.
                    </p>
                    <p class="contact-info">
                        <i class="fas fa-users"></i>
                        Rejoignez une communauté active de professionnels.
                    </p>
                    <div class="mega-hover rounded-20 mt-4 mt-lg-5 mb-4">
                        <img src="{{asset('front/assets/img/about/contact-1.jpg')}}" alt="inscription" class="w-100">
                    </div>
                </div>
                <div class="col-lg-6 col-xl-6">
                    <inscription></inscription>
                </div>
            </div>
        </div>
    </section>
@endsection
