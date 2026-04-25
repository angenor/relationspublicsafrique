@extends('layouts.default')

@section('content')

    <!--==============================
    Breadcumb
============================== -->
    <div class="breadcumb-wrapper events-breadcrumb" data-bg-src="{{asset('front/assets/img/breadcumb/breadcumb-bg.png')}}">
        <div class="container z-index-common">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Événements</h1>
                <p class="breadcumb-text">Découvrez nos événements en relations publiques</p>
                <div class="breadcumb-menu-wrap">
                    <ul class="breadcumb-menu">
                        <li><a href="{{url('/')}}">Accueil</a></li>
                        <li>Événements</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!--==============================
    Events Area
==============================-->
    <section class="vs-blog-wrapper space-top space-extra-bottom">
        <div class="container">
            <div class="text-center mb-4">
                <a href="{{ route('lome.tour.register') }}" class="vs-btn style3" style="background: var(--events-theme-color); color: #fff; padding: 16px 28px; font-size: 18px; border-radius: 8px;">
                    S’inscrire à  COM’ TOUR
                </a>
            </div>
            <div class="row gx-40">
                <div class="col-lg-8">
                    <!-- Featured Events Section -->
                    @if($featuredEvents->count() > 0)
                        <div class="mb-5">
                            <h2 class="h3 mb-4 events-section-title">Événements à la une</h2>
                            @foreach($featuredEvents as $event)
                                <div class="vs-blog blog-single mb-4 events-card">
                                    <div class="blog-img position-relative">
                                        <a href="{{ route('events.show', ['id' => $event->id, 'slug' => $event->slug]) }}">
                                            @if($event->image)
                                                <img src="{{ $event->img }}" alt="{{ $event->title }}" class="w-100">
                                            @else
                                                <img src="{{ asset('front/assets/img/blog/blog-1-1.jpg') }}" alt="{{ $event->title }}" class="w-100">
                                            @endif
                                        </a>

                                        <a href="{{ route('events.show', ['id' => $event->id, 'slug' => $event->slug]) }}" class="blog-date events-date">
                                            <span class="day">{{ $event->start_date->format('d') }}</span>
                                            <span class="month">{{ $event->start_date->format('M') }}</span>
                                        </a>

                                        @if($event->is_featured)
                                            <div class="position-absolute top-0 end-0 m-3">
                                                <span class="badge events-featured px-3 py-2">À la une</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="blog-content p-4">
                                        <div class="blog-meta events-meta">
                                            <a href="#"><i class="fal fa-user"></i>{{ $event->user?->name ?? 'Anonyme' }}</a>
                                            <a href="#"><i class="fal fa-calendar"></i>{{ $event->start_date->format('d/m/Y') }}</a>
                                            @if($event->location)
                                                <a href="#"><i class="fal fa-map-marker-alt"></i>{{ $event->location }}</a>
                                            @endif
                                            @if($event->pays)
                                                <a href="#"><i class="fal fa-flag"></i>{{ $event->pays->name }}</a>
                                            @endif
                                        </div>
                                        <h2 class="blog-title events-title">
                                            <a href="{{ route('events.show', ['id' => $event->id, 'slug' => $event->slug]) }}">
                                                {{ $event->title }}
                                            </a>
                                        </h2>
                                        <p>{{ $event->resume ?: Str::limit(strip_tags($event->description), 200) }}</p>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                @if($event->price > 0)
                                                    <span class="events-price"><i class="fal fa-euro-sign me-1"></i>{{ number_format($event->price, 2) }} €</span>
                                                @else
                                                    <span class="badge bg-success"><i class="fal fa-check me-1"></i>Gratuit</span>
                                                @endif
                                            </div>
                                            <div>
                                                @if($event->can_register && $event->registration_deadline && $event->registration_deadline->isFuture())
                                                    <span class="badge events-registration">Inscriptions ouvertes</span>
                                                @elseif($event->can_register && (!$event->registration_deadline || $event->registration_deadline->isFuture()))
                                                    <span class="badge events-registration">Inscriptions ouvertes</span>
                                                @elseif($event->registration_deadline && $event->registration_deadline->isPast())
                                                    <span class="badge bg-danger">Inscriptions fermées</span>
                                                @elseif($event->max_participants && $event->current_participants >= $event->max_participants)
                                                    <span class="badge bg-danger">Complet</span>
                                                @endif
                                            </div>
                                        </div>

                                        <a href="{{ route('events.show', ['id' => $event->id, 'slug' => $event->slug]) }}" class="vs-btn style3">
                                            <i class="far fa-angle-right"></i>Voir détails
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- All Events Section -->
                    <h2 class="h3 mb-4 events-section-title">Tous les événements</h2>
                    @forelse($events as $event)
                        <div class="vs-blog blog-single events-card mb-4">
                            <div class="blog-img position-relative">
                                <a href="{{ route('events.show', ['id' => $event->id, 'slug' => $event->slug]) }}">
                                    @if($event->image)
                                        <img src="{{ $event->img }}" alt="{{ $event->title }}" class="w-100">
                                    @else
                                        <img src="{{ asset('front/assets/img/blog/blog-1-1.jpg') }}" alt="{{ $event->title }}" class="w-100">
                                    @endif
                                </a>

                                <a href="{{ route('events.show', ['id' => $event->id, 'slug' => $event->slug]) }}" class="blog-date events-date">
                                    <span class="day">{{ $event->start_date->format('d') }}</span>
                                    <span class="month">{{ $event->start_date->format('M') }}</span>
                                </a>

                                @if($event->is_featured)
                                    <div class="position-absolute top-0 end-0 m-3">
                                        <span class="badge events-featured px-3 py-2">À la une</span>
                                    </div>
                                @endif
                            </div>
                            <div class="blog-content p-4">
                                <div class="blog-meta events-meta">
                                    <a href="#"><i class="fal fa-user"></i>{{ $event->user?->name ?? 'Anonyme' }}</a>
                                    <a href="#"><i class="fal fa-calendar"></i>{{ $event->start_date->format('d/m/Y') }}</a>
                                    @if($event->location)
                                        <a href="#"><i class="fal fa-map-marker-alt"></i>{{ $event->location }}</a>
                                    @endif
                                    @if($event->pays)
                                        <a href="#"><i class="fal fa-flag"></i>{{ $event->pays->name }}</a>
                                    @endif
                                </div>
                                <h2 class="blog-title events-title">
                                    <a href="{{ route('events.show', ['id' => $event->id, 'slug' => $event->slug]) }}">
                                        {{ $event->title }}
                                    </a>
                                </h2>
                                <p>{{ $event->resume ?: Str::limit(strip_tags($event->description), 200) }}</p>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        @if($event->price > 0)
                                            <span class="events-price"><i class="fal fa-euro-sign me-1"></i>{{ number_format($event->price, 2) }} €</span>
                                        @else
                                            <span class="badge bg-success"><i class="fal fa-check me-1"></i>Gratuit</span>
                                        @endif
                                    </div>
                                    <div>
                                        @if($event->can_register && $event->registration_deadline && $event->registration_deadline->isFuture())
                                            <span class="badge events-registration">Inscriptions ouvertes</span>
                                        @elseif($event->can_register && (!$event->registration_deadline || $event->registration_deadline->isFuture()))
                                            <span class="badge events-registration">Inscriptions ouvertes</span>
                                        @elseif($event->registration_deadline && $event->registration_deadline->isPast())
                                            <span class="badge bg-danger">Inscriptions fermées</span>
                                        @elseif($event->max_participants && $event->current_participants >= $event->max_participants)
                                            <span class="badge bg-danger">Complet</span>
                                        @endif
                                    </div>
                                </div>

                                <a href="{{ route('events.show', ['id' => $event->id, 'slug' => $event->slug]) }}" class="vs-btn style3">
                                    <i class="far fa-angle-right"></i>Voir détails
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fal fa-calendar-times fa-3x text-muted mb-3"></i>

                            <p class="text-muted">Il n'y a actuellement aucun événement disponible.</p>
                        </div>
                    @endforelse

                    <!-- Pagination -->
                    @if($events->hasPages())
                        <div class="vs-  mt-5">
                            {{ $events->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <aside class="sidebar-area">
                        <!-- Search Widget -->
                        <div class="widget widget_search events-widget">
                            <h3 class="widget_title">Rechercher un événement</h3>
                            <form class="search-form" action="{{ route('events.index') }}" method="GET">
                                <input type="text" name="q" placeholder="Rechercher..." value="{{ request('q') }}">
                                <button type="submit"><i class="far fa-search"></i></button>
                            </form>
                        </div>

                        <!-- Status Filter Widget -->
                        <div class="widget widget_categories events-widget">
                            <h3 class="widget_title">Statut</h3>
                            <ul>
                                <li>
                                    <a href="{{ route('events.index', array_merge(request()->query(), ['status' => 'upcoming'])) }}">
                                        À venir <span>({{ $upcomingCount }})</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('events.index', array_merge(request()->query(), ['status' => 'ongoing'])) }}">
                                        En cours <span>({{ $ongoingCount }})</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('events.index', array_merge(request()->query(), ['status' => 'past'])) }}">
                                        Passés <span>({{ $pastCount }})</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Categories Widget -->
                        @if($categories->count() > 0)
                            <div class="widget widget_categories events-widget">
                                <h3 class="widget_title">Catégories</h3>
                                <ul>
                                    @foreach($categories as $category)
                                        <li>
                                            <a href="{{ route('events.index', array_merge(request()->query(), ['category' => $category->id])) }}">
                                                {{ $category->name }} <span>({{ $category->events()->where('online', true)->count() }})</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </ul>
                        </div>
                        @endif

                        <!-- Recent Events Widget -->
                        <div class="widget events-widget">
                            <h3 class="widget_title">Événements récents</h3>
                            <div class="recent-post-wrap">
                                @php
                                    $recentEvents = \App\Models\Event::with(['category', 'user'])
                                        ->online()
                                        ->published()
                                        ->where('is_featured', false)
                                        ->orderBy('created_at', 'desc')
                                        ->take(3)
                                        ->get();
                                @endphp

                                @forelse($recentEvents as $event)
                                    <div class="recent-post">
                                        <div class="media-img">
                                            <a href="{{ route('events.show', ['id' => $event->id, 'slug' => $event->slug]) }}">
                                                @if($event->image)
                                                    <img src="{{ $event->img }}" alt="{{ $event->title }}">
                                                @else
                                                    <img src="{{ asset('front/assets/img/blog/recent-post-1-1.jpg') }}" alt="{{ $event->title }}">
                                                @endif
                                            </a>
                                        </div>
                                        <div class="media-body">
                                            <h4 class="post-title">
                                                <a class="text-inherit" href="{{ route('events.show', ['id' => $event->id, 'slug' => $event->slug]) }}">
                                                    {{ Str::limit($event->title, 50) }}
                                                </a>
                                            </h4>
                                            <div class="recent-post-meta">
                                                <a href="#">{{ $event->start_date->format('d/m/Y') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                @empty

                                @endforelse
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('title')
    Événements - Relations Publiques Afrique
@endsection
