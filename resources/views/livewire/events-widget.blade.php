<div class="vs-blog-wrapper space-top space-extra-bottom">
    <div class="container">
        <!-- Featured Events Section -->
        @if($featuredEvents->count() > 0)
            <div class="mb-5">
                <div class="text-center mb-5">
                    <h2 class="h2 mb-3 events-section-title">Événements à la une</h2>
                    <p class="text-lg text-muted">Découvrez nos événements phares en relations publiques</p>
                </div>

                <div class="row gx-4">
                    @foreach($featuredEvents as $event)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="vs-blog blog-single h-100 events-card">
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

                                    <div class="position-absolute top-0 end-0 m-3">
                                        <span class="badge events-featured px-3 py-2">À la une</span>
                                    </div>
                                </div>

                                <div class="blog-content d-flex flex-column h-100">
                                    <div class="blog-meta mb-3 events-meta">
                                        <a href="#"><i class="fal fa-user"></i>{{ $event->user?->name ?? 'Anonyme' }}</a>
                                        <a href="#"><i class="fal fa-calendar"></i>{{ $event->start_date->format('d/m/Y') }}</a>
                                        @if($event->location)
                                            <a href="#"><i class="fal fa-map-marker-alt"></i>{{ $event->location }}</a>
                                        @endif
                                    </div>

                                    <h3 class="blog-title h5 mb-3 events-title">
                                        <a href="{{ route('events.show', ['id' => $event->id, 'slug' => $event->slug]) }}">
                                            {{ Str::limit($event->title, 60) }}
                                        </a>
                                    </h3>

                                    <p class="mb-3 flex-grow-1">{{ Str::limit($event->resume ?: $event->description, 120) }}</p>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        @if($event->price > 0)
                                            <span class="events-price">{{ number_format($event->price, 2) }} €</span>
                                        @else
                                            <span class="events-price">Gratuit</span>
                                        @endif

                                        @if($event->registration_deadline && $event->registration_deadline->isFuture())
                                            <span class="badge events-registration">Inscriptions ouvertes</span>
                                        @endif
                                    </div>

                                    <a href="{{ route('events.show', ['id' => $event->id, 'slug' => $event->slug]) }}" class="vs-btn style3 mt-auto">
                                        <i class="far fa-angle-right"></i>Voir détails
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Upcoming Events Section -->
        @if($events->count() > 0)
            <div>
                <div class="text-center mb-5">
                    <h2 class="h2 mb-3 events-section-title">Événements à venir</h2>
                    <p class="text-lg text-muted">Planifiez votre participation aux prochains événements</p>
                </div>

                <div class="row gx-4">
                    @foreach($events->take(6) as $event)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="vs-blog blog-single h-100 events-card">
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

                                    @if($event->registration_deadline && $event->registration_deadline->isFuture())
                                        <div class="position-absolute bottom-0 start-0 m-3">
                                            <span class="badge events-registration">Inscriptions ouvertes</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="blog-content d-flex flex-column h-100">
                                    <div class="blog-meta mb-3 events-meta">
                                        <a href="#"><i class="fal fa-user"></i>{{ $event->user?->name ?? 'Anonyme' }}</a>
                                        <a href="#"><i class="fal fa-calendar"></i>{{ $event->start_date->format('d/m/Y') }}</a>
                                        @if($event->location)
                                            <a href="#"><i class="fal fa-map-marker-alt"></i>{{ $event->location }}</a>
                                        @endif
                                    </div>

                                    <h3 class="blog-title h5 mb-3 events-title">
                                        <a href="{{ route('events.show', ['id' => $event->id, 'slug' => $event->slug]) }}">
                                            {{ Str::limit($event->title, 60) }}
                                        </a>
                                    </h3>

                                    <p class="mb-3 flex-grow-1">{{ Str::limit($event->resume ?: $event->description, 120) }}</p>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        @if($event->price > 0)
                                            <span class="events-price">{{ number_format($event->price, 2) }} €</span>
                                        @else
                                            <span class="events-price">Gratuit</span>
                                        @endif

                                        <div class="text-muted small">
                                            <i class="fal fa-users me-1"></i>
                                            {{ $event->current_participants }}
                                            @if($event->max_participants)
                                                / {{ $event->max_participants }}
                                            @endif
                                        </div>
                                    </div>

                                    <a href="{{ route('events.show', ['id' => $event->id, 'slug' => $event->slug]) }}" class="vs-btn style3 mt-auto">
                                        <i class="far fa-angle-right"></i>Voir détails
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- View All Events Button -->
                <div class="text-center mt-5">
                    <a href="{{ route('events.index') }}" class="vs-btn style3 style2">
                        <i class="far fa-calendar-alt me-2"></i>Voir tous les événements
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
