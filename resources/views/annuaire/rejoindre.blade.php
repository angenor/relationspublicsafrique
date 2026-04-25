@extends('layouts.default')

@section('content')
    <!--==============================
    Breadcumb
============================== -->
    <div class="breadcumb-wrapper events-breadcrumb" data-bg-src="{{asset('front/assets/img/breadcumb/breadcumb-bg.png')}}">
        <div class="container z-index-common">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Rejoindre l'annuaire</h1>
                <p class="breadcumb-text">Intégrez votre organisation à notre réseau professionnel</p>
                <div class="breadcumb-menu-wrap">
                    <ul class="breadcumb-menu">
                        <li><a href="{{url('/')}}">Accueil</a></li>
                        <li><a href="{{route('annuaire.consulter')}}">Annuaire</a></li>
                        <li>Rejoindre</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!--==============================
    About Area
==============================-->
    <section class="space-top">
        <div class="container">
            <div class="row">
                <div class="col-xl-9">
                    <div class="title-area mb-3 mb-xl-5">
                        <span class="sec-subtitle">Rejoignez notre réseau</span>
                        <h2 class="sec-title">Intégrez votre organisation à l'annuaire professionnel</h2>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4 mb-30 mb-xl-0">
                    <p class="fs-md mt-n1">Rejoignez notre annuaire professionnel et bénéficiez d'une visibilité accrue, d'un réseau étendu et d'opportunités de collaboration avec d'autres professionnels du secteur des relations publiques.</p>
                    <div class="media-style1">
                        <div class="media-img"><img src="{{asset('front/assets/img/about/author-1-1.png')}}" alt="About Author"></div>
                        <div class="media-body">
                            <span class="media-label">Réseau Professionnel</span>
                            <p class="media-info">Plus de 500 organisations</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="list-style1 vs-list">
                        <ul>
                            <li>Visibilité accrue de votre organisation</li>
                            <li>Accès au réseau professionnel étendu</li>
                            <li>Opportunités de collaboration</li>
                            <li>Événements et formations exclusives</li>
                            <li>Support et accompagnement personnalisé</li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-4 mt-n5 pt-5 pt-xl-0">
                    <div class="img-box3">
                        <div class="img-1 mega-hover"><img class="w-100" src="{{asset('front/assets/img/about/about-s-1.png')}}" alt="About Img"></div>
                        <div class="shape-dotted jump"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--==============================
    Features Area
==============================-->
    <section class="space-top space-extra-bottom">
        <div class="container">
            <div class="row vs-carousel" data-slide-show="4" data-md-slide-show="3" data-sm-slide-show="2" data-xs-slide-show="2">
                <div class="col-sm-6 col-lg-3">
                    <div class="media-style8">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/about-icon-1.svg')}}" alt=""></div>
                        <h5 class="media-title">500+ Organisations</h5>
                        <p class="media-text">Plus de 500 organisations déjà inscrites dans notre annuaire.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="media-style8">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/about-icon-2.svg')}}" alt=""></div>
                        <h5 class="media-title">15+ Pays</h5>
                        <p class="media-text">Présence internationale dans plus de 15 pays africains.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="media-style8">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/about-icon-3.svg')}}" alt=""></div>
                        <h5 class="media-title">10+ Ans</h5>
                        <p class="media-text">Plus de 10 ans d'expérience dans le secteur des relations publiques.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="media-style8">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/about-icon-4.svg')}}" alt=""></div>
                        <h5 class="media-title">1000+ Membres</h5>
                        <p class="media-text">Plus de 1000 professionnels actifs dans notre réseau.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--==============================
    Process Area
==============================-->
    <section class="space-top space-extra-bottom bg-smoke">
        <div class="container">
            <div class="title-area text-center">
                <div class="sec-icon">
                    <div class="vs-circle"></div>
                </div>
                <span class="sec-subtitle">PROCESSUS D'ADHÉSION</span>
                <h2 class="sec-title h1">Comment rejoindre l'annuaire</h2>
            </div>
            <div class="row">
                <div class="col-sm-6 col-xl-3">
                    <div class="media-style9">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-1.svg')}}" alt="icon"></div>
                        <h5 class="media-title">1. Candidature</h5>
                        <p>Remplissez le formulaire de candidature en ligne avec les informations de votre organisation.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="media-style9">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-2.svg')}}" alt="icon"></div>
                        <h5 class="media-title">2. Vérification</h5>
                        <p>Notre équipe examine votre candidature et vérifie les informations fournies.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="media-style9">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-3.svg')}}" alt="icon"></div>
                        <h5 class="media-title">3. Validation</h5>
                        <p>Après validation, votre organisation est intégrée à l'annuaire professionnel.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="media-style9">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-4.svg')}}" alt="icon"></div>
                        <h5 class="media-title">4. Activation</h5>
                        <p>Votre profil est activé et vous accédez à tous les avantages du réseau.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--==============================
    Form Area
==============================-->
    <section class="space-top space-extra-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-7 text-center text-xl-start">
                    <div class="title-area">
                        <span class="sec-subtitle">CANDIDATURE EN LIGNE</span>
                        <h2 class="sec-title h1">Formulaire de candidature</h2>
                    </div>
                    <div class="row gx-80 gy-xl-4 mb-4 mb-xl-0">
                        <div class="col-md-6 col-xl-6 wow fadeInUp" data-wow-delay="0.2s">
                            <div class="media-style4">
                                <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-1.svg')}}" alt=""></div>
                                <h5 class="media-title">Informations complètes</h5>
                                <p>Renseignez tous les détails de votre organisation pour une candidature complète.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-6 wow fadeInUp" data-wow-delay="0.3s">
                            <div class="media-style4">
                                <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-2.svg')}}" alt=""></div>
                                <h5 class="media-title">Traitement rapide</h5>
                                <p>Notre équipe traite votre candidature dans les plus brefs délais.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="position-relative">
                        <form action="#" class="form-style2">
                            <div class="form-inner">
                                <h3 class="form-title h5">Rejoignez plus de <span class="text-theme">500 organisations</span> déjà inscrites dans notre annuaire.</h3>
                                <div class="row">
                                    <div class="col-md-6 col-xl-12">
                                        <div class="form-group">
                                            <input type="text" name="nom_organisation" id="nom_organisation" placeholder="Nom de l'organisation" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-12">
                                        <div class="form-group">
                                            <input type="text" name="contact_personne" id="contact_personne" placeholder="Personne de contact" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-12">
                                        <div class="form-group">
                                            <input type="email" name="email" id="email" placeholder="Email de contact" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-12">
                                        <div class="form-group">
                                            <input type="tel" name="telephone" id="telephone" placeholder="Téléphone" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-12">
                                        <div class="form-group">
                                            <select name="secteur" id="secteur" required>
                                                <option selected disabled hidden>Secteur d'activité</option>
                                                <option>Relations Publiques</option>
                                                <option>Communication</option>
                                                <option>Marketing</option>
                                                <option>Événementiel</option>
                                                <option>Médias</option>
                                                <option>Autre</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-12">
                                        <div class="form-group">
                                            <input type="text" name="ville" id="ville" placeholder="Ville" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <textarea name="description" id="description" placeholder="Description de l'organisation" rows="4" required></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button type="submit" class="vs-btn">Soumettre la candidature</button>
                                        <a class="form-link" href="{{route('annuaire.suggerer')}}">Suggérer une adhésion</a>
                                    </div>
                                </div>
                            </div>
                            <div class="vs-circle color2"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--==============================
    Call To Action
==============================-->
    <section class="" data-bg-src="assets/img/bg/divider-bg-1-1.jpg">
        <div class="container">
            <div class="row align-items-center text-center text-lg-start">
                <div class="col-lg-5 col-xl-6 space-extra">
                    <h2 class="sec-title text-white mb-3">Expertise reconnue</h2>
                    <p class="fs-md text-white">Rejoignez un réseau de professionnels experts dans le domaine des relations publiques</p>
                    <div class="row gx-60 mb-4 pb-xl-3 text-start justify-content-center justify-content-lg-start">
                        <div class="col-auto col-lg-12 col-xl-auto">
                            <div class="list-style4 vs-list">
                                <ul class="list-unstyled m-0">
                                    <li>Organisations certifiées</li>
                                    <li>Réseau professionnel étendu</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-auto col-lg-12 col-xl-auto">
                            <div class="list-style4 vs-list">
                                <ul class="list-unstyled m-0">
                                    <li>Formations exclusives</li>
                                    <li>Événements de networking</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <a href="{{route('annuaire.consulter')}}" class="vs-btn style5"><i class="far fa-angle-right"></i>Consulter l'annuaire</a>
                </div>
                <div class="col-lg-7 col-xl-6 align-self-end">
                    <div class="img-box2">
                        <div class="vs-circle"></div>
                        <img class="img-1" src="{{asset('front/assets/img/about/about-1-2.png')}}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('title')
    Rejoindre l'annuaire
@endsection
