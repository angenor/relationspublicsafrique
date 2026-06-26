/* ============================================================
   Section Média (newsroom / magazine) — feature 002-media-newsroom
   Entrée Vite dédiée : initialisation des lecteurs Plyr (audio + captions)
   et accessibilité de la sidebar mobile.
   ============================================================ */

import Plyr from "plyr";

const players = [];

function initMediaPlayers() {
    // Lecteurs audio (podcasts) avec sous-titres si présents.
    document.querySelectorAll(".media-audio-player").forEach((el) => {
        if (el.dataset.plyrInit === "1") {
            return;
        }
        el.dataset.plyrInit = "1";
        players.push(
            new Plyr(el, {
                captions: { active: true, update: true },
                controls: [
                    "play",
                    "progress",
                    "current-time",
                    "duration",
                    "mute",
                    "volume",
                    "captions",
                    "settings",
                    "download",
                ],
            }),
        );
    });

    // Fermer la sidebar mobile après navigation (a11y clavier / clic).
    document.querySelectorAll(".media-sidebar a").forEach((link) => {
        link.addEventListener("click", () => {
            document.body.classList.remove("media-sidebar-open");
        });
    });
}

/* Scroll infini : amélioration progressive du bouton « Charger plus ».
   On observe le bouton ; quand il entre dans le viewport, on le clique
   (déclenche loadMore côté Livewire). Le bouton reste utilisable au clavier. */
let loadMoreObserver = null;

function observeLoadMore() {
    if (!("IntersectionObserver" in window)) {
        return;
    }
    if (!loadMoreObserver) {
        loadMoreObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting && !entry.target.disabled) {
                        entry.target.click();
                    }
                });
            },
            { rootMargin: "320px 0px" },
        );
    }
    loadMoreObserver.disconnect();
    const btn = document.querySelector("[data-media-loadmore]");
    if (btn) {
        loadMoreObserver.observe(btn);
    }
}

function boot() {
    if (document.readyState !== "loading") {
        initMediaPlayers();
        observeLoadMore();
    } else {
        document.addEventListener("DOMContentLoaded", () => {
            initMediaPlayers();
            observeLoadMore();
        });
    }
}

boot();
// Re-init après navigation SPA Livewire.
document.addEventListener("livewire:navigated", () => {
    initMediaPlayers();
    observeLoadMore();
});
// Ré-observer le sentinel après chaque mise à jour Livewire (morph).
document.addEventListener("livewire:init", () => {
    if (window.Livewire) {
        window.Livewire.hook("morph.updated", observeLoadMore);
    }
});

window.MediaPlyr = Plyr;
