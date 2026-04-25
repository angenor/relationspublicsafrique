<!-- Modal de confirmation -->
<div id="confirmationModal" class="modal fade" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
            <div class="modal-header" style="background: var(--events-theme-color); border-radius: 15px 15px 0 0; border: none; padding: 15px 20px;">
                <h5 class="modal-title text-white fw-bold" id="confirmationModalLabel" style="font-size: 1.1rem;">
                    <i class="fal fa-question-circle me-2"></i>
                    <span id="modalTitle">Confirmation</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div class="text-center mb-3">
                    <div class="confirmation-icon mb-2" style="font-size: 2rem; color: var(--events-theme-color);">
                        <i class="fal fa-exclamation-triangle"></i>
                    </div>
                    <p class="mb-0" style="font-size: 1rem; color: #333; line-height: 1.5;" id="modalMessage">
                        Êtes-vous sûr de vouloir effectuer cette action ?
                    </p>
                </div>
            </div>
            <div class="modal-footer" style="border: none; padding: 0 20px 20px 20px; gap: 10px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 8px 16px; border-radius: 6px; font-weight: 500; border: none; font-size: 0.9rem;">
                    <i class="fal fa-times me-1"></i>Annuler
                </button>
                <button type="button" class="btn" id="confirmButton" style="background: var(--events-theme-color); color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 500; font-size: 0.9rem;">
                    <i class="fal fa-check me-1"></i>
                    <span id="confirmButtonText">Confirmer</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
class ConfirmationModal {
    constructor() {
        this.modal = document.getElementById('confirmationModal');
        this.title = document.getElementById('modalTitle');
        this.message = document.getElementById('modalMessage');
        this.confirmButton = document.getElementById('confirmButton');
        this.confirmButtonText = document.getElementById('confirmButtonText');
        this.confirmCallback = null;
    }

    show(options = {}) {
        const {
            title = 'Confirmation',
            message = 'Êtes-vous sûr de vouloir effectuer cette action ?',
            confirmText = 'Confirmer',
            confirmClass = 'btn',
            icon = 'fal fa-exclamation-triangle',
            onConfirm = null
        } = options;

        this.title.textContent = title;
        this.message.textContent = message;
        this.confirmButtonText.textContent = confirmText;
        this.confirmCallback = onConfirm;

        // Style du bouton de confirmation
        this.confirmButton.className = confirmClass;
        this.confirmButton.style.background = 'var(--events-theme-color)';
        this.confirmButton.style.color = 'white';
        this.confirmButton.style.border = 'none';
        this.confirmButton.style.padding = '8px 16px';
        this.confirmButton.style.borderRadius = '6px';
        this.confirmButton.style.fontWeight = '500';
        this.confirmButton.style.fontSize = '0.9rem';

        // Icône
        const iconElement = this.modal.querySelector('.confirmation-icon i');
        iconElement.className = icon;
        iconElement.style.color = 'var(--events-theme-color)';

        // Afficher la modal
        const bsModal = new bootstrap.Modal(this.modal);
        bsModal.show();
    }

    hide() {
        const bsModal = bootstrap.Modal.getInstance(this.modal);
        if (bsModal) {
            bsModal.hide();
        }
    }
}

// Instance globale
window.confirmationModal = new ConfirmationModal();

// Gérer le clic sur le bouton de confirmation
document.getElementById('confirmButton').addEventListener('click', function() {
    if (window.confirmationModal.confirmCallback) {
        window.confirmationModal.confirmCallback();
    }
    window.confirmationModal.hide();
});

// Fonctions utilitaires
window.confirmAction = function(options) {
    window.confirmationModal.show(options);
};

// Fonction pour les formations
window.confirmFormationAction = function(action, formationId, actionText) {
    const actionMessages = {
        'enroll': {
            title: 'S\'inscrire à la formation',
            message: 'Êtes-vous sûr de vouloir vous inscrire à cette formation ?',
            confirmText: 'S\'inscrire',
            icon: 'fal fa-user-plus'
        },
        'unenroll': {
            title: 'Se désinscrire',
            message: 'Êtes-vous sûr de vouloir vous désinscrire de cette formation ?',
            confirmText: 'Se désinscrire',
            icon: 'fal fa-user-minus'
        }
    };

    const config = actionMessages[action] || actionMessages['enroll'];

    window.confirmAction({
        ...config,
        onConfirm: function() {
            if (action === 'enroll') {
                enrollFormation(formationId);
            } else if (action === 'unenroll') {
                unenrollFormation(formationId);
            }
        }
    });
};
</script>
