/*
 * Section Événements (vitrine) — feature 004-evenements
 * Entrée Vite dédiée (cf. vite.config.js). Deux modules :
 *   1. Compte à rebours (FR-017) sur les cards/détail des événements à venir.
 *   2. Lightbox léger de la galerie post-événement (clavier + aria), calqué sur
 *      resources/js/projets.js. L'embed replay (YouTube/Vimeo) est rendu côté
 *      serveur via App\Helper\VideoEmbed ; ce fichier ne gère que les images.
 */
(function () {
    'use strict';

    var COUNTDOWN_LABELS = { days: 'j', hours: 'h', minutes: 'min', seconds: 's' };

    /* ------------------------------------------------------------------ */
    /* Compte à rebours                                                    */
    /* ------------------------------------------------------------------ */
    function renderCountdown(el) {
        var startAttr = el.getAttribute('data-start');
        if (!startAttr) {
            return;
        }
        var start = new Date(startAttr).getTime();
        if (isNaN(start)) {
            return;
        }

        var diff = start - Date.now();
        if (diff <= 0) {
            // L'événement a commencé : le rebours n'a plus de sens.
            el.classList.add('event-countdown--done');
            el.textContent = 'En cours';
            return;
        }

        var s = Math.floor(diff / 1000);
        var days = Math.floor(s / 86400);
        var hours = Math.floor((s % 86400) / 3600);
        var minutes = Math.floor((s % 3600) / 60);
        var seconds = s % 60;
        var parts = { days: days, hours: hours, minutes: minutes, seconds: seconds };

        if (!el.dataset.cdBuilt) {
            el.dataset.cdBuilt = '1';
            var html = '';
            Object.keys(COUNTDOWN_LABELS).forEach(function (key) {
                html +=
                    '<span class="event-countdown__item">' +
                    '<b data-cd="' + key + '">0</b>' +
                    '<small>' + COUNTDOWN_LABELS[key] + '</small>' +
                    '</span>';
            });
            el.innerHTML = html;
        }

        Object.keys(parts).forEach(function (key) {
            var node = el.querySelector('[data-cd="' + key + '"]');
            if (node) {
                node.textContent = key === 'days' ? String(parts[key]) : ('0' + parts[key]).slice(-2);
            }
        });
    }

    function tickCountdowns() {
        var nodes = document.querySelectorAll('[data-countdown]');
        for (var i = 0; i < nodes.length; i++) {
            renderCountdown(nodes[i]);
        }
    }

    var countdownTimer = null;
    function initCountdowns() {
        tickCountdowns();
        if (countdownTimer === null && document.querySelector('[data-countdown]')) {
            // Un seul intervalle global ; il re-balaie le DOM à chaque tick, donc
            // les cards ré-injectées par Livewire (filtres) sont prises en compte.
            countdownTimer = window.setInterval(tickCountdowns, 1000);
        }
    }

    /* ------------------------------------------------------------------ */
    /* Lightbox galerie photos                                             */
    /* ------------------------------------------------------------------ */
    function initLightbox() {
        var triggers = document.querySelectorAll('[data-event-lightbox]');
        if (!triggers.length) {
            return;
        }

        var overlay = document.querySelector('.event-lightbox');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'event-lightbox';
            overlay.setAttribute('role', 'dialog');
            overlay.setAttribute('aria-modal', 'true');
            overlay.setAttribute('aria-hidden', 'true');
            overlay.innerHTML =
                '<button type="button" class="event-lightbox__close" aria-label="Fermer">&times;</button>' +
                '<figure class="event-lightbox__figure">' +
                '<img class="event-lightbox__img" src="" alt="">' +
                '<figcaption class="event-lightbox__caption"></figcaption>' +
                '</figure>';
            document.body.appendChild(overlay);
        }

        var img = overlay.querySelector('.event-lightbox__img');
        var caption = overlay.querySelector('.event-lightbox__caption');
        var closeBtn = overlay.querySelector('.event-lightbox__close');
        var lastFocused = null;

        function open(src, legende) {
            lastFocused = document.activeElement;
            img.setAttribute('src', src);
            img.setAttribute('alt', legende || '');
            caption.textContent = legende || '';
            caption.style.display = legende ? '' : 'none';
            overlay.classList.add('is-open');
            overlay.setAttribute('aria-hidden', 'false');
            document.body.classList.add('event-lightbox-open');
            closeBtn.focus();
        }

        function close() {
            overlay.classList.remove('is-open');
            overlay.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('event-lightbox-open');
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

    /* ------------------------------------------------------------------ */
    /* Inscription interne (parcours connecté, EventRegistrationController) */
    /* ------------------------------------------------------------------ */
    function initRegistration() {
        var buttons = document.querySelectorAll('[data-event-register]');
        if (!buttons.length) {
            return;
        }
        var meta = document.querySelector('meta[name="csrf-token"]');
        var token = meta ? meta.getAttribute('content') : '';

        buttons.forEach(function (btn) {
            if (btn.dataset.registerBound) {
                return;
            }
            btn.dataset.registerBound = '1';
            btn.addEventListener('click', function () {
                var url = btn.getAttribute('data-url');
                var feedback = document.querySelector('[data-event-register-feedback]');
                btn.disabled = true;
                btn.classList.add('is-disabled');

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({})
                })
                    .then(function (res) {
                        return res.json().then(function (data) {
                            return { status: res.status, data: data || {} };
                        });
                    })
                    .then(function (r) {
                        if (r.data.redirect) {
                            window.location.href = r.data.redirect;
                            return;
                        }
                        if (feedback) {
                            feedback.textContent = r.data.message || '';
                            feedback.className = 'event-cta-feedback ' + (r.data.success ? 'text-success' : 'text-danger');
                        }
                        if (r.data.success) {
                            btn.textContent = 'Inscription confirmée';
                        } else {
                            btn.disabled = false;
                            btn.classList.remove('is-disabled');
                        }
                    })
                    .catch(function () {
                        if (feedback) {
                            feedback.textContent = 'Une erreur est survenue. Veuillez réessayer.';
                            feedback.className = 'event-cta-feedback text-danger';
                        }
                        btn.disabled = false;
                        btn.classList.remove('is-disabled');
                    });
            });
        });
    }

    function init() {
        initCountdowns();
        initLightbox();
        initRegistration();
    }

    document.addEventListener('DOMContentLoaded', init);
    // Navigation Livewire (wire:navigate) : ré-initialise après remplacement de page.
    document.addEventListener('livewire:navigated', init);
})();
