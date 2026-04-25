@extends('layouts.default')

@section('title', 'Formations')

@section('content')
    <!--==============================
    Breadcumb
============================== -->
    <div class="breadcumb-wrapper events-breadcrumb" data-bg-src="{{asset('front/assets/img/breadcumb/breadcumb-bg.png')}}">
        <div class="container z-index-common">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Formations</h1>
                <p class="breadcumb-text">Découvrez nos formations professionnelles et développez vos compétences</p>
                <div class="breadcumb-menu-wrap">
                    <ul class="breadcumb-menu">
                        <li><a href="{{url('/')}}">Accueil</a></li>
                        <li>Formations</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

<div class="container py-5">
    <!-- En-tête -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-bold text-theme mb-3">Nos Formations</h1>
            <p class="lead text-muted">Découvrez nos formations professionnelles et développez vos compétences</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('formations') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Recherche</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="Rechercher une formation...">
                        </div>

                        <div class="col-md-2">
                            <label for="category" class="form-label">Catégorie</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Toutes les catégories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="level" class="form-label">Niveau</label>
                            <select class="form-select" id="level" name="level">
                                <option value="">Tous les niveaux</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>
                                        {{ ucfirst($level) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="instructor" class="form-label">Formateur</label>
                            <select class="form-select" id="instructor" name="instructor">
                                <option value="">Tous les formateurs</option>
                                @foreach($instructors as $instructor)
                                    <option value="{{ $instructor->id }}" {{ request('instructor') == $instructor->id ? 'selected' : '' }}>
                                        {{ $instructor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="sort" class="form-label">Trier par</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Plus récent</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Prix croissant</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Prix décroissant</option>
                                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Mieux noté</option>
                                <option value="duration" {{ request('sort') == 'duration' ? 'selected' : '' }}>Durée</option>
                            </select>
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn text-white bg-theme w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des formations -->
    <div class="row">
        @forelse($formations as $formation)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm formation-card">
                    <div class="position-relative">
                        <a href="{{ route('formation.show', ['slug' => $formation->slug, 'id' => $formation->id]) }}" class="d-block">
                            <img src="{{ $formation->image_url }}" class="card-img-top" alt="{{ $formation->name }}" style="height: 250px; object-fit: cover; transition: transform 0.3s ease;">
                        </a>
                        @if($formation->is_featured)
                            <span class="badge bg-warning new-label position-absolute top-0 end-0 m-2">
                                <i class="fas fa-star"></i> Mise en avant
                            </span>
                        @endif
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-theme">
                                <i class="fas fa-layer-group me-1"></i>{{ ucfirst($formation->level_name) }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-theme">{{ $formation->name }}</h5>
                        <p class="card-text text-muted small">{{ Str::limit($formation->description, 100) }}</p>

                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> {{ $formation->instructor->name }}
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> {{ $formation->formatted_duration }}
                                </small>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $formation->rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <small class="text-muted ms-1">({{ $formation->rating_count }})</small>
                                </div>
                                <span class="badge price-badge">{{ $formation->formatted_price }}</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-users"></i> {{ $formation->enrollment_count }} inscrits
                                </small>
                                <a href="{{ route('formation.show', ['slug' => $formation->slug, 'id' => $formation->id]) }}"
                                   class="btn btn-sm text-white bg-theme">
                                    Voir détails
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Aucune formation trouvée</h4>
                    <p class="text-muted">Essayez de modifier vos critères de recherche.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($formations->hasPages())
        <div class="row mt-5">
            <div class="col-12 d-flex justify-content-center">
                {{ $formations->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
</div>

<style>
.formation-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
}

.formation-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.formation-card:hover .card-img-top {
    transform: scale(1.05);
}

.formation-card a {
    text-decoration: none;
    color: inherit;
}

.formation-card a:hover {
    text-decoration: none;
    color: inherit;
}

.badge.bg-theme {
    background-color: var(--theme-color) !important;
}

.btn.bg-theme {
    background-color: var(--theme-color) !important;
    border-color: var(--theme-color) !important;
}

.btn.bg-theme:hover {
    background-color: var(--secondary-color) !important;
    border-color: var(--secondary-color) !important;
}
</style>
@endsection
