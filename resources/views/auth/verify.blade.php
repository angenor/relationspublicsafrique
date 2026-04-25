@extends('layouts.default')

@section('content')
    <div class="breadcumb-wrapper" data-bg-src="{{asset('front/front/assets/img/breadcumb/breadcumb-bg.png')}}">
        <div class="container z-index-common">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Vérification Email</h1>
                <div class="breadcumb-menu-wrap">
                    <ul class="breadcumb-menu">
                        <li><a href="{{url('/')}}">Accueil</a></li>
                        <li>Vérification Email</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <section class="space-top space-extra-bottom">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-5">
                            <div class="text-center">
                                <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle mb-4" style="width: 80px; height: 80px; background-color: rgba(var(--theme-color-rgb), 0.1);">
                                    <i class="fas fa-envelope fa-2x text-theme"></i>
                                </div>

                                <h2 class="h3 text-theme mb-4">Vérifiez votre email</h2>

                                <div class="text-muted mb-4">
                                    <p class="mb-3">
                                        Merci pour votre inscription ! Avant de commencer, pourriez-vous vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer ?
                                    </p>

                                    <p class="small">
                                        Si vous n'avez pas reçu l'e-mail, nous vous en enverrons volontiers un autre.
                                    </p>
                                </div>

                                @if (session('resent'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle me-2"></i>
                                        Un nouveau lien de vérification a été envoyé à votre adresse e-mail.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <div class="d-grid gap-3">
                                    <form class="d-inline" method="POST" action="{{ route('verification.send') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-lg w-100 text-white bg-theme">
                                            <i class="fas fa-paper-plane me-2"></i>
                                            Renvoyer l'e-mail de vérification
                                        </button>
                                    </form>

                                    <div class="d-flex align-items-center justify-content-center gap-3 text-muted small">
                                        <a href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                           class="text-decoration-none">
                                            <i class="fas fa-sign-out-alt me-1"></i>
                                            Se déconnecter
                                        </a>

                                        <span class="text-muted">|</span>

                                        <a href="{{ route('login') }}" class="text-decoration-none text-theme">
                                            <i class="fas fa-sign-in-alt me-1"></i>
                                            Retour à la connexion
                                        </a>
                                    </div>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
