@extends('layouts.front')

@section('content')
    <div class="card shadow-lg border-0 overflow-hidden mt-5 mb-5 mycontainer">
        <div class="position-relative">
            <img src="{{ asset('images/front/ban.jpg') }}" alt="Bannière" class="img-fluid w-100" style="height: 300px; object-fit: cover;">
            <button class="btn btn-light btn-sm position-absolute top-0 end-0 m-3 rounded-circle shadow">
                <i class="fas fa-edit"></i>
            </button>
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-25"></div>
        </div>

        <div class="d-flex flex-column align-items-center justify-content-center position-relative">
            <div class="position-relative" style="margin-top: -120px;">
                <img src="{{$user->profil?->img ?? asset('images/user2.jpg')}}" alt="Photo de profil"
                     class="img-fluid rounded-circle border border-5 border-white shadow-lg"
                     style="width: 200px; height: 200px; object-fit: cover;">
                <button class="btn btn-light btn-sm position-absolute bottom-0 end-0 rounded-circle shadow">
                    <i class="fas fa-camera"></i>
               </button>
           </div>

            <div class="w-100 d-flex justify-content-between align-items-center p-4" style="background-color: rgba(var(--theme-color-rgb), 0.1);">
                <div class="text-center">
                    <h5 class="fw-bold fs-4 mb-0 text-theme">{{ $user->posts()->where('online', 1)->count() }}</h5>
                    <small class="text-muted">Articles</small>
                   </div>
                <div class="text-center">
                    <h4 class="fw-bold text-dark mb-1">{{$user->profil?->fullname ?? $user->name}}</h4>
                    <p class="text-muted mb-0">{{$user->profil?->fonction ?? 'Membre'}}</p>
               </div>
                <div class="text-center">
                    <h5 class="fw-bold fs-4 mb-0 text-theme">{{ $user->created_at->diffInDays(now()) }}</h5>
                    <small class="text-muted">Jours</small>
               </div>
           </div>
        </div>
    </div>

    <div class="card shadow mb-5 mycontainer">
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-center">
                        <a href="{{ route('profil') }}" class="btn btn-sm m-1 d-flex flex-column align-items-center p-3 text-theme" style="border: 2px solid var(--theme-color); background-color: transparent;">
                            <i class="fas fa-user fa-lg mb-2"></i>
                            <small class="fw-bold">Profil</small>
                        </a>

                        <a href="{{ route('events.index') }}" class="btn btn-sm m-1 d-flex flex-column align-items-center p-3 text-theme" style="border: 2px solid var(--theme-color); background-color: transparent;">
                            <i class="fas fa-calendar-alt fa-lg mb-2"></i>
                            <small class="fw-bold">Événements</small>
                        </a>

                        <a href="{{ route('blog') }}" class="btn btn-sm m-1 d-flex flex-column align-items-center p-3 text-theme" style="border: 2px solid var(--theme-color); background-color: transparent;">
                            <i class="fas fa-newspaper fa-lg mb-2"></i>
                            <small class="fw-bold">Articles</small>
                        </a>

                        <a href="{{ route('user.index') }}" class="btn btn-sm m-1 d-flex flex-column align-items-center p-3 text-theme" style="border: 2px solid var(--theme-color); background-color: transparent;">
                            <i class="fas fa-graduation-cap fa-lg mb-2"></i>
                            <small class="fw-bold">Abonnements</small>
                        </a>

                        <a href="{{ route('user.tableau-de-bord') }}" class="btn btn-sm m-1 d-flex flex-column align-items-center p-3 text-white" style="background-color: var(--theme-color); border: 2px solid var(--theme-color);">
                            <i class="fas fa-tachometer-alt fa-lg mb-2"></i>
                            <small class="fw-bold">Tableau de bord</small>
                        </a>

                        <a href="{{ route('user.index.cours') }}" class="btn btn-sm m-1 d-flex flex-column align-items-center p-3 text-theme" style="border: 2px solid var(--theme-color); background-color: transparent;">
                            <i class="fas fa-book fa-lg mb-2"></i>
                            <small class="fw-bold">Cours</small>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm m-1 d-flex flex-column align-items-center p-3">
                                <i class="fas fa-sign-out-alt fa-lg mb-2"></i>
                                <small class="fw-bold">Déconnexion</small>
                            </button>
                        </form>
                    </div>
                </div>
        </div>
        </div>
    </div>

    <div class="card shadow mycontainer">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-newspaper me-2"></i>
                Mes Articles
            </h5>
                       </div>
        <div class="card-body">
            <div class="row g-4">
                @forelse($posts as $post)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card h-100 shadow-sm">
                            <div class="position-relative">
                                <img src="{{$post->img}}" alt="{{$post->name}}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-primary">{{$post->created_at->format('d/m/Y')}}</span>
                        </div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title fw-bold">{{$post->name}}</h6>
                                <p class="card-text text-muted small flex-grow-1">{{Str::limit($post->resume, 100)}}</p>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-secondary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                </button>
                                        <button class="btn btn-outline-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                </button>
                                        <button class="btn btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucun article publié</h5>
                        <p class="text-muted">Commencez par publier votre premier article !</p>
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Publier un article
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

@endsection



@section('css')

@endsection
