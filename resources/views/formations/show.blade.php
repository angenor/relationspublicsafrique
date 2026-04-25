@extends('layouts.default')

@section('title', $formation->name)

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Contenu principal -->
        <div class="col-lg-8">
            <!-- En-tête de la formation -->
            <div class="card shadow-sm mb-4">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="{{ $formation->image_url }}" class="img-fluid rounded-start h-100" alt="{{ $formation->name }}" style="object-fit: cover;">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h1 class="card-title text-theme h3">{{ $formation->name }}</h1>
                                @if($formation->is_featured)
                                    <span class="badge bg-warning">
                                        <i class="fas fa-star"></i> Mise en avant
                                    </span>
                                @endif
                            </div>

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-theme">
                                    <i class="fas fa-layer-group me-1"></i>{{ ucfirst($formation->level_name) }}
                                </span>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-globe me-1"></i>{{ strtoupper($formation->language) }}
                                </span>
                                @if($formation->category)
                                    <span class="badge bg-info">
                                        <i class="fas fa-tag me-1"></i>{{ $formation->category->name }}
                                    </span>
                                @endif
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">Formateur</small>
                                    <strong>{{ $formation->instructor->name }}</strong>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">Durée</small>
                                    <strong>{{ $formation->formatted_duration }}</strong>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $formation->rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="ms-2">{{ number_format($formation->rating, 1) }}/5 ({{ $formation->rating_count }} avis)</span>
                                </div>
                                <div class="text-end">
                                    <div class="h4 text-theme mb-0">{{ $formation->formatted_price }}</div>
                                    <small class="text-muted">{{ $formation->enrollment_count }} inscrits</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    @if($isEnrolled)
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-check-circle"></i> Vous êtes inscrit à cette formation
                            @if($enrollment)
                                <br><small>Progression: {{ $enrollment->progress }}%</small>
                            @endif
                        </div>
                        <div class="d-flex gap-2 justify-content-center">
                            <button class="btn btn-outline-theme" onclick="confirmFormationAction('unenroll', {{ $formation->id }})">
                                <i class="fas fa-times"></i> Se désinscrire
                            </button>
                            <a href="#" class="btn text-white bg-theme">
                                <i class="fas fa-play"></i> Continuer la formation
                            </a>
                        </div>
                    @else
                        @if($formation->canEnroll())
                            <button class="btn btn-lg text-white bg-theme" onclick="confirmFormationAction('enroll', {{ $formation->id }})">
                                <i class="fas fa-plus"></i> S'inscrire à la formation
                            </button>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                @if($formation->max_students && $formation->enrollment_count >= $formation->max_students)
                                    Cette formation est complète.
                                @elseif($formation->end_date && $formation->end_date->isPast())
                                    Cette formation est terminée.
                                @else
                                    Cette formation n'est pas disponible pour le moment.
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Description -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Description</h5>
                </div>
                <div class="card-body">
                    {!! $formation->content !!}
                </div>
            </div>

            <!-- Chapitres -->
            @if($formation->chapitres->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-theme text-white">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Programme de la formation</h5>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="chaptersAccordion">
                            @foreach($formation->chapitres as $index => $chapitre)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $chapitre->id }}">
                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $chapitre->id }}"
                                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                                aria-controls="collapse{{ $chapitre->id }}">
                                            <div class="d-flex justify-content-between w-100 me-3">
                                                <span>{{ $chapitre->name }}</span>
                                                <small class="text-muted">{{ $chapitre->duration }} min</small>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $chapitre->id }}"
                                         class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                         aria-labelledby="heading{{ $chapitre->id }}"
                                         data-bs-parent="#chaptersAccordion">
                                        <div class="accordion-body">
                                            {!! $chapitre->content !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Avis -->
            @if($formation->reviews->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-theme text-white">
                        <h5 class="mb-0"><i class="fas fa-star"></i> Avis des participants ({{ $formation->reviews->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @foreach($formation->reviews as $review)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong>{{ $review->user->name }}</strong>
                                        <div class="d-flex align-items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                            <span class="ms-2">{{ $review->rating }}/5</span>
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $review->created_at->format('d/m/Y') }}</small>
                                </div>
                                @if($review->comment)
                                    <p class="mb-0">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Informations de la formation -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-info"></i> Informations</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6"><strong>Prix:</strong></div>
                        <div class="col-6">{{ $formation->formatted_price }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Durée:</strong></div>
                        <div class="col-6">{{ $formation->formatted_duration }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Niveau:</strong></div>
                        <div class="col-6">{{ ucfirst($formation->level_name) }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>Langue:</strong></div>
                        <div class="col-6">{{ strtoupper($formation->language) }}</div>
                    </div>
                    @if($formation->max_students)
                        <div class="row mb-2">
                            <div class="col-6"><strong>Places:</strong></div>
                            <div class="col-6">{{ $formation->enrollment_count }}/{{ $formation->max_students }}</div>
                        </div>
                    @endif
                    @if($formation->certificate)
                        <div class="row mb-2">
                            <div class="col-6"><strong>Certificat:</strong></div>
                            <div class="col-6">{{ $formation->certificate }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Formations similaires -->
            @if($similarFormations->count() > 0)
                <div class="card shadow-sm">
                    <div class="card-header bg-theme text-white">
                        <h5 class="mb-0"><i class="fas fa-graduation-cap"></i> Formations similaires</h5>
                    </div>
                    <div class="card-body">
                        @foreach($similarFormations as $similar)
                            <div class="d-flex mb-3">
                                <img src="{{ $similar->image_url }}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;" alt="{{ $similar->name }}">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('formation.show', ['slug' => $similar->slug, 'id' => $similar->id]) }}"
                                           class="text-decoration-none text-theme">
                                            {{ Str::limit($similar->name, 40) }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">{{ $similar->formatted_price }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function enrollFormation(formationId) {
    fetch(`/formation/${formationId}/enroll`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', 'Inscription réussie !', 'Vous êtes maintenant inscrit à cette formation.');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('error', 'Erreur', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Erreur', 'Une erreur est survenue.');
    });
}

function unenrollFormation(formationId) {
    fetch(`/formation/${formationId}/unenroll`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', 'Désinscription réussie !', 'Vous avez été désinscrit de cette formation.');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('error', 'Erreur', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Erreur', 'Une erreur est survenue.');
    });
}

// Fonction pour afficher des notifications
function showNotification(type, title, message) {
    // Créer l'élément de notification
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 250px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        border: none;
        padding: 12px 16px;
        font-size: 0.9rem;
        ${type === 'success' ?
            'background: #28a745; color: white;' :
            'background: #dc3545; color: white;'
        }
    `;

    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fal fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2" style="font-size: 1rem;"></i>
            <div>
                <strong style="font-size: 0.9rem;">${title}</strong><br>
                <small style="font-size: 0.8rem;">${message}</small>
            </div>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.8rem;"></button>
        </div>
    `;

    document.body.appendChild(notification);

    // Auto-supprimer après 5 secondes
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
</script>

<style>
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

.btn-outline-theme {
    color: var(--theme-color) !important;
    border-color: var(--theme-color) !important;
}

.btn-outline-theme:hover {
    background-color: var(--theme-color) !important;
    border-color: var(--theme-color) !important;
    color: white !important;
}

.card-header.bg-theme {
    background-color: var(--theme-color) !important;
}

.card-header.bg-theme * {
    color: white !important;
}

.card-header.bg-theme h5,
.card-header.bg-theme h6,
.card-header.bg-theme .card-title,
.card-header.bg-theme i {
    color: white !important;
}
</style>
@endsection
