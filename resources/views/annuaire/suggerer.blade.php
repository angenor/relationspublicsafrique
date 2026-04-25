@extends('layouts.default')

@section('content')
    <!--==============================
    Breadcumb
============================== -->
    <div class="breadcumb-wrapper events-breadcrumb" data-bg-src="{{asset('front/assets/img/breadcumb/breadcumb-bg.png')}}">
        <div class="container z-index-common">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Suggérer une adhésion</h1>
                <p class="breadcumb-text">Proposez une organisation pour rejoindre notre annuaire</p>
                <div class="breadcumb-menu-wrap">
                    <ul class="breadcumb-menu">
                        <li><a href="{{url('/')}}">Accueil</a></li>
                        <li><a href="{{route('annuaire.consulter')}}">Annuaire</a></li>
                        <li>Suggérer</li>
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
                        <span class="sec-subtitle">Proposez une organisation</span>
                        <h2 class="sec-title">Aidez-nous à enrichir notre annuaire professionnel</h2>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4 mb-30 mb-xl-0">
                    <p class="fs-md mt-n1">Connaissez-vous une organisation qui pourrait enrichir notre annuaire ? Proposez-la nous ! Nous examinerons votre suggestion et contacterons l'organisation concernée.</p>
                    <div class="media-style1">
                        <div class="media-img"><img src="{{asset('front/assets/img/about/author-1-1.png')}}" alt="About Author"></div>
                        <div class="media-body">
                            <span class="media-label">Réseau Collaboratif</span>
                            <p class="media-info">Plus de 200 suggestions traitées</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="list-style1 vs-list">
                        <ul>
                            <li>Organisations du secteur des relations publiques</li>
                            <li>Agences de communication</li>
                            <li>Entreprises de l'événementiel</li>
                            <li>Médias et journalistes</li>
                            <li>Institutions et organisations</li>
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
                        <h5 class="media-title">200+ Suggestions</h5>
                        <p class="media-text">Plus de 200 organisations suggérées par notre communauté.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="media-style8">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/about-icon-2.svg')}}" alt=""></div>
                        <h5 class="media-title">85% Acceptées</h5>
                        <p class="media-text">85% des suggestions sont acceptées et intégrées à l'annuaire.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="media-style8">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/about-icon-3.svg')}}" alt=""></div>
                        <h5 class="media-title">Qualité</h5>
                        <p class="media-text">Nous privilégions la qualité et la pertinence des organisations.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="media-style8">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/about-icon-4.svg')}}" alt=""></div>
                        <h5 class="media-title">Diversité</h5>
                        <p class="media-text">Un annuaire diversifié représentant tous les secteurs d'activité.</p>
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
                <span class="sec-subtitle">PROCESSUS DE SUGGESTION</span>
                <h2 class="sec-title h1">Comment suggérer une organisation</h2>
            </div>
            <div class="row">
                <div class="col-sm-6 col-xl-3">
                    <div class="media-style9">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-1.svg')}}" alt="icon"></div>
                        <h5 class="media-title">1. Suggestion</h5>
                        <p>Remplissez le formulaire avec les informations de l'organisation que vous souhaitez suggérer.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="media-style9">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-2.svg')}}" alt="icon"></div>
                        <h5 class="media-title">2. Vérification</h5>
                        <p>Notre équipe examine la suggestion et vérifie la pertinence de l'organisation.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="media-style9">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-3.svg')}}" alt="icon"></div>
                        <h5 class="media-title">3. Contact</h5>
                        <p>Nous contactons l'organisation suggérée pour lui proposer d'intégrer l'annuaire.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="media-style9">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-4.svg')}}" alt="icon"></div>
                        <h5 class="media-title">4. Intégration</h5>
                        <p>Si l'organisation accepte, elle est intégrée à l'annuaire et vous êtes informé.</p>
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
                        <span class="sec-subtitle">SUGGESTION EN LIGNE</span>
                        <h2 class="sec-title h1">Formulaire de suggestion</h2>
                    </div>
                    <div class="row gx-80 gy-xl-4 mb-4 mb-xl-0">
                        <div class="col-md-6 col-xl-6 wow fadeInUp" data-wow-delay="0.2s">
                            <div class="media-style4">
                                <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-1.svg')}}" alt=""></div>
                                <h5 class="media-title">Informations détaillées</h5>
                                <p>Fournissez le maximum d'informations sur l'organisation suggérée.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-6 wow fadeInUp" data-wow-delay="0.3s">
                            <div class="media-style4">
                                <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-2.svg')}}" alt=""></div>
                                <h5 class="media-title">Suivi personnalisé</h5>
                                <p>Nous vous tenons informé du statut de votre suggestion.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="position-relative">
                        <form action="#" class="form-style2">
                            <div class="form-inner">
                                <h3 class="form-title h5">Suggérez une organisation pour enrichir notre <span class="text-theme">annuaire professionnel</span>.</h3>
                                <div class="row">
                                    <div class="col-md-6 col-xl-12">
                                        <div class="form-group">
                                            <input type="text" name="votre_nom" id="votre_nom" placeholder="Votre nom" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-12">
                                        <div class="form-group">
                                            <input type="email" name="votre_email" id="votre_email" placeholder="Votre email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-12">
                                        <div class="form-group">
                                            <input type="text" name="nom_organisation" id="nom_organisation" placeholder="Nom de l'organisation suggérée" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-12">
                                        <div class="form-group">
                                            <input type="text" name="contact_organisation" id="contact_organisation" placeholder="Contact de l'organisation">
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
                                                <option>Institution</option>
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
                                            <textarea name="description" id="description" placeholder="Description de l'organisation et pourquoi vous la suggérez" rows="4" required></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <textarea name="justification" id="justification" placeholder="Justification de votre suggestion" rows="3" required></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button type="submit" class="vs-btn">Soumettre la suggestion</button>
                                        <a class="form-link" href="{{route('annuaire.rejoindre')}}">Rejoindre directement</a>
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
                    <h2 class="sec-title text-white mb-3">Réseau collaboratif</h2>
                    <p class="fs-md text-white">Participez à l'enrichissement de notre annuaire en suggérant des organisations pertinentes</p>
                    <div class="row gx-60 mb-4 pb-xl-3 text-start justify-content-center justify-content-lg-start">
                        <div class="col-auto col-lg-12 col-xl-auto">
                            <div class="list-style4 vs-list">
                                <ul class="list-unstyled m-0">
                                    <li>Suggestions qualifiées</li>
                                    <li>Suivi personnalisé</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-auto col-lg-12 col-xl-auto">
                            <div class="list-style4 vs-list">
                                <ul class="list-unstyled m-0">
                                    <li>Réseau étendu</li>
                                    <li>Communauté active</li>
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
    Suggérer une adhésion
@endsection
