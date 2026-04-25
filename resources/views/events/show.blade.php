@extends('layouts.default')

@section('content')

    <!--==============================
    Breadcumb
============================== -->
    <div class="breadcumb-wrapper events-breadcrumb" data-bg-src="{{asset('front/assets/img/breadcumb/breadcumb-bg.png')}}">
        <div class="container z-index-common">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">{{ $event->title }}</h1>
                <p class="breadcumb-text">{{ $event->resume ?: Str::limit($event->description, 100) }}</p>
                <div class="breadcumb-menu-wrap">
                    <ul class="breadcumb-menu">
                        <li><a href="{{url('/')}}">Accueil</a></li>
                        <li><a href="{{ route('events.index') }}">Événements</a></li>
                        @if($event->category)
                            <li><a href="{{ route('events.index', ['category' => $event->category->id]) }}">{{ $event->category->name }}</a></li>
                        @endif
                        <li>{{ Str::limit($event->title, 30) }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!--==============================
    Event Details Area
==============================-->
    <section class="vs-blog-wrapper space-top space-extra-bottom">
        <div class="container">
            <div class="row gx-40">
                <div class="col-lg-8">
                    <!-- Event Image -->
                    <div class="vs-blog blog-single mb-5 events-card">
                        <div class="blog-img">
                            @if($event->image)
                                <img src="{{ $event->img }}" alt="{{ $event->title }}" class="w-100">
                            @else
                                <img src="{{ asset('front/assets/img/blog/blog-1-1.jpg') }}" alt="{{ $event->title }}" class="w-100">
                            @endif
                        </div>
                    </div>

                    <!-- Event Content -->
                    <div class="vs-blog blog-single">
                        <div class="blog-content">
                            <!-- Event Meta -->
                            <div class="blog-meta mb-4 events-meta">
                                <a href="#"><i class="fal fa-user"></i>{{ $event->user?->name ?? 'Anonyme' }}</a>
                                <a href="#"><i class="fal fa-calendar"></i>{{ $event->start_date->format('d/m/Y H:i') }}</a>
                                @if($event->location)
                                    <a href="#"><i class="fal fa-map-marker-alt"></i>{{ $event->location }}</a>
                                @endif
                                @if($event->category)
                                    <a href="{{ route('events.index', ['category' => $event->category->id]) }}">
                                        <i class="fal fa-tag"></i>{{ $event->category->name }}
                                    </a>
                                @endif
                            </div>

                            <!-- Event Title -->
                            <h1 class="blog-title h2 mb-4 events-title">{{ $event->title }}</h1>

                            <!-- Event Description -->
                            <div class="prose mb-5">
                                {!! $event->description !!}
                            </div>

                            <!-- Event Details -->
                            <div class="row mb-5">
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title events-title mb-3">
                                                <i class="fal fa-calendar-alt me-2"></i>Dates
                                            </h5>
                                            <div class="mb-2">
                                                <strong>Début :</strong> {{ $event->start_date->format('d/m/Y H:i') }}
                                            </div>
                                            <div class="mb-2">
                                                <strong>Fin :</strong> {{ $event->end_date->format('d/m/Y H:i') }}
                                            </div>
                                            @if($event->registration_deadline)
                                                <div class="events-price">
                                                    <strong>Inscriptions jusqu'au :</strong> {{ $event->registration_deadline->format('d/m/Y H:i') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title events-title mb-3">
                                                <i class="fal fa-info-circle me-2"></i>Informations
                                            </h5>
                                            @if($event->location)
                                                <div class="mb-2">
                                                    <strong>Lieu :</strong> {{ $event->location }}
                                                </div>
                                            @endif
                                            <div class="mb-2">
                                                <strong>Prix :</strong>
                                                @if($event->price > 0)
                                                    <span class="events-price">{{ number_format($event->price, 2) }} €</span>
                                                @else
                                                    <span class="events-price">Gratuit</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <!-- Event Status -->
                            <div class="card border-0 shadow-sm mb-5">
                                <div class="card-body">
                                    <h5 class="card-title events-title mb-3">Statut de l'événement</h5>
                                    @if($event->is_upcoming)
                                        <div class="alert mb-0" style="background-color: rgba(113, 61, 11, 0.1); border-left: 4px solid var(--events-theme-color); color: var(--events-theme-color);">
                                            <i class="fal fa-clock me-2"></i>
                                            Cet événement aura lieu le {{ $event->start_date->format('d/m/Y') }}
                                        </div>
                                    @elseif($event->is_ongoing)
                                        <div class="alert mb-0" style="background-color: rgba(113, 61, 11, 0.1); border-left: 4px solid var(--events-theme-color); color: var(--events-theme-color);">
                                            <i class="fal fa-play-circle me-2"></i>
                                            Cet événement est en cours
                                        </div>
                                    @else
                                        <div class="alert mb-0" style="background-color: rgba(113, 61, 11, 0.1); border-left: 4px solid var(--events-theme-color); color: var(--events-theme-color);">
                                            <i class="fal fa-calendar-check me-2"></i>
                                            Cet événement s'est terminé le {{ $event->end_date->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Event Registration -->
                            <div class="card border-0 shadow-sm mb-5">
                                <div class="card-body">
                                    <h5 class="card-title events-title mb-3">
                                        <i class="fal fa-user-plus me-2"></i>Inscription
                                    </h5>
                                    @livewire('event-registration-modal', ['event' => $event])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <aside class="sidebar-area">
                        <!-- Event Quick Info -->
                        <div class="widget events-widget">
                            <h3 class="widget_title">Informations rapides</h3>
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fal fa-calendar me-3" style="color: var(--events-theme-color);"></i>
                                        <div>
                                            <small class="text-muted">Date</small>
                                            <div class="fw-bold">{{ $event->start_date->format('d/m/Y') }}</div>
                                        </div>
                                    </div>
                                    @if($event->location)
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fal fa-map-marker-alt me-3" style="color: var(--events-theme-color);"></i>
                                            <div>
                                                <small class="text-muted">Lieu</small>
                                                <div class="fw-bold">{{ $event->location }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fal fa-euro-sign me-3" style="color: var(--events-theme-color);"></i>
                                        <div>
                                            <small class="text-muted">Prix</small>
                                            <div class="fw-bold">
                                                @if($event->price > 0)
                                                    {{ number_format($event->price, 2) }} €
                                                @else
                                                    Gratuit
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Organizer Info -->
                        @if($event->user)
                            <div class="widget events-widget">
                                <h3 class="widget_title">Organisateur</h3>
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <img src="{{ $event->user->profile_photo_url ?? asset('front/assets/img/team/team-s-1-1.png') }}"
                                                 alt="{{ $event->user->name }}"
                                                 class="rounded-circle"
                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                        </div>
                                        <h5 class="mb-1">{{ $event->user->name }}</h5>
                                        <p class="text-muted mb-0">Organisateur de l'événement</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Similar Events -->
                        @if($similarEvents->count() > 0)
                            <div class="widget events-widget">
                                <h3 class="widget_title">Événements similaires</h3>
                                <div class="recent-post-wrap">
                                    @foreach($similarEvents->take(3) as $similarEvent)
                                        <div class="recent-post">
                                            <div class="media-img">
                                                <a href="{{ route('events.show', ['id' => $similarEvent->id, 'slug' => $similarEvent->slug]) }}">
                                                    @if($similarEvent->image)
                                                        <img src="{{ $similarEvent->img }}" alt="{{ $similarEvent->title }}">
                                                    @else
                                                        <img src="{{ asset('front/assets/img/blog/recent-post-1-1.jpg') }}" alt="{{ $similarEvent->title }}">
                                                    @endif
                                                </a>
                                            </div>
                                            <div class="media-body">
                                                <h4 class="post-title">
                                                    <a class="text-inherit" href="{{ route('events.show', ['id' => $similarEvent->id, 'slug' => $similarEvent->slug]) }}">
                                                        {{ Str::limit($similarEvent->title, 50) }}
                                                    </a>
                                                </h4>
                                                <div class="recent-post-meta">
                                                    <a href="#">{{ $similarEvent->start_date->format('d/m/Y') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Share Event -->
                        <div class="widget events-widget">
                            <h3 class="widget_title">Partager</h3>
                            <div class="d-flex gap-2">
                                <a href="#" class="btn btn-sm" style="background: var(--events-theme-color); color: white; border: none;">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="btn btn-sm" style="background: var(--events-theme-light); color: white; border: none;">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="btn btn-sm" style="background: var(--events-theme-color); color: white; border: none;">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="#" class="btn btn-sm" style="background: var(--events-theme-dark); color: white; border: none;">
                                    <i class="fal fa-envelope"></i>
                                </a>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('title')
    {{ $event->title }} - Relations Publiques Afrique
@endsection
