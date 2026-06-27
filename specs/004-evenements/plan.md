# Implementation Plan: Section Événements (vitrine)

**Branch**: `004-evenements` | **Date**: 2026-06-27 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `specs/004-evenements/spec.md`

## Summary

Section publique **`/evenements`** présentant tous les événements organisés par RPA : listing en **cards** groupées/filtrables par statut temporel (**En cours / À venir / Clos**), avec filtres réactifs (statut, type, pays), tri par date et **compte à rebours** optionnel ; **page détaillée** par événement (objectifs, programme/agenda, intervenants, public cible, CTA dynamique S'inscrire / Voir replay·photos·compte rendu) ; back-office Filament enrichi (CRUD, statut temporel automatique, mode d'inscription **interne ou externe**, **upload de médias post-événement**).

**Particularité majeure vs 003** : le domaine **Événement existe déjà** (modèle `App\Models\Event`, `EventController`, routes `events.*` sous `/evenements`, `EventResource` Filament + `RegistrationsRelationManager`, `EventRegistration` + `EventRegistrationController`, accessors/scopes temporels `isUpcoming/isOngoing/isCompleted` + `scopeUpcoming/ongoing/past`). Cette feature est donc une **évolution additive** de l'existant, **pas** une section greenfield. Le plan privilégie l'extension du modèle et la réutilisation des patrons éprouvés (001 annuaire, 002 média, 003 projets) : trait `App\Helper\Sluggable`, helper `App\Helper\VideoEmbed` (replay YouTube/Vimeo en embed), patron Livewire `App\Livewire\Projets\GrilleProjets` → `GrilleEvents`, patron `App\Services\Projets\ProjetSearchService` → `EventSearchService`, référentiels `categories` (`type='event'` = **type d'événement**) et `pays`, `config/events.php` (miroir de `config/projets.php`).

**Deux dettes techniques de l'existant résolues par cette feature** (cf. [research.md](./research.md)) :
1. **`online` surchargé** — actuellement à la fois drapeau de *visibilité* (`activate()/deactivate()`, gate `->online()` dans `EventController`, requis par `can_register`) **et** « format en ligne » (label Filament « En ligne »). On le **recentre sur le format** (présentiel/en ligne) et on bascule la **visibilité publique sur `status='published'`** uniquement.
2. **`pays_id` absent du schéma** — référencé par `EventResource` (form/table/filtre) et la relation `pays()`, mais **aucune migration ne crée la colonne** → bug latent corrigé par une migration additive.

Aucune nouvelle dépendance npm/composer : compte à rebours + lightbox en JS léger (nouvelle entrée Vite `events.css|js`), embed vidéo via `VideoEmbed` existant.

## Technical Context

**Language/Version**: PHP `^8.1`, Laravel 10
**Primary Dependencies**: Filament 3 (admin), Livewire 3 (grille/filtres réactifs), Vite + Bootstrap 5 (front, cohérent avec projets/annuaire/média), `App\Helper\VideoEmbed` (existant, embed YouTube/Vimeo pour replay), `App\Helper\Sluggable` (existant), `intervention/image` (existant, miniatures/visuels). **Aucune nouvelle dépendance** (compte à rebours + lightbox en JS natif/léger).
**Storage**: MySQL 8 (prod) / SQLite (`database/database.sqlite`, in-memory pour tests). Fichiers sur disque `public` : `events` (existant, visuel), nouveaux sous-dossiers `events/speakers`, `events/medias`. Aucun nouveau disque.
**Testing**: PHPUnit (`phpunit.xml`), `tests/Feature/Events/*` & `tests/Unit/Events/*` (miroir de `tests/*/Projets` et `tests/*/Media`). `vendor/bin/pint` pour le style.
**Target Platform**: serveur Linux (web), navigateurs modernes ; responsive mobile/tablette/desktop.
**Project Type**: web — monolithe Laravel (front Blade/Livewire + admin Filament partageant les modèles Eloquent).
**Performance Goals**: listing/filtres < 1 s perçue (SC-001) ; pagination/charger-plus pour ≥ plusieurs dizaines d'événements (SC-008). Eager-loading systématique (anti N+1) sur `category` (type), `pays`, `speakers`, `medias`.
**Constraints**: **non-régression** stricte des surfaces existantes — routes `events.index/show/category/calendar/search`, `EventResource`, parcours d'inscription interne (`EventRegistrationController`), section « Lomé COM'TOUR » (qui pointe sur `events.index`). Toute modification de fichier existant est **additive** ou une **correction documentée** (online/pays_id). Les FK vers `categories`/`pays`/`users` restent des **colonnes `integer` sans contrainte DB** (tables legacy à `id` `int` sans AUTO_INCREMENT) — même contournement que 002/003.
**Scale/Scope**: contenu institutionnel (dizaines à quelques centaines d'événements), trafic public modéré ; ~3 migrations additives (colonnes events + `event_speakers` + `event_medias`) + 1 corrective (`pays_id`), 2 modèles nouveaux (`EventSpeaker`, `EventMedia`), 1 service, 1 composant Livewire, extension d'1 ressource Filament (+2 RelationManagers), réutilisation du layout `front`.

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

La constitution (`.specify/memory/constitution.md`) est encore le **modèle vierge** (placeholders non renseignés) : aucun principe ratifié n'impose de gate spécifique. Gates par défaut appliqués (bonnes pratiques + cohérence projet) :

| Gate (par défaut) | Statut | Justification |
|---|---|---|
| Simplicité / réutilisation (YAGNI) | ✅ PASS | Réutilise le **domaine Event existant** (modèle, scopes temporels, inscriptions, EventResource) + patrons `Sluggable`/`VideoEmbed`/`GrilleProjets`/`ProjetSearchService`, référentiels `categories`/`pays`, layout `front`. **Aucune** nouvelle dépendance. |
| Non-régression de l'existant | ⚠️ PASS sous conditions | Évolution d'une section existante : la résolution des dettes `online`/`pays_id` modifie un comportement existant. Mitigé par : changements additifs, tests de non-régression dédiés (`events.*` répondent 200, inscription interne inchangée, Lomé COM'TOUR intact), et décision documentée (research R1/R2). |
| Test-first / couverture | ✅ PASS (intention) | Tests Feature/Unit prévus en miroir de 003, écrits avec chaque lot (incluant SC-002/004/006/007). |
| Pas de dépendance injustifiée | ✅ PASS | Zéro ajout npm/composer. |

> Recommandation (hors périmètre) : renseigner la constitution via `/speckit-constitution`. La seule complexité à tracker est la résolution des dettes existantes (cf. *Complexity Tracking*).

**Re-check post-design (Phase 1)** : la conception (data-model + contrats) reste additive (3 tables/colonnes + 1 correctif), réutilise les patrons existants, et isole le changement de comportement `online`/visibilité derrière des tests de non-régression. ✅ PASS.

## Project Structure

### Documentation (this feature)

```text
specs/004-evenements/
├── plan.md              # Ce fichier
├── spec.md              # Spécification (déjà créée)
├── research.md          # Phase 0 — décisions techniques (dont dettes online/pays_id)
├── data-model.md        # Phase 1 — schéma de données (extension Event + 2 tables)
├── quickstart.md        # Phase 1 — amorçage & vérification
├── contracts/           # Phase 1 — contrats d'interface
│   ├── public-routes.md
│   ├── livewire-grille-events.md
│   └── admin-filament.md
├── checklists/
│   └── requirements.md  # Checklist qualité de la spec (16/16)
└── tasks.md             # Phase 2 (généré par /speckit-tasks — PAS ici)
```

### Source Code (repository root)

```text
app/
├── Models/
│   ├── Event.php                        # ÉTENDU : + champs (objectifs/programme/public_cible/compte_rendu,
│   │                                    #   registration_mode/registration_url, pays_id) ; online = format ;
│   │                                    #   accessors temporal_status/can_register (corrigé)/has_replay ;
│   │                                    #   relations speakers()/medias() ; scope published() = source visibilité
│   ├── EventSpeaker.php                 # NOUVEAU : intervenant (nom, role, photo, bio, position)
│   └── EventMedia.php                   # NOUVEAU : média post-événement (type replay/image/document, ...)
├── Services/
│   └── Events/
│       └── EventSearchService.php       # NOUVEAU : calqué sur Services/Projets/ProjetSearchService
├── Livewire/
│   └── Events/
│       └── GrilleEvents.php             # NOUVEAU : calqué sur Livewire/Projets/GrilleProjets (filtres statut/type/pays, tri)
├── Http/Controllers/
│   ├── EventController.php              # ÉTENDU : index → vue Livewire + comptes par statut ; show enrichi ;
│   │                                    #   gate ->online() remplacé par ->published() (R1)
│   └── EventRegistrationController.php  # INCHANGÉ (parcours interne) — réutilisé tel quel
├── Console/Commands/
│   └── MarkPastEventsCompleted.php      # NOUVEAU (optionnel, housekeeping éditorial) : events:mark-completed
└── Filament/Resources/
    └── EventResource.php                # ÉTENDU : sections (contenu détaillé, inscription, format) ;
                                         #   + RelationManagers/{SpeakersRelationManager, MediasRelationManager} ;
                                         #   Activer/Désactiver → Publier/Dépublier (status) ; badge statut temporel

database/
├── migrations/   # additives : add_vitrine_columns_to_events (objectifs, programme, public_cible,
│                 #  compte_rendu, registration_mode, registration_url) ; add_pays_id_to_events (CORRECTIF) ;
│                 #  create_event_speakers_table ; create_event_medias_table
├── factories/    # EventFactory (étendu/créé), EventSpeakerFactory, EventMediaFactory
└── seeders/      # EventSeeder (idempotent ; ≥1 événement par statut temporel + intervenants/médias post-événement,
                  #  modes inscription interne & externe) + CategorySeeder enrichi (catégories type='event')

resources/
├── views/
│   ├── events/
│   │   ├── index.blade.php              # listing (layouts/front + Livewire GrilleEvents) — REMPLACE le statique
│   │   └── show.blade.php               # détail enrichi (objectifs/programme/intervenants/public cible + CTA dynamique + countdown)
│   ├── components/
│   │   └── event-card.blade.php         # card (badge statut temporel, CTA contextuel)
│   └── livewire/events/grille-events.blade.php
├── js/events.js                         # countdown + lightbox galerie + embed replay (NOUVELLE entrée Vite)
└── css/events.css                       # styles section événements (NOUVELLE entrée Vite)

config/events.php                        # per_page, per_page_step, a_la_une (miroir config/projets.php)
routes/web.php                           # routes events.* INCHANGÉES (signatures conservées) — non-régression
vite.config.js                          # + 2 entrées (css/events.css, js/events.js)
resources/views/layouts/front.blade.php # + 1 entrée nav « Événements » → events.index (Lomé COM'TOUR conservé)

tests/
├── Feature/Events/*                     # listing/filtres/sections, show + CTA dynamique, statuts temporels,
│                                        #  inscription interne/externe, admin Filament, NON-RÉGRESSION (events.*, Lomé)
└── Unit/Events/*                        # EventSearchService, accessors temporal_status/can_register/has_replay, scopes, relations
```

**Structure Decision** : monolithe Laravel existant (Option « web »). Contrairement à 003 (greenfield), on **étend le domaine Event en place** ; le code nouveau est isolé sous des sous-dossiers `Events/` dédiés (Livewire, Services, tests) en miroir de `Projets/`. Le listing et le détail réutilisent le **layout public `front.blade.php`** (vitrine institutionnelle standard, comme `/projets`). Les modifications de fichiers existants sont soit **additives** (routes inchangées, `vite.config.js`, nav), soit des **corrections documentées** (`EventController` gate de visibilité, sémantique `online`, colonne `pays_id`) couvertes par des tests de non-régression.

## Phasing (lots d'implémentation, par dépendances)

> Le détail tâche-par-tâche sera généré par `/speckit-tasks`. Découpage cible (chaque lot livrable/testable) :

- **Lot 1 — Données & socle** *(dépend de rien)* : migrations additives (colonnes vitrine sur `events`, **correctif `pays_id`**, `event_speakers`, `event_medias`), extension `Event` (nouveaux champs `$casts`/accessors `temporal_status`, `can_register` corrigé, `has_replay`/`has_post_event_media` ; relations `speakers()`/`medias()` ; `online` = format ; `published()` source de visibilité), modèles `EventSpeaker`/`EventMedia`, `config/events.php`, factories + `EventSeeder` (≥1 événement par statut temporel, intervenants/médias, modes interne & externe) + catégories `type='event'`. Tests Unit (accessors temporels, can_register, has_replay, scopes, relations).
- **Lot 2 — Admin Filament** *(dépend L1)* : extension `EventResource` (sections : identité/card, **contenu détaillé** objectifs/programme/public cible/compte rendu, **inscription** mode interne|externe + URL, **format** présentiel/en ligne + lieu, statut éditorial & mise en avant, type & pays) ; **RelationManagers** `SpeakersRelationManager` (intervenants) et `MediasRelationManager` (replay/photos/compte rendu) ; remplacement des actions **Activer/Désactiver → Publier/Dépublier** (sur `status`) ; affichage du **badge statut temporel** calculé. Permet de produire du contenu. Tests Feature (CRUD, publication, modes inscription, médias post-événement).
- **Lot 3 — Backend public** *(dépend L1)* : `EventController` (index → données pour Livewire + comptes par statut ; **gate `->published()`** ; show enrichi avec eager-load `speakers`/`medias`/`category`/`pays`), `EventSearchService` (filtres statut temporel/type/pays + tri proche/récent), conservation des routes `events.*`, CTA dynamique (interne `route('events.register')` / externe `registration_url` / replay). Tests Feature (statut temporel exact, show + 404 brouillon, CTA selon état, **non-régression** routes existantes + inscription).
- **Lot 4 — Frontend listing & détail** *(dépend L3)* : vue `events/index` + Livewire `GrilleEvents` + `event-card` + vue `grille-events` (filtres réactifs statut/type/pays, tri, badge statut temporel, **compte à rebours** sur cards à venir, charger-plus/pagination, état vide), vue `events/show` (sections conditionnelles objectifs/programme/intervenants/public cible, **CTA dynamique** S'inscrire·Voir replay/photos/compte rendu, galerie post-événement avec lightbox + embed replay, countdown), entrées Vite `events.css|js`, entrée nav « Événements ». Tests Feature (filtres/URL, masquage sections optionnelles, CTA selon statut, countdown).
- **Lot 5 — Polish, automatisation & non-régression** *(dépend L1-L4)* : commande `events:mark-completed` (optionnelle, bascule éditoriale `published→completed` pour les événements clos ; le statut temporel d'affichage reste **dérivé des dates**) + entrée scheduler ; état vide explicite (listing & filtres), visuel placeholder, accessibilité (clavier/aria sur filtres/galerie/countdown), revue perf (index/eager-load), `pint`, complétion couverture de tests (SC-002/004/006/007 + non-régression Lomé COM'TOUR).

## Complexity Tracking

> Seules les **dettes de l'existant** justifient une déviation du principe « additif pur » ; documentées ici et couvertes par des tests de non-régression.

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| Modifier la sémantique de `online` (visibilité → format) | La spec exige un « format présentiel/en ligne » ; le champ est déjà labellisé « En ligne » dans Filament mais sert de gate de visibilité → conflit direct avec FR-009/FR-018. | Ajouter une colonne `format`/`is_online` séparée laisserait deux notions « en ligne » contradictoires et un gate de visibilité parallèle à `status` (double source de vérité), source de bugs durables. |
| Corriger l'absence de `pays_id` (migration additive) | `EventResource` et la relation `pays()` référencent une colonne **inexistante** en base → erreurs SQL latentes ; le filtre « par pays » (FR-015) en dépend. | « Ne pas y toucher » laisserait le filtre pays inopérant et un bug latent en production. |

> *Constitution vierge : aucun autre gate à justifier. Conception alignée sur les patrons existants (001/002/003).*
