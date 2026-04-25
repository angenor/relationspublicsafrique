@extends('layouts.default')

@section('content')
    <div class="breadcumb-wrapper events-breadcrumb" data-bg-src="{{asset('front/assets/img/breadcumb/breadcumb-bg.png')}}">
        <div class="container z-index-common">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">COM' TOUR - Inscription</h1>
                <div class="breadcumb-menu-wrap">
                    <ul class="breadcumb-menu">
                        <li><a href="{{url('/')}}">Accueil</a></li>
                        <li>Inscription</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <section class="space-top space-extra-bottom">
        <div class="container">
            <div class="row gx-80">
                <div class="col-lg-6 col-xl-6 mb-30 mb-lg-0">
                    <h2 class="h1 mt-n2" style="color: var(--events-theme-color)">Rejoindre le Lomé COM' TOUR</h2>
                    <p class="text-muted">Renseignez vos informations pour confirmer votre participation.</p>

                    <!-- Message de succès (caché par défaut) -->
                    <div id="successMessage" class="alert alert-success d-none" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h5 class="alert-heading mb-1">Inscription réussie !</h5>
                                <p class="mb-0">Votre inscription au Lomé COM' TOUR a été enregistrée avec succès. Vous recevrez un email de confirmation sous peu.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire d'inscription -->
                    <form method="POST" action="{{ route('lome.tour.register.store') }}" class="form-style5 ajax-contact" enctype="multipart/form-data" id="lomeTourForm" novalidate>
                        @csrf
                        <div class="vs-circle"></div>
                        <h3 class="form-title">Formulaire d'inscription</h3>

                        <div class="form-group">
                            <input type="text" name="prenom" value="{{ old('prenom') }}" placeholder="Prénom" class="form-control" required>
                            <div class="invalid-feedback"></div>
                            <div class="text-danger">@error('prenom') {{ $message }} @enderror</div>
                        </div>

                        <div class="form-group">
                            <input type="text" name="nom" value="{{ old('nom') }}" placeholder="Nom" class="form-control" required>
                            <div class="invalid-feedback"></div>
                            <div class="text-danger">@error('nom') {{ $message }} @enderror</div>
                        </div>

                        <div class="form-group">
                            <select name="statut" class="form-control" required>
                                <option value="" disabled {{ old('statut') ? '' : 'selected' }}>Statut</option>
                                <option value="etudiant" {{ old('statut')==='etudiant' ? 'selected' : '' }}>Étudiant</option>
                                <option value="professionnel" {{ old('statut')==='professionnel' ? 'selected' : '' }}>Professionnel</option>
                            </select>
                            <div class="invalid-feedback"></div>
                            <div class="text-danger">@error('statut') {{ $message }} @enderror</div>
                        </div>

                        <div class="form-group">
                            <input type="text" name="fonction" value="{{ old('fonction') }}" placeholder="Fonction" class="form-control" required>
                            <div class="invalid-feedback"></div>
                            <div class="text-danger">@error('fonction') {{ $message }} @enderror</div>
                        </div>

                        <div class="form-group">
                            <input type="text" name="telephone_whatsapp" value="{{ old('telephone_whatsapp') }}" placeholder="Numéro WhatsApp" class="form-control" required>
                            <div class="invalid-feedback"></div>
                            <div class="text-danger">@error('telephone_whatsapp') {{ $message }} @enderror</div>
                        </div>

                        <div class="form-group">
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email (optionnel)" class="form-control">
                            <div class="invalid-feedback"></div>
                            <div class="text-danger">@error('email') {{ $message }} @enderror</div>
                        </div>

                        <div class="form-group">
                            <input type="file" name="photo_professionnelle" accept="image/*" class="form-control">
                            <small class="text-muted">Photo professionnelle 1:1 (jpeg, png, max 2 Mo)</small>
                            <div class="invalid-feedback"></div>
                            <div class="text-danger">@error('photo_professionnelle') {{ $message }} @enderror</div>
                        </div>

                        <div class="form-group">
                            <textarea name="notes" rows="3" placeholder="Notes (optionnel)" class="form-control">{{ old('notes') }}</textarea>
                            <div class="invalid-feedback"></div>
                            <div class="text-danger">@error('notes') {{ $message }} @enderror</div>
                        </div>

                        <button type="submit" class="vs-btn" style="background: var(--events-theme-color); color: #fff" id="submitBtn">S'inscrire</button>
                    </form>
                </div>

                <div class="col-lg-6 col-xl-6">
                    <div class="mega-hover rounded-20 mt-4 mt-lg-5 mb-4"><img src="{{ asset('front/assets/img/about/contact-1.jpg') }}" alt="Lomé COM' TOUR" class="w-100"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Toast de succès -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <i class="fas fa-check-circle me-2"></i>
                <strong class="me-auto">Succès</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Votre inscription au Lomé COM' TOUR a été enregistrée avec succès !
            </div>
        </div>
    </div>
@endsection

@section('title')
    Lomé COM’ TOUR - Inscription
@endsection

@push('styles')
<!-- Bootstrap Validation CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-validator@0.11.9/dist/css/bootstrapValidator.min.css">

<style>
/* Styles personnalisés pour la validation */
.form-control.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.form-control.is-valid {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.valid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #28a745;
}

/* Message de succès */
#successMessage {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border: 1px solid #c3e6cb;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
}

#successMessage .alert-heading {
    color: #155724;
    font-weight: 600;
}

#successMessage p {
    color: #155724;
    margin-bottom: 0;
}

/* Toast de succès */
.toast-header.bg-success {
    background-color: #28a745 !important;
}

.toast-body {
    background-color: #f8f9fa;
}

/* Animation pour le formulaire */
.form-style5 {
    transition: all 0.3s ease;
}

.form-style5.hidden {
    opacity: 0;
    transform: translateY(-20px);
    pointer-events: none;
}

/* Bouton de soumission */
#submitBtn {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

#submitBtn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

#submitBtn:not(:disabled):hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
</style>
@endpush

@push('scripts')
<!-- Bootstrap Validation JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-validator@0.11.9/dist/js/bootstrapValidator.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('lomeTourForm');
    const successMessage = document.getElementById('successMessage');
    const successToast = document.getElementById('successToast');

    // Vérifier si l'inscription a réussi (via session)
    @if(session('success'))
        // Cacher le formulaire et afficher le message de succès
        form.style.display = 'none';
        successMessage.classList.remove('d-none');

        // Animation d'apparition du message de succès
        successMessage.style.opacity = '0';
        successMessage.style.transform = 'translateY(20px)';
        setTimeout(() => {
            successMessage.style.transition = 'all 0.5s ease';
            successMessage.style.opacity = '1';
            successMessage.style.transform = 'translateY(0)';
        }, 100);

        // Afficher la toast de succès
        const toast = new bootstrap.Toast(successToast);
        toast.show();

        // Scroll vers le message de succès
        setTimeout(() => {
            successMessage.scrollIntoView({ behavior: 'smooth' });
        }, 300);
    @endif

    // Configuration Bootstrap Validation
    $(form).bootstrapValidator({
        message: 'Cette valeur n\'est pas valide',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            prenom: {
                validators: {
                    notEmpty: {
                        message: 'Le prénom est requis'
                    },
                    stringLength: {
                        min: 2,
                        max: 255,
                        message: 'Le prénom doit contenir entre 2 et 255 caractères'
                    },
                    regexp: {
                        regexp: /^[a-zA-ZÀ-ÿ\s\-']+$/,
                        message: 'Le prénom ne peut contenir que des lettres, espaces, tirets et apostrophes'
                    }
                }
            },
            nom: {
                validators: {
                    notEmpty: {
                        message: 'Le nom est requis'
                    },
                    stringLength: {
                        min: 2,
                        max: 255,
                        message: 'Le nom doit contenir entre 2 et 255 caractères'
                    },
                    regexp: {
                        regexp: /^[a-zA-ZÀ-ÿ\s\-']+$/,
                        message: 'Le nom ne peut contenir que des lettres, espaces, tirets et apostrophes'
                    }
                }
            },
            statut: {
                validators: {
                    notEmpty: {
                        message: 'Veuillez sélectionner votre statut'
                    },
                    choice: {
                        min: 1,
                        message: 'Veuillez sélectionner votre statut'
                    }
                }
            },
            fonction: {
                validators: {
                    notEmpty: {
                        message: 'La fonction est requise'
                    },
                    stringLength: {
                        min: 2,
                        max: 255,
                        message: 'La fonction doit contenir entre 2 et 255 caractères'
                    }
                }
            },
            telephone_whatsapp: {
                validators: {
                    notEmpty: {
                        message: 'Le numéro WhatsApp est requis'
                    },
                    regexp: {
                        regexp: /^\+?\d{8,15}$/,
                        message: 'Format invalide. Exemple: +22812345678 ou 12345678'
                    },
                    stringLength: {
                        min: 8,
                        max: 15,
                        message: 'Le numéro doit contenir entre 8 et 15 chiffres'
                    }
                }
            },
            email: {
                validators: {
                    emailAddress: {
                        message: 'Format d\'email invalide'
                    },
                    stringLength: {
                        max: 255,
                        message: 'L\'email ne peut pas dépasser 255 caractères'
                    }
                }
            },
            photo_professionnelle: {
                validators: {
                    file: {
                        extension: 'jpeg,jpg,png',
                        type: 'image/jpeg,image/png',
                        maxSize: 2 * 1024 * 1024, // 2MB
                        message: 'Veuillez sélectionner une image valide (JPEG, PNG) de maximum 2MB'
                    }
                }
            },
            notes: {
                validators: {
                    stringLength: {
                        max: 1000,
                        message: 'Les notes ne peuvent pas dépasser 1000 caractères'
                    }
                }
            }
        }
    }).on('success.form.bv', function(e) {
        // Prévenir la soumission normale du formulaire
        e.preventDefault();

        // Soumettre le formulaire via AJAX
        const formData = new FormData(form);

        // Afficher un indicateur de chargement
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Inscription en cours...';
        submitBtn.disabled = true;

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Erreur lors de l\'inscription');
        })
        .then(data => {
            if (data.success) {
                // Animation de transition
                form.classList.add('hidden');

                // Attendre la fin de l'animation puis cacher complètement
                setTimeout(() => {
                    form.style.display = 'none';

                    // Afficher le message de succès avec animation
                    successMessage.classList.remove('d-none');
                    successMessage.style.opacity = '0';
                    successMessage.style.transform = 'translateY(20px)';

                    // Animation d'apparition du message de succès
                    setTimeout(() => {
                        successMessage.style.transition = 'all 0.5s ease';
                        successMessage.style.opacity = '1';
                        successMessage.style.transform = 'translateY(0)';
                    }, 100);

                    // Afficher la toast de succès
                    const toast = new bootstrap.Toast(successToast);
                    toast.show();

                    // Scroll vers le message de succès
                    setTimeout(() => {
                        successMessage.scrollIntoView({ behavior: 'smooth' });
                    }, 300);
                }, 300);
            } else {
                throw new Error(data.message || 'Erreur lors de l\'inscription');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de l\'inscription. Veuillez réessayer.');
        })
        .finally(() => {
            // Restaurer le bouton
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });
});
</script>
@endpush


