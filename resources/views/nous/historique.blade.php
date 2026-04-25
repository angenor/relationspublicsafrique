@extends('layouts.default')

@section('content')

    <!--==============================
    Breadcumb
    ============================== -->
    <div class="breadcumb-wrapper events-breadcrumb" data-bg-src="{{asset('front/assets/img/breadcumb/breadcumb-bg.png')}}">
        <div class="container z-index-common">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Notre Historique</h1>
                <p class="breadcumb-text">Découvrez notre parcours et notre histoire</p>
                <div class="breadcumb-menu-wrap">
                    <ul class="breadcumb-menu">
                        <li><a href="{{url('/')}}">Accueil</a></li>
                        <li><a href="#">Nous</a></li>
                        <li>Historique</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!--==============================
    Historique Content Area
    ==============================-->
    <section class="vs-blog-wrapper space-top space-extra-bottom">
        <div class="container">
            <div class="row gx-40">
                <div class="col-lg-8">
                    <!-- Image à la une -->
                    @if($historique && $historique->image)
                        <div class="nous-featured-image mb-5">
                            <img src="{{ asset('storage/' . $historique->image) }}" alt="{{ $historique->name }}" class="w-100">
                        </div>
                    @endif

                    <div class="vs-blog blog-single">
                        <div class="blog-content">
                            @if($historique)
                                <div class="blog-meta mb-4 events-meta">
                                    <a href="#"><i class="fal fa-user"></i>{{ $historique->user?->name ?? 'Administration' }}</a>
                                    <a href="#"><i class="fal fa-calendar"></i>{{ $historique->created_at->format('d/m/Y') }}</a>
                                    <a href="#"><i class="fal fa-tag"></i>Historique</a>
                                </div>

                                <h1 class="blog-title h2 mb-4 events-title">{{ $historique->name }}</h1>

                                <div class="prose mb-5">
                                    {!! $historique->content !!}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fal fa-history fa-3x text-muted mb-3"></i>
                                    <h3 class="h4 text-muted">Historique en cours de rédaction</h3>
                                    <p class="text-muted">Notre historique sera bientôt disponible.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <aside class="sidebar-area">
                        <!-- Navigation Nous -->
                        <div class="widget events-widget">
                            <h3 class="widget_title">À propos de nous</h3>
                            <ul>
                                <li>
                                    <a href="{{ route('nous.mission') }}">
                                        <i class="fal fa-bullseye me-2"></i>Mission
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('nous.vision') }}">
                                        <i class="fal fa-eye me-2"></i>Vision
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('nous.historique') }}" class="active">
                                        <i class="fal fa-history me-2"></i>Historique
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Contact Widget -->
                        <div class="widget events-widget">
                            <h3 class="widget_title">Contactez-nous</h3>
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <p class="mb-3">Vous avez des questions sur notre historique ?</p>
                                    <a href="{{ route('contact') }}" class="vs-btn" style="background: var(--events-theme-color); color: white; border: none;">
                                        <i class="fal fa-envelope me-2"></i>Nous contacter
                                    </a>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('title')
    Notre Historique - Relations Publiques Afrique
@endsection
