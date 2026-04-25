@extends('layouts.front')

@section('content')
    <!-- Hero Section pour les cours -->
    <div class="modern-hero-section">
        <div class="hero-background">
            <div class="hero-overlay"></div>
            <div class="hero-pattern"></div>
        </div>

        <div class="container position-relative">
            <div class="hero-content">
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-book"></i>
                        Mes Cours
                    </h1>
                    <p class="page-subtitle">Découvrez et suivez vos cours achetés</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation moderne -->
    <div class="container">
        <div class="modern-nav-section">
            <div class="nav-grid">
                <a href="{{ route('profil') }}" class="nav-item" data-tooltip="Gérer mon profil">
                    <div class="nav-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="nav-label">Profil</span>
                </a>

                <a href="{{ route('events.index') }}" class="nav-item" data-tooltip="Voir les événements">
                    <div class="nav-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <span class="nav-label">Événements</span>
                </a>

                <a href="{{ route('blog') }}" class="nav-item" data-tooltip="Lire les articles">
                    <div class="nav-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <span class="nav-label">Articles</span>
                </a>

                <a href="{{ route('abonnements') }}" class="nav-item" data-tooltip="Mes abonnements">
                    <div class="nav-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <span class="nav-label">Abonnements</span>
                </a>

                <a href="{{ route('tableau-de-bord') }}" class="nav-item" data-tooltip="Tableau de bord">
                    <div class="nav-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <span class="nav-label">Dashboard</span>
                </a>

                <a href="{{ route('index.cours') }}" class="nav-item active" data-tooltip="Mes cours">
                    <div class="nav-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <span class="nav-label">Cours</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="nav-item logout-form">
                    @csrf
                    <button type="submit" class="nav-button" data-tooltip="Se déconnecter">
                        <div class="nav-icon">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <span class="nav-label">Déconnexion</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Contenu des cours -->
    <div class="container">
        <div class="courses-section">
            @if($cours->count() > 0)
                <div class="courses-grid">
                    @foreach($cours as $achatCours)
                        <div class="course-card">
                            <div class="course-image">
                                <img src="{{ $achatCours->course->img ?? asset('images/default-course.jpg') }}"
                                     alt="{{ $achatCours->course->name ?? 'Cours' }}">
                                <div class="course-status">
                                    <span class="status-badge purchased">Acheté</span>
                                </div>
                            </div>

                            <div class="course-content">
                                <h3 class="course-title">{{ $achatCours->course->name ?? 'Cours sans titre' }}</h3>
                                <p class="course-description">
                                    {{ Str::limit($achatCours->course->description ?? 'Aucune description disponible', 120) }}
                                </p>

                                <div class="course-meta">
                                    <div class="course-info">
                                        <span class="course-price">
                                            <i class="fas fa-tag"></i>
                                            {{ number_format($achatCours->course->price ?? 0, 0, ',', ' ') }} FCFA
                                        </span>
                                        <span class="course-date">
                                            <i class="fas fa-calendar"></i>
                                            Acheté le {{ $achatCours->created_at->format('d M Y') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="course-actions">
                                    <a href="{{ route('cours.suivre-le-cours', ['slug' => $achatCours->course->slug ?? 'cours', 'id' => $achatCours->course->id ?? 1]) }}"
                                       class="btn btn-primary">
                                        <i class="fas fa-play"></i>
                                        Suivre le cours
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $cours->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3>Aucun cours acheté</h3>
                    <p>Vous n'avez pas encore acheté de cours. Découvrez notre catalogue de formations.</p>
                    <a href="{{ route('blog') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i>
                        Découvrir les cours
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('css')
<style>
/* ========================================
   STYLES POUR LA PAGE DES COURS
   Utilisant les couleurs du thème existant
======================================== */

/* Page Header */
.page-header {
    text-align: center;
    color: white;
    margin-bottom: 2rem;
}

.page-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.page-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    margin: 0;
}

/* Courses Section */
.courses-section {
    margin: 3rem 0;
}

.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.course-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--border-color);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.course-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.course-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.course-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.course-card:hover .course-image img {
    transform: scale(1.05);
}

.course-status {
    position: absolute;
    top: 1rem;
    right: 1rem;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.purchased {
    background: rgba(var(--success-color), 0.9);
    color: white;
}

.course-content {
    padding: 1.5rem;
}

.course-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--title-color);
    margin-bottom: 0.75rem;
    line-height: 1.4;
}

.course-description {
    color: var(--body-color);
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.course-meta {
    margin-bottom: 1.5rem;
}

.course-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.course-price,
.course-date {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: var(--body-color);
}

.course-price {
    font-weight: 600;
    color: var(--theme-color);
}

.course-actions {
    display: flex;
    gap: 1rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-size: 0.95rem;
}

.btn-primary {
    background: var(--theme-color);
    color: white;
    flex: 1;
    justify-content: center;
}

.btn-primary:hover {
    background: var(--theme-color2);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(var(--theme-color-rgb), 0.3);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--border-color);
}

.empty-icon {
    font-size: 4rem;
    color: var(--border-color);
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--title-color);
    margin-bottom: 1rem;
}

.empty-state p {
    color: var(--body-color);
    font-size: 1.1rem;
    margin-bottom: 2rem;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 3rem;
}

.pagination {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.pagination a,
.pagination span {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    color: var(--body-color);
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
}

.pagination a:hover {
    background: var(--theme-color);
    color: white;
    border-color: var(--theme-color);
}

.pagination .active span {
    background: var(--theme-color);
    color: white;
    border-color: var(--theme-color);
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
        flex-direction: column;
        gap: 0.5rem;
    }

    .courses-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .course-card {
        margin: 0 1rem;
    }

    .course-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .course-content {
        padding: 1rem;
    }

    .course-title {
        font-size: 1.1rem;
    }

    .empty-state {
        padding: 2rem 1rem;
    }

    .empty-icon {
        font-size: 3rem;
    }
}
</style>
@endsection
