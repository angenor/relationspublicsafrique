<div>
    @if(Auth::check())
        @if($isRegistered)
            <!-- Utilisateur déjà inscrit -->
            <div class="alert alert-success mb-3" style="background-color: rgba(113, 61, 11, 0.1); border-left: 4px solid var(--events-theme-color); color: var(--events-theme-color);">
                <i class="fal fa-check-circle me-2"></i>
                <strong>Vous êtes inscrit à cet événement !</strong>
            </div>

            <button wire:click="cancelRegistration"
                    class="vs-btn"
                    style="background: var(--events-theme-color); color: white; border: none;"
                    onclick="return confirm('Êtes-vous sûr de vouloir annuler votre inscription ?')">
                <i class="fal fa-times me-2"></i>Annuler mon inscription
            </button>
        @elseif($canRegister)
            <!-- Bouton d'inscription -->
            <div class="mb-3">
                @if($event->registration_deadline)
                    <p class="events-price mb-3">
                        <i class="fal fa-clock me-2"></i>
                        Inscriptions ouvertes jusqu'au {{ $event->registration_deadline->format('d/m/Y H:i') }}
                    </p>
                @endif

                @if($event->max_participants)
                    <p class="events-price mb-3">
                        <i class="fal fa-users me-2"></i>
                        Places disponibles : {{ $event->max_participants - $event->current_participants }} / {{ $event->max_participants }}
                    </p>
                @endif
            </div>

            <button wire:click="openModal"
                    class="vs-btn"
                    style="background: var(--events-theme-gradient); color: white; border: none; padding: 12px 30px; font-weight: 600;">
                <i class="fal fa-user-plus me-2"></i>S'inscrire à l'événement
            </button>
        @else
            <!-- Inscriptions fermées -->
            <div class="alert alert-warning mb-0" style="background-color: rgba(113, 61, 11, 0.1); border-left: 4px solid var(--events-theme-color); color: var(--events-theme-color);">
                <i class="fal fa-exclamation-triangle me-2"></i>
                @if($event->registration_deadline && $event->registration_deadline->isPast())
                    Les inscriptions sont fermées (date limite dépassée).
                @elseif($event->max_participants && $event->current_participants >= $event->max_participants)
                    Cet événement est complet.
                @else
                    Les inscriptions ne sont pas ouvertes pour cet événement.
                @endif
            </div>
        @endif
    @else
        <!-- Utilisateur non connecté -->
        <div class="alert alert-info mb-3" style="background-color: rgba(113, 61, 11, 0.1); border-left: 4px solid var(--events-theme-color); color: var(--events-theme-color);">
            <i class="fal fa-info-circle me-2"></i>
            Vous devez être connecté pour vous inscrire à cet événement.
        </div>

        <div class="d-flex gap-3">
            <a href="{{ route('login') }}"
               class="vs-btn"
               style="background: var(--events-theme-color); color: white; border: none; padding: 12px 30px; font-weight: 600;">
                <i class="fal fa-sign-in-alt me-2"></i>Se connecter
            </a>

            <a href="{{ route('register') }}"
               class="vs-btn"
               style="background: var(--events-theme-light); color: white; border: none; padding: 12px 30px; font-weight: 600;">
                <i class="fal fa-user-plus me-2"></i>S'inscrire
            </a>
        </div>
    @endif

    <!-- Modal d'inscription -->
    @if($showModal)
        <div class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border: none; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                    <div class="modal-header" style="background: var(--events-theme-gradient); color: white; border-radius: 10px 10px 0 0;">
                        <h5 class="modal-title" style="color: white;">
                            <i class="fal fa-user-plus me-2"></i>Inscription à l'événement
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close btn-close-white" style="filter: invert(1) grayscale(100%) brightness(200%);"></button>
                    </div>

                    <div class="modal-body" style="padding: 30px;">
                        <div class="mb-4">
                            <h6 class="fw-bold" style="color: var(--events-theme-color);">Événement : {{ $event->title }}</h6>
                            <p class="text-muted mb-0">
                                <i class="fal fa-calendar me-2"></i>
                                {{ $event->start_date->format('d/m/Y H:i') }}
                                @if($event->location)
                                    - <i class="fal fa-map-marker-alt me-2"></i>{{ $event->location }}
                                @endif
                            </p>
                        </div>

                        <form wire:submit.prevent="register" id="eventRegistrationForm">
                            <div class="mb-4">
                                <label for="notes" class="form-label fw-bold" style="color: var(--events-theme-color);">
                                    <i class="fal fa-comment-alt me-2"></i>Commentaires (optionnel)
                                </label>
                                <textarea wire:model="notes"
                                          id="notes"
                                          class="form-control"
                                          rows="3"
                                          maxlength="500"
                                          placeholder="Ajoutez un commentaire ou une remarque..."
                                          style="border: 2px solid #e9ecef; border-radius: 8px; resize: vertical;"></textarea>
                                <small class="text-muted">
                                    <span id="charCount">0</span>/500 caractères
                                </small>
                                <div id="notesError" class="text-danger mt-2" style="display: none;">
                                    <i class="fal fa-exclamation-circle me-1"></i><span id="notesErrorMessage"></span>
                                </div>
                            </div>

                            <div class="d-flex gap-3 justify-content-end">
                                <button type="button"
                                        wire:click="closeModal"
                                        class="btn btn-secondary"
                                        style="padding: 10px 25px; border-radius: 8px;">
                                    <i class="fal fa-times me-2"></i>Annuler
                                </button>

                                <button type="submit"
                                        id="submitBtn"
                                        class="btn"
                                        style="background: var(--events-theme-gradient); color: white; border: none; padding: 10px 25px; border-radius: 8px; font-weight: 600;">
                                    <i class="fal fa-check me-2"></i>Confirmer l'inscription
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Messages flash -->
    @if (session()->has('success'))
        <div class="alert alert-success mt-3" style="background-color: rgba(113, 61, 11, 0.1); border-left: 4px solid var(--events-theme-color); color: var(--events-theme-color);">
            <i class="fal fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger mt-3" style="background-color: rgba(220, 53, 69, 0.1); border-left: 4px solid #dc3545; color: #dc3545;">
            <i class="fal fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Éléments du formulaire
            const notesField = document.getElementById('notes');
            const charCount = document.getElementById('charCount');
            const notesError = document.getElementById('notesError');
            const notesErrorMessage = document.getElementById('notesErrorMessage');
            const form = document.getElementById('eventRegistrationForm');
            const submitBtn = document.getElementById('submitBtn');

            let userHasTyped = false;

            // Compteur de caractères en temps réel
            if (notesField && charCount) {
                notesField.addEventListener('input', function() {
                    if (!userHasTyped) {
                        userHasTyped = true;
                        // Ajouter la classe pour activer l'affichage des erreurs
                        if (form) {
                            form.classList.add('form-touched');
                        }
                    }

                    const currentLength = this.value.length;
                    charCount.textContent = currentLength;

                    // Changer la couleur si proche de la limite
                    if (currentLength > 450) {
                        charCount.style.color = '#dc3545';
                    } else if (currentLength > 400) {
                        charCount.style.color = '#ffc107';
                    } else {
                        charCount.style.color = '#6c757d';
                    }

                    // Validation en temps réel uniquement si l'utilisateur a déjà tapé
                    if (userHasTyped) {
                        if (currentLength > 500) {
                            showError('Le commentaire ne doit pas dépasser 500 caractères.');
                        } else {
                            hideError();
                        }
                    }
                });

                // Validation lors de la perte de focus (blur)
                notesField.addEventListener('blur', function() {
                    if (userHasTyped && this.value.length > 500) {
                        showError('Le commentaire ne doit pas dépasser 500 caractères.');
                    }
                });
            }

            // Validation du formulaire
            if (form) {
                form.addEventListener('submit', function(e) {
                    let isValid = true;

                    // Valider le champ notes
                    if (notesField && notesField.value.length > 500) {
                        showError('Le commentaire ne doit pas dépasser 500 caractères.');
                        isValid = false;
                        e.preventDefault();
                        return false;
                    }

                    // Désactiver le bouton de soumission pour éviter les double clics
                    if (isValid && submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fal fa-spinner fa-spin me-2"></i>Inscription en cours...';

                        // Réactiver le bouton après 3 secondes en cas d'erreur
                        setTimeout(function() {
                            if (submitBtn.disabled) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = '<i class="fal fa-check me-2"></i>Confirmer l\'inscription';
                            }
                        }, 3000);
                    }
                });
            }

            // Fonction pour afficher une erreur
            function showError(message) {
                if (notesError && notesErrorMessage) {
                    notesErrorMessage.textContent = message;
                    notesError.style.display = 'block';

                    if (notesField) {
                        notesField.style.borderColor = '#dc3545';
                    }
                }
            }

            // Fonction pour cacher l'erreur
            function hideError() {
                if (notesError) {
                    notesError.style.display = 'none';

                    if (notesField) {
                        notesField.style.borderColor = '#e9ecef';
                    }
                }
            }

            // Réinitialiser le formulaire quand le modal se ferme
            document.addEventListener('livewire:init', () => {
                Livewire.on('modal-closed', () => {
                    if (form) {
                        form.reset();
                        form.classList.remove('form-touched');
                    }
                    if (charCount) {
                        charCount.textContent = '0';
                        charCount.style.color = '#6c757d';
                    }
                    hideError();
                    userHasTyped = false;
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fal fa-check me-2"></i>Confirmer l\'inscription';
                    }
                });
            });

            // Réinitialiser aussi au chargement initial de la page
            if (form) {
                form.classList.remove('form-touched');
            }
        });
    </script>
    @endpush
</div>
