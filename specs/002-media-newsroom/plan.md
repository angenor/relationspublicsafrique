# Implementation Plan: Section Média (newsroom / magazine)

**Branch**: `002-media-newsroom` | **Date**: 2026-06-25 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `specs/002-media-newsroom/spec.md`

## Summary

Nouvelle section publique **`/media`** : un blog/newsroom/magazine (inspiration brut.media) au design minimaliste avec **menu latéral gauche**, distincte de la page d'accueil `/` et du blog `/actualites` (non-régression stricte). Cinq types de contenu (article, interview, podcast, vidéo, reportage), mise en avant éditoriale (À la une, Dernières, Tendances, sélection épinglée), recherche/filtres/tri réactifs, pages détail SEO avec lecteurs audio (Plyr) et vidéo embarquée (YouTube/Vimeo), séries/playlists, partage social, commentaires modérés et newsletter opt-in.

**Approche technique** (détaillée dans [research.md](./research.md)) : modèle **`Media` autonome** (ne pas surcharger `Post`) + tables liées, réutilisant les référentiels et patrons existants — trait `App\Helper\Sluggable`, `App\Services\Annuaire\TextNormalizer`, patron Livewire `ProfilGrid`/`ProfilSearchService`, partage `\Share`, ressources Filament (`FormationResource`/`EventResource`). Vidéos en **embed** (pas d'hébergement interne), audio MP3 lu par **Plyr** (nouvelle dépendance front, entrée Vite dédiée). Publication programmée + tendances via le **scheduler déjà actif**. Newsletter & commentaires créés de zéro (anti-spam par honeypot, aucune infra existante).

## Technical Context

**Language/Version**: PHP `^8.1`, Laravel 10
**Primary Dependencies**: Filament 3 (admin), Livewire 3 (grille/filtres), Vite + Bootstrap 5 (front, cohérent avec l'annuaire), `jorenvanhocht/laravel-share` (partage, existant), **`plyr`** (lecteur audio/vidéo, **nouvelle**). Pas de FFmpeg/HLS (vidéos en embed).
**Storage**: MySQL 8 (prod) / SQLite in-memory (tests). Fichiers sur disque `public` (`media/covers|audio|subtitles|og|series`). Aucun nouveau disque (pas de vidéo interne).
**Testing**: PHPUnit (`phpunit.xml`), `tests/Feature/Media/*` & `tests/Unit/Media/*` (miroir de `tests/*/Annuaire`). `vendor/bin/pint` pour le style.
**Target Platform**: serveur Linux (web), navigateurs modernes ; responsive mobile/tablette/desktop.
**Project Type**: web — monolithe Laravel (front Blade/Livewire + admin Filament partageant les modèles Eloquent).
**Performance Goals**: listing/recherche < 1 s perçue (SC-002) ; pas d'exigence temps réel. Eager-loading systématique (anti N+1).
**Constraints**: **non-régression** absolue de `/` et `/actualites` (FR-041/SC-008) ; queue `sync` en local (prod : `database`/`redis` + table `jobs`) ; **cron `schedule:run`** requis pour publication programmée + tendances (déjà nécessaire pour l'annuaire).
**Scale/Scope**: contenu éditorial (centaines à milliers d'items), trafic public modéré ; 7 tables nouvelles, ~5 ressources Filament, 1 composant Livewire, 1 nouveau layout.

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

La constitution (`.specify/memory/constitution.md`) est encore le **modèle vierge** (placeholders non renseignés) : aucun principe ratifié n'impose de gate spécifique. Gates par défaut appliqués (bonnes pratiques + cohérence projet) :

| Gate (par défaut) | Statut | Justification |
|---|---|---|
| Simplicité / réutilisation (YAGNI) | ✅ PASS | Réutilise Sluggable, TextNormalizer, patrons Livewire/Filament, `\Share` ; pas d'infra superflue (pas de Scout, pas de FFmpeg, pas de S3). |
| Non-régression de l'existant | ✅ PASS | Section autonome ; aucune route/vue/modèle existants modifiés (FR-041). |
| Test-first / couverture | ✅ PASS (intention) | Tests Feature/Unit prévus en miroir de 001 ; à écrire avant/avec l'implémentation par lot. |
| Pas de dépendance injustifiée | ✅ PASS | Seul ajout : `plyr` (lecteur accessible) — justifié (R5). |

> Recommandation (hors périmètre de cette feature) : renseigner la constitution via `/speckit-constitution` pour formaliser ces gates. Aucune violation à tracker (section *Complexity Tracking* vide).

**Re-check post-design (Phase 1)** : la conception (data-model + contrats) n'introduit aucune violation nouvelle — tables additives, patrons réutilisés, une seule modif d'un fichier existant non-média (nav). ✅ PASS.

## Project Structure

### Documentation (this feature)

```text
specs/002-media-newsroom/
├── plan.md              # Ce fichier
├── spec.md              # Spécification (déjà créée)
├── research.md          # Phase 0 — décisions techniques
├── data-model.md        # Phase 1 — schéma de données
├── quickstart.md        # Phase 1 — amorçage & vérification
├── contracts/           # Phase 1 — contrats d'interface
│   ├── public-routes.md
│   ├── livewire-grille-medias.md
│   ├── admin-filament.md
│   └── console-notifications.md
├── checklists/
│   └── requirements.md  # Checklist qualité de la spec (16/16)
└── tasks.md             # Phase 2 (généré par /speckit-tasks — PAS ici)
```

### Source Code (repository root)

```text
app/
├── Models/
│   ├── Media.php                       # + traits Sluggable, SoftDeletes ; scopes/accessors/relations
│   ├── MediaSerie.php
│   ├── MediaComment.php
│   └── NewsletterSubscriber.php
├── Observers/
│   └── MediaObserver.php               # slug, titre_normalise, reading_time, notif publication
├── Services/
│   └── Media/
│       └── MediaSearchService.php      # calqué sur Services/Annuaire/ProfilSearchService.php
├── Helper/
│   └── VideoEmbed.php                  # parse YouTube/Vimeo → iframe
├── Livewire/
│   └── Media/
│       └── GrilleMedias.php            # calqué sur Livewire/Annuaire/ProfilGrid.php
├── Http/Controllers/
│   ├── MediaController.php             # home/index/show/serie/storeComment
│   └── NewsletterController.php        # subscribe/confirm/unsubscribe (URL signées)
├── Console/Commands/
│   ├── MediaPublishScheduled.php       # media:publish-scheduled
│   └── MediaRecomputePopularity.php    # media:recompute-popularity
├── Notifications/
│   ├── NewMediaPublishedNotification.php
│   └── NewsletterConfirmationNotification.php
└── Filament/Resources/
    ├── MediaResource.php  (+ Pages/, RelationManagers/{CommentairesRelationManager})
    ├── MediaSerieResource.php (+ Pages/, RelationManagers/{EpisodesRelationManager})
    ├── MediaCommentResource.php (+ Pages/)
    └── NewsletterSubscriberResource.php (+ Pages/)

database/
├── migrations/   # 7 migrations additives (media, media_series, category_media, media_tag,
│                 #  media_authors, media_comments, newsletter_subscribers) + index/backfill
├── factories/    # MediaFactory, MediaSerieFactory, MediaCommentFactory, NewsletterSubscriberFactory
└── seeders/      # MediaSeeder (idempotent, ≥1 par type + featured/pinned/scheduled/archived)

resources/
├── views/
│   ├── layouts/media.blade.php         # NOUVEAU layout sidebar gauche minimaliste
│   ├── components/
│   │   ├── media-sidebar.blade.php
│   │   ├── media-card.blade.php        # calqué sur livewire/annuaire/profil-carte
│   │   └── media-ligne.blade.php
│   ├── livewire/media/grille-medias.blade.php
│   └── media/{home,index,show,serie}.blade.php
├── js/media.js                         # import 'plyr' + init lecteurs (NOUVELLE entrée Vite)
└── css/media.css                       # styles section média (NOUVELLE entrée Vite)

config/media.php                        # per_page, a_la_une, popularité, rate limit
routes/web.php                          # + routes media.* (aucune route existante touchée)
vite.config.js                          # + 2 entrées (css/media.css, js/media.js)
app/Console/Kernel.php                  # + 2 commandes planifiées
resources/views/layouts/front.blade.php # + 1 entrée nav « Média » (seule modif d'un fichier existant)

tests/
├── Feature/Media/*                     # public, recherche, statuts, séries, commentaires, newsletter, non-régression
└── Unit/Media/*                        # MediaSearchService, slug, popularité, VideoEmbed, reading time
```

**Structure Decision** : monolithe Laravel existant (Option « web »). Le code média est **isolé** sous des sous-dossiers `Media/` dédiés (Livewire, Services, Filament, tests) pour la lisibilité, en miroir de l'organisation `Annuaire/` de la feature 001. Les seules modifications de fichiers existants sont des **points d'extension** (`routes/web.php`, `vite.config.js`, `app/Console/Kernel.php`) plus l'ajout d'une entrée de navigation « Média » dans `layouts/front.blade.php`.

## Phasing (lots d'implémentation, par dépendances)

> Le détail tâche-par-tâche sera généré par `/speckit-tasks`. Découpage cible (chaque lot livrable/testable) :

- **Lot 1 — Données & socle** *(dépend de rien)* : migrations additives (7 tables + index + backfill `titre_normalise`), modèles `Media/MediaSerie/MediaComment/NewsletterSubscriber` (traits, casts, relations, scopes, accessors), `MediaObserver`, `config/media.php`, factories + `MediaSeeder`. Tests Unit (slug, normalisation, scopes, reading time).
- **Lot 2 — Admin Filament** *(dépend L1)* : `MediaResource` (sections conditionnelles, uploads audio/cover/.vtt, embed, SEO, featured/pinned/status/scheduling, catégories/tags/pays/co-auteurs/série), `MediaSerieResource` + `EpisodesRelationManager`. Permet de produire du contenu. Tests Feature (CRUD/bulk/publish).
- **Lot 3 — Backend public** *(dépend L1)* : `MediaController` (home/index/show/serie + storeComment), `MediaSearchService`, `VideoEmbed`, routes `media.*`, incrément de vues, `\Share`. Tests Feature (statuts, show, similaires, **non-régression `/` & `/actualites`**).
- **Lot 4 — Frontend listing** *(dépend L3)* : `layouts/media.blade.php` (sidebar gauche), `media-sidebar`, Livewire `GrilleMedias` + `media-card`/`media-ligne` + vue `grille-medias`, rubriques home (À la une/Dernières/Tendances/sélection), filtres/tri/charger-plus, entrée nav « Média ». Tests Feature (filtres/tri/URL).
- **Lot 5 — Détail & lecteurs** *(dépend L3, L4)* : vue `media/show` (+ SEO/OG, bloc auteurs, partage incl. email, similaires, préc/suiv), `media/serie` (playlist), intégration **Plyr** (`resources/js/media.js`, dépendance npm, entrées Vite), embed YouTube/Vimeo responsive, sous-titres, téléchargement audio.
- **Lot 6 — Newsletter & commentaires** *(dépend L1-L5)* : `NewsletterController` (opt-in/confirm/unsubscribe signés), `NewsletterConfirmationNotification`/`NewMediaPublishedNotification`, formulaire d'abonnement + honeypot/throttle, `storeComment` + affichage modéré, `MediaCommentResource`/`CommentairesRelationManager`, `NewsletterSubscriberResource`.
- **Lot 7 — Programmation, tendances & polish** *(dépend L1-L6)* : commandes `media:publish-scheduled` & `media:recompute-popularity` + planification (`Kernel`), scroll infini (IntersectionObserver) en complément de « Charger plus », accessibilité (clavier/aria/captions), revue perf (index/eager-load), `pint`, complétion de la couverture de tests.

## Complexity Tracking

> Aucune violation de gate à justifier (constitution vierge, conception alignée sur les patrons existants). Section laissée vide intentionnellement.

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| — | — | — |
