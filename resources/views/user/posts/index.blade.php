@extends('layouts.front')

@section('content')
    <!-- Hero Section avec gradient moderne -->
    <div class="modern-hero-section">
        <div class="hero-background">
            <div class="hero-overlay"></div>
            <div class="hero-pattern"></div>
        </div>

        <div class="container position-relative">
            <div class="hero-content">
                <!-- Profile Card -->
                <div class="profile-card">
                    <div class="profile-avatar">
                        <img src="{{$user->profil?->img ?? asset('images/user2.jpg')}}"
                             alt="Photo de profil"
                             class="avatar-img">
                        <div class="avatar-status online"></div>
                        <button class="avatar-edit-btn" title="Modifier la photo">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>

                    <div class="profile-info">
                        <h1 class="profile-name">{{$user->profil?->fullname ?? $user->name}}</h1>
                        <p class="profile-role">{{$user->profil?->fonction ?? 'Membre'}}</p>
                        <div class="profile-badges">
                            <span class="badge badge-primary">
                                <i class="fas fa-calendar-alt"></i>
                                Membre depuis {{ $user->created_at->format('M Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ $user->posts()->where('online', 1)->count() }}</h3>
                            <p class="stat-label">Articles</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ \App\Models\Event::upcoming()->count() }}</h3>
                            <p class="stat-label">Événements</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ $user->created_at->diffInDays(now()) }}</h3>
                            <p class="stat-label">Jours</p>
                        </div>
                    </div>
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

                <a href="{{ route('tableau-de-bord') }}" class="nav-item active" data-tooltip="Tableau de bord">
                    <div class="nav-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <span class="nav-label">Dashboard</span>
                </a>

                <a href="{{ route('index.cours') }}" class="nav-item" data-tooltip="Mes cours">
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

    <!-- Dashboard moderne -->
    <div class="container">
        <div class="dashboard-section">
            <div class="dashboard-header">
                <h2 class="dashboard-title">
                    <i class="fas fa-tachometer-alt"></i>
                    Tableau de Bord
                </h2>
                <p class="dashboard-subtitle">Vue d'ensemble de votre activité</p>
            </div>

            <!-- Widgets Grid -->
            <div class="widgets-grid">
                <!-- Derniers articles -->
                <div class="widget widget-large">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-newspaper"></i>
                            <span>Mes derniers articles</span>
                        </div>
                        <a href="{{ route('blog') }}" class="widget-action">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="widget-content">
                        @php
                            $userPosts = $user->posts()->where('online', 1)->latest()->take(3)->get();
                        @endphp
                        @forelse($userPosts as $post)
                            <div class="content-item">
                                <div class="content-image">
                                    <img src="{{ $post->img }}" alt="{{ $post->name }}">
                                </div>
                                <div class="content-info">
                                    <h6 class="content-title">{{ Str::limit($post->name, 50) }}</h6>
                                    <div class="content-meta">
                                        <span class="content-date">
                                            <i class="fas fa-calendar"></i>
                                            {{ $post->created_at->format('d M Y') }}
                                        </span>
                                        <span class="content-status published">Publié</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                                <h6>Aucun article publié</h6>
                                <p>Commencez à écrire votre premier article</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Prochains événements -->
                <div class="widget widget-large">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Prochains événements</span>
                        </div>
                        <a href="{{ route('events.index') }}" class="widget-action">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="widget-content">
                        @php
                            $upcomingEvents = \App\Models\Event::upcoming()->published()->take(3)->get();
                        @endphp
                        @forelse($upcomingEvents as $event)
                            <div class="content-item">
                                <div class="content-image">
                                    <img src="{{ $event->img }}" alt="{{ $event->title }}">
                                </div>
                                <div class="content-info">
                                    <h6 class="content-title">{{ Str::limit($event->title, 50) }}</h6>
                                    <div class="content-meta">
                                        <span class="content-date">
                                            <i class="fas fa-clock"></i>
                                            {{ $event->start_date->format('d M Y H:i') }}
                                        </span>
                                        <span class="content-status upcoming">À venir</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <h6>Aucun événement à venir</h6>
                                <p>Découvrez nos prochains événements</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Widgets secondaires -->
                <div class="widget widget-small">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-chart-line"></i>
                            <span>Activité</span>
                        </div>
                    </div>
                    <div class="widget-content">
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="activity-info">
                                <span class="activity-label">Articles publiés</span>
                                <span class="activity-value">{{ $user->posts()->where('online', 1)->count() }}</span>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="activity-info">
                                <span class="activity-label">Jours d'activité</span>
                                <span class="activity-value">{{ $user->created_at->diffInDays(now()) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="widget widget-small">
                    <div class="widget-header">
                        <div class="widget-title">
                            <i class="fas fa-trophy"></i>
                            <span>Badges</span>
                        </div>
                    </div>
                    <div class="widget-content">
                        <div class="badges-grid">
                            <div class="badge-item">
                                <i class="fas fa-star"></i>
                                <span>Écrivain</span>
                            </div>
                            <div class="badge-item">
                                <i class="fas fa-users"></i>
                                <span>Membre</span>
                            </div>
                            <div class="badge-item">
                                <i class="fas fa-calendar-check"></i>
                                <span>Actif</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
<style>
/* ========================================
   STYLES MODERNES POUR LE TABLEAU DE BORD
   Utilisant les couleurs du thème existant
======================================== */

/* Hero Section Moderne */
.modern-hero-section {
    position: relative;
    background: linear-gradient(135deg, var(--theme-color) 0%, var(--theme-color2) 50%, var(--theme-color3) 100%);
    padding: 4rem 0 2rem;
    overflow: hidden;
    margin-bottom: 2rem;
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, var(--theme-color) 0%, var(--theme-color2) 50%, var(--theme-color3) 100%);
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.1);
}

.hero-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: radial-gradient(circle at 25% 25%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                      radial-gradient(circle at 75% 75%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
}

.hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: white;
}

/* Profile Card */
.profile-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.profile-avatar {
    position: relative;
    display: inline-block;
    margin-bottom: 1rem;
}

.avatar-img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid white;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease;
}

.avatar-img:hover {
    transform: scale(1.05);
}

.avatar-status {
    position: absolute;
    bottom: 10px;
    right: 10px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 3px solid white;
}

.avatar-status.online {
    background-color: var(--success-color);
}

.avatar-edit-btn {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: var(--theme-color);
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.avatar-edit-btn:hover {
    background: var(--theme-color2);
    transform: scale(1.1);
}

.profile-info {
    color: var(--title-color);
}

.profile-name {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--theme-color);
}

.profile-role {
    font-size: 1.1rem;
    color: var(--body-color);
    margin-bottom: 1rem;
}

.profile-badges {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
}

.badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.badge-primary {
    background: rgba(var(--theme-color-rgb), 0.1);
    color: var(--theme-color);
    border: 1px solid rgba(var(--theme-color-rgb), 0.2);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.stat-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--theme-color), var(--theme-color2));
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 1.5rem;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--theme-color);
    margin-bottom: 0.5rem;
}

.stat-label {
    color: var(--body-color);
    font-weight: 500;
    margin: 0;
}

/* Navigation Moderne */
.modern-nav-section {
    margin: 2rem 0;
}

.nav-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
    background: white;
    padding: 1.5rem;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--border-color);
}

.nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem;
    border-radius: 15px;
    text-decoration: none;
    color: var(--body-color);
    transition: all 0.3s ease;
    position: relative;
    background: transparent;
    border: 2px solid transparent;
}

.nav-item:hover {
    background: rgba(var(--theme-color-rgb), 0.05);
    border-color: rgba(var(--theme-color-rgb), 0.2);
    transform: translateY(-2px);
    color: var(--theme-color);
}

.nav-item.active {
    background: var(--theme-color);
    color: white;
    box-shadow: 0 8px 25px rgba(var(--theme-color-rgb), 0.3);
}

.nav-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(var(--theme-color-rgb), 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.5rem;
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.nav-item:hover .nav-icon,
.nav-item.active .nav-icon {
    background: rgba(var(--theme-color-rgb), 0.2);
    transform: scale(1.1);
}

.nav-item.active .nav-icon {
    background: rgba(255, 255, 255, 0.2);
}

.nav-label {
    font-size: 0.9rem;
    font-weight: 500;
    text-align: center;
}

.logout-form {
    margin: 0;
}

.nav-button {
    background: none;
    border: none;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem;
    border-radius: 15px;
    text-decoration: none;
    color: var(--error-color);
    transition: all 0.3s ease;
    cursor: pointer;
}

.nav-button:hover {
    background: rgba(var(--error-color), 0.1);
    transform: translateY(-2px);
}

/* Dashboard Section */
.dashboard-section {
    margin: 2rem 0;
}

.dashboard-header {
    text-align: center;
    margin-bottom: 3rem;
}

.dashboard-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--theme-color);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.dashboard-subtitle {
    font-size: 1.1rem;
    color: var(--body-color);
    margin: 0;
}

/* Widgets Grid */
.widgets-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    grid-template-rows: auto auto;
    gap: 2rem;
}

.widget {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--border-color);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.widget:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.widget-large {
    grid-column: span 1;
}

.widget-small {
    grid-column: span 1;
}

.widget-header {
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(var(--theme-color-rgb), 0.05), rgba(var(--theme-color2-rgb), 0.05));
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.widget-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 600;
    color: var(--theme-color);
    font-size: 1.1rem;
}

.widget-action {
    color: var(--theme-color);
    text-decoration: none;
    padding: 0.5rem;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.widget-action:hover {
    background: rgba(var(--theme-color-rgb), 0.1);
    transform: scale(1.1);
}

.widget-content {
    padding: 1.5rem;
}

/* Content Items */
.content-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.content-item:hover {
    background: rgba(var(--theme-color-rgb), 0.05);
    border-color: rgba(var(--theme-color-rgb), 0.1);
    transform: translateX(5px);
}

.content-image {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    overflow: hidden;
    flex-shrink: 0;
}

.content-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.content-info {
    flex: 1;
}

.content-title {
    font-weight: 600;
    color: var(--title-color);
    margin-bottom: 0.5rem;
    font-size: 1rem;
}

.content-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.9rem;
}

.content-date {
    color: var(--body-color);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.content-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.content-status.published {
    background: rgba(var(--success-color), 0.1);
    color: var(--success-color);
}

.content-status.upcoming {
    background: rgba(var(--theme-color-rgb), 0.1);
    color: var(--theme-color);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 2rem;
    color: var(--body-color);
}

.empty-icon {
    font-size: 3rem;
    color: var(--border-color);
    margin-bottom: 1rem;
}

.empty-state h6 {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--title-color);
}

.empty-state p {
    margin: 0;
    font-size: 0.9rem;
}

/* Activity Items */
.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.activity-item:hover {
    background: rgba(var(--theme-color-rgb), 0.05);
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(var(--theme-color-rgb), 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--theme-color);
}

.activity-info {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.activity-label {
    color: var(--body-color);
    font-size: 0.9rem;
}

.activity-value {
    font-weight: 600;
    color: var(--theme-color);
    font-size: 1.1rem;
}

/* Badges Grid */
.badges-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
    gap: 1rem;
}

.badge-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem;
    border-radius: 12px;
    background: rgba(var(--theme-color-rgb), 0.05);
    border: 1px solid rgba(var(--theme-color-rgb), 0.1);
    transition: all 0.3s ease;
}

.badge-item:hover {
    background: rgba(var(--theme-color-rgb), 0.1);
    transform: translateY(-2px);
}

.badge-item i {
    font-size: 1.5rem;
    color: var(--theme-color);
    margin-bottom: 0.5rem;
}

.badge-item span {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--theme-color);
    text-align: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    .widgets-grid {
        grid-template-columns: 1fr;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .nav-grid {
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    }

    .dashboard-title {
        font-size: 2rem;
        flex-direction: column;
        gap: 0.5rem;
    }

    .profile-name {
        font-size: 1.5rem;
    }

    .stat-number {
        font-size: 2rem;
    }
}

@media (max-width: 480px) {
    .modern-hero-section {
        padding: 2rem 0 1rem;
    }

    .profile-card {
        padding: 1.5rem;
    }

    .avatar-img {
        width: 100px;
        height: 100px;
    }

    .nav-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }

    .nav-item {
        padding: 0.75rem;
    }

    .nav-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
}
</style>
@endsection
