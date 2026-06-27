/*
 * Section Projets (vitrine) — feature 003-projets-vitrine
 * Entrée Vite dédiée (cf. vite.config.js). Lightbox léger de la galerie de la
 * page détail (ouverture/fermeture clavier + aria). L'embed vidéo est rendu
 * côté serveur via App\Helper\VideoEmbed ; ce fichier ne gère que les images.
 */
(function () {
    'use strict';

    function initLightbox() {
        var triggers = document.querySelectorAll('[data-projet-lightbox]');
        if (!triggers.length) {
            return;
        }

        var overlay = document.querySelector('.projet-lightbox');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'projet-lightbox';
            overlay.setAttribute('role', 'dialog');
            overlay.setAttribute('aria-modal', 'true');
            overlay.setAttribute('aria-hidden', 'true');
            overlay.innerHTML =
                '<button type="button" class="projet-lightbox__close" aria-label="Fermer">&times;</button>' +
                '<figure class="projet-lightbox__figure">' +
                '<img class="projet-lightbox__img" src="" alt="">' +
                '<figcaption class="projet-lightbox__caption"></figcaption>' +
                '</figure>';
            document.body.appendChild(overlay);
        }

        var img = overlay.querySelector('.projet-lightbox__img');
        var caption = overlay.querySelector('.projet-lightbox__caption');
        var closeBtn = overlay.querySelector('.projet-lightbox__close');
        var lastFocused = null;

        function open(src, legende) {
            lastFocused = document.activeElement;
            img.setAttribute('src', src);
            img.setAttribute('alt', legende || '');
            caption.textContent = legende || '';
            caption.style.display = legende ? '' : 'none';
            overlay.classList.add('is-open');
            overlay.setAttribute('aria-hidden', 'false');
            document.body.classList.add('projet-lightbox-open');
            closeBtn.focus();
        }

        function close() {
            overlay.classList.remove('is-open');
            overlay.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('projet-lightbox-open');
            img.setAttribute('src', '');
            if (lastFocused && typeof lastFocused.focus === 'function') {
                lastFocused.focus();
            }
        }

        triggers.forEach(function (trigger) {
            if (trigger.dataset.lightboxBound) {
                return;
            }
            trigger.dataset.lightboxBound = '1';
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                open(trigger.getAttribute('href'), trigger.getAttribute('data-legende'));
            });
        });

        if (!overlay.dataset.bound) {
            overlay.dataset.bound = '1';
            closeBtn.addEventListener('click', close);
            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) {
                    close();
                }
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && overlay.classList.contains('is-open')) {
                    close();
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', initLightbox);
    // Navigation Livewire (wire:navigate) : ré-initialise après remplacement de page.
    document.addEventListener('livewire:navigated', initLightbox);
})();
