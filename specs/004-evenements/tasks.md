---
description: "Task list — Section Événements (vitrine)"
---

# Tasks: Section Événements (vitrine)

**Input**: Design documents from `specs/004-evenements/`
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/

**Tests** : INCLUS — le plan (gate « Test-first ») et chaque lot prévoient des tests Feature/Unit (PHPUnit). Tests sous `tests/Feature/Events/*` et `tests/Unit/Events/*`.

**Organization**: tâches groupées par user story (priorités spec : US1=P1, US2=P2, US4=P2, US3=P3).

**Contexte clé** : **évolution additive** du domaine `Event` existant (modèle, `EventController`, routes `events.*`, `EventResource`, `EventRegistration`). Deux dettes résolues : `online` → **format** (visibilité = `status='published'`, R1) et **ajout `pays_id`** (R2). Non-régression stricte (routes existantes, Lomé COM'TOUR, inscription interne).

> **Corrections post-analyse** (`/speckit-analyze`) intégrées : T015 (route d'inscription `events.register` — **absente** auparavant, C1), T034 (branchement + test inscription interne), T046 (alignement gate `->online()`→`->published()` sur `category/calendar/search`, F1).

## Format: `[ID] [P?] [Story] Description`

- **[P]** : parallélisable (fichiers différents, pas de dépendance sur une tâche incomplète).
- **[Story]** : US1 / US2 / US3 / US4 (phases user story uniquement).
- Chemins de fichiers absolus depuis la racine du dépôt.

---

## Phase 1: Setup (infrastructure partagée)

**Purpose**: amorçage config & assets de la section.

- [X] T001 Créer `config/events.php` (`per_page`, `per_page_step`, `a_la_une`) en miroir de `config/projets.php`, dans `config/events.php`
- [X] T002 [P] Ajouter les entrées Vite `resources/css/events.css` et `resources/js/events.js` au tableau `input` de `vite.config.js`
- [X] T003 [P] Créer les fichiers d'assets stub `resources/css/events.css` et `resources/js/events.js`
- [X] T004 [P] Créer l'arborescence de tests `tests/Feature/Events/` et `tests/Unit/Events/` (en miroir de `tests/*/Projets`)

---

## Phase 2: Foundational (prérequis bloquants)

**Purpose**: couche de données partagée par TOUTES les user stories (modèle Event étendu, tables liées, seed, route d'inscription).

**⚠️ CRITICAL**: aucune user story ne peut démarrer avant la fin de cette phase.

- [X] T005 Créer la migration corrective `add_pays_id_to_events` (colonne `pays_id` integer nullable + index, **sans contrainte DB** — R2) dans `database/migrations/`
- [X] T006 [P] Créer la migration `add_vitrine_columns_to_events` (`objectifs` text, `programme` longText, `public_cible` text, `compte_rendu` longText, `registration_mode` string(20) default `internal` + index, `registration_url` string nullable) dans `database/migrations/`
- [X] T007 [P] Créer la migration `create_event_speakers_table` (`id`, `event_id` FK cascade, `nom`, `role`, `organisation`, `photo`, `bio`, `position`, timestamps) dans `database/migrations/`
- [X] T008 [P] Créer la migration `create_event_medias_table` (`id`, `event_id` FK cascade, `type` string(20), `chemin` nullable, `url_embed` nullable, `legende` nullable, `position`, timestamps) dans `database/migrations/`
- [X] T009 [P] Créer le modèle `EventSpeaker` (`$fillable`/`$guarded`, `belongsTo(Event)`, scope `ordered`, accessor `photo_url`) dans `app/Models/EventSpeaker.php`
- [X] T010 [P] Créer le modèle `EventMedia` (`belongsTo(Event)`, constante `$types=[replay,image,document]`, scope `ordered`, accessor `embed`/`url`) dans `app/Models/EventMedia.php`
- [X] T011 Étendre le modèle `Event` (caster/`$casts` nouveaux champs ; constantes `$registrationModes`, `$temporalStatuses` ; accessors `temporal_status`, `temporal_status_label`, `format_label`, `has_replay`, `has_post_event_media`, `cta` ; **corriger `can_register`** (R1/R9 : ne dépend plus de `online`) ; relations `speakers()`/`medias()` ; documenter `online`=format ; `published()` = source de visibilité) dans `app/Models/Event.php` (dépend T005-T010)
- [X] T012 [P] Enrichir `CategorySeeder` avec des catégories `type='event'` (conférence, atelier, webinaire…) dans `database/seeders/CategorySeeder.php`
- [X] T013 [P] Créer/étendre `EventFactory`, `EventSpeakerFactory`, `EventMediaFactory` dans `database/factories/`
- [X] T014 Créer/mettre à jour `EventSeeder` idempotent (≥1 événement par statut temporel : à venir/en cours/clos ; intervenants ; médias post-événement ; modes inscription **interne ET externe** ; ajuster `online` en format — R1) dans `database/seeders/EventSeeder.php` (dépend T011-T013)
- [X] T015 **[C1]** Déclarer la route d'inscription interne `POST /evenements/{event}/inscription` → `EventRegistrationController@register`, nommée `events.register` (le contrôleur **existe** mais **aucune route ne le reliait**) dans `routes/web.php`
- [X] T016 Appliquer `php artisan migrate` puis `db:seed --class=CategorySeeder` et `--class=EventSeeder` ; vérifier le schéma (`pays_id`, nouvelles colonnes, tables `event_speakers`/`event_medias`) et la résolution de la route `events.register`
- [X] T017 [P] Tests Unit accessors `Event` (`temporal_status`, `can_register`, `has_replay`, `has_post_event_media`, `format_label`, `cta`) dans `tests/Unit/Events/EventAttributesTest.php`
- [X] T018 [P] Tests Unit relations & scopes (`speakers`/`medias`/`published`/`upcoming`/`ongoing`/`past`) dans `tests/Unit/Events/EventRelationsTest.php`

**Checkpoint**: socle de données + route d'inscription prêts — les user stories peuvent démarrer.

---

## Phase 3: User Story 1 - Parcourir les événements (Priority: P1) 🎯 MVP

**Goal**: listing public `/evenements` en cards groupées/filtrables par statut temporel (En cours / À venir / Clos), avec bannière, pagination/charger-plus, badge de statut et CTA contextuel.

**Independent Test**: charger `/evenements` avec des événements publiés de chaque statut → vérifier le bon groupe, les champs de card, le CTA, l'état vide (par groupe), et l'exclusion des brouillons.

### Tests for User Story 1

- [X] T019 [P] [US1] Test Feature listing : événements publiés listés et groupés par statut temporel, brouillon exclu, présentiel publié visible (R1) dans `tests/Feature/Events/ListingTest.php`
- [X] T020 [P] [US1] Test Feature card & état vide : champs (visuel, titre, dates, lieu/format, badge, résumé), CTA contextuel, **état vide par groupe** (En cours/À venir/Clos) dans `tests/Feature/Events/CardTest.php`

### Implementation for User Story 1

- [X] T021 [US1] Créer `EventSearchService` (requête base `published()`, regroupement/scope temporel, pagination) dans `app/Services/Events/EventSearchService.php`
- [X] T022 [US1] Créer le composant Livewire `GrilleEvents` (prop `#[Url] statut`, `perPage`, `mount`, `chargerPlus`, `render` via `EventSearchService`) dans `app/Livewire/Events/GrilleEvents.php`
- [X] T023 [P] [US1] Créer le composant card `event-card` (visuel, titre, date(s), lieu/format `format_label`, badge `temporal_status_label`/« Annulé », `resume_text`, CTA via accessor `cta`, slot compte à rebours) dans `resources/views/components/event-card.blade.php`
- [X] T024 [P] [US1] Créer la vue Livewire `grille-events` (sections En cours/À venir/Clos, grille de cards, bouton « Charger plus », **état vide explicite par groupe**) dans `resources/views/livewire/events/grille-events.blade.php`
- [X] T025 [US1] Mettre à jour `EventController@index` (rendu `events/index` avec `GrilleEvents` + comptes par statut ; **remplacer le gate `->online()` par `->published()`** sur la requête, `featuredEvents` et les compteurs — R1) dans `app/Http/Controllers/EventController.php`
- [X] T026 [P] [US1] Créer la vue `events/index` (bannière titre + accroche, intégration `@livewire(GrilleEvents)`, layout `front`) dans `resources/views/events/index.blade.php`
- [X] T027 [US1] Ajouter l'entrée de navigation « Événements » → `route('events.index')` (sans toucher au lien « Lomé COM'TOUR ») dans `resources/views/layouts/front.blade.php`
- [X] T028 [P] [US1] Styles de base listing/cards dans `resources/css/events.css`

**Checkpoint**: US1 fonctionnelle et testable seule (MVP).

---

## Phase 4: User Story 2 - Page détaillée + CTA dynamique (Priority: P2)

**Goal**: page individuelle `/evenements/{slug}-{id}` complète (objectifs, programme, intervenants, public cible) avec CTA dynamique (S'inscrire interne/externe, Voir replay/photos/compte rendu) et médias post-événement.

**Independent Test**: ouvrir une URL d'événement → vérifier l'affichage de tous les champs, le masquage des sections vides, le 404 sur brouillon, et le CTA conforme à l'état (à venir/clos, complet/closes, annulé).

### Tests for User Story 2

- [X] T029 [P] [US2] Test Feature détail : champs complets, sections optionnelles masquées si vides, 404 brouillon/slug inconnu, matrice CTA dynamique (interne/externe/replay/détails) dans `tests/Feature/Events/ShowTest.php`

### Implementation for User Story 2

- [X] T030 [US2] Enrichir `EventController@show` (gate `->published()`, eager-load `category`/`pays`/`user`/`speakers`/`medias`, `increment('view')`, événements similaires publiés) dans `app/Http/Controllers/EventController.php`
- [X] T031 [US2] Créer/refondre la vue `events/show` (bannière + visuel, date/heure, lieu/format, description, objectifs, programme/agenda, intervenants, public cible, **CTA dynamique**, bloc médias post-événement, similaires, compte à rebours si à venir) dans `resources/views/events/show.blade.php`
- [X] T032 [P] [US2] Créer les partials détail : liste des intervenants et galerie post-événement (replay via `App\Helper\VideoEmbed`, photos lightbox, document) dans `resources/views/events/partials/`
- [X] T033 [P] [US2] Ajouter au JS la lightbox galerie + init embed replay, et les styles de galerie/détail dans `resources/js/events.js` et `resources/css/events.css`
- [X] T034 [US2] **[C1/U1]** Brancher le CTA « S'inscrire » **interne** sur la route `events.register` (formulaire/modal ; **inscription réservée aux connectés** — gérer le 401/redirection login pour visiteur anonyme) + Test Feature inscription interne (auth requise, anti-doublon `isUserRegistered`, capacité `max_participants`/`current_participants`, `registration_deadline`) dans `resources/views/events/show.blade.php` et `tests/Feature/Events/RegistrationTest.php` (dépend T015, T030, T031)

**Checkpoint**: US1 + US2 fonctionnent indépendamment ; inscription interne opérationnelle.

---

## Phase 5: User Story 4 - Back-office (gestion des événements) (Priority: P2)

**Goal**: `EventResource` Filament enrichi (contenu détaillé, mode d'inscription interne/externe, format, compte rendu, type/pays), gestion des intervenants et médias post-événement, statut temporel automatique affiché, publication.

**Independent Test**: créer un événement complet en back-office, le publier, vérifier son apparition côté public dans le bon groupe temporel ; vérifier la contrainte URL externe et la bascule CTA replay.

### Tests for User Story 4

- [X] T035 [P] [US4] Test Feature admin : création complète → visible public ; Publier/Dépublier ; `external` sans `registration_url` rejeté ; ajout média `replay` → CTA replay ; `category_id` limité à `type='event'` dans `tests/Feature/Events/AdminEventResourceTest.php`

### Implementation for User Story 4

- [X] T036 [US4] Étendre le formulaire `EventResource` en sections (Identité/card ; Contenu détaillé : `objectifs`/`programme`/`public_cible` ; Format & lieu : `online`/`location` ; Inscription : `registration_mode`/`registration_url` requis si externe/`max_participants`/`price` ; Publication & classement : `status`/`is_featured`/`category_id`/`pays_id`/`user_id` ; Compte rendu) dans `app/Filament/Resources/EventResource.php`
- [X] T037 [P] [US4] Créer `SpeakersRelationManager` (CRUD intervenants : `nom`/`role`/`organisation`/`photo`/`bio`/`position` reorderable) dans `app/Filament/Resources/EventResource/RelationManagers/SpeakersRelationManager.php`
- [X] T038 [P] [US4] Créer `MediasRelationManager` (CRUD médias post-événement : `type`/`chemin`/`url_embed`/`legende`/`position` reorderable) dans `app/Filament/Resources/EventResource/RelationManagers/MediasRelationManager.php`
- [X] T039 [US4] Mettre à jour `EventResource` : enregistrer les RelationManagers (`getRelations`), colonne **statut temporel** + filtres `type`/Clos, **remplacer Activer/Désactiver par Publier/Dépublier** (sur `status`, + bulk) dans `app/Filament/Resources/EventResource.php` (dépend T036-T038)
- [X] T040 [US4] Restreindre `category_id` aux catégories `type='event'` et ajouter le Placeholder « Statut temporel » (lecture seule, FR-023) dans `app/Filament/Resources/EventResource.php`

**Checkpoint**: US1 + US2 + US4 opérationnelles ; contenu gérable de bout en bout.

---

## Phase 6: User Story 3 - Filtrer, trier, compte à rebours (Priority: P3)

**Goal**: filtres réactifs statut/type/pays, tri par date (proche/récent), compte à rebours sur les événements à venir.

**Independent Test**: appliquer chaque filtre/combinaison et le tri sur un jeu varié → résultats exacts et reflétés dans l'URL ; compte à rebours présent sur les à venir.

### Tests for User Story 3

- [X] T041 [P] [US3] Test Feature filtres/tri : `statut`/`type`/`pays` (combinaisons), `tri` proche/récent, état des paramètres dans l'URL, compte à rebours sur à venir dans `tests/Feature/Events/FiltersTest.php`

### Implementation for User Story 3

- [X] T042 [US3] Étendre `EventSearchService` (filtres `type` via `byCategory`, `pays` via `pays_id`, recherche `q`, tri `proche|recent`) dans `app/Services/Events/EventSearchService.php`
- [X] T043 [US3] Étendre `GrilleEvents` (props `#[Url]` `type`/`pays`/`tri`/`q` ; `updating` → `resetPage` + reset `perPage` ; `resetFilters`) dans `app/Livewire/Events/GrilleEvents.php`
- [X] T044 [US3] Ajouter la barre de filtres à la vue `grille-events` (selects statut/type/pays/tri + bouton Réinitialiser) dans `resources/views/livewire/events/grille-events.blade.php`
- [X] T045 [P] [US3] Implémenter le compte à rebours (lecture `data-start`, init sur cards/détail à venir) dans `resources/js/events.js` + styles dans `resources/css/events.css`

**Checkpoint**: toutes les user stories livrées.

---

## Phase 7: Polish & Cross-Cutting

**Purpose**: automatisation, accessibilité, performance, non-régression, style.

- [X] T046 **[F1]** Aligner le gate de visibilité (R1) sur les méthodes legacy : retirer `->online()` (→ s'appuyer sur `->published()`) dans `EventController@category`, `@calendar` et `@search` afin de ne pas masquer les événements **présentiels** publiés sur ces routes, dans `app/Http/Controllers/EventController.php`
- [X] T047 [P] Créer la commande optionnelle `events:mark-completed` (bascule éditoriale `published→completed` des événements clos ; l'affichage reste dérivé — R3/R8) dans `app/Console/Commands/MarkPastEventsCompleted.php`
- [X] T048 [P] Planifier `events:mark-completed` (quotidien) dans le scheduler `app/Console/Kernel.php`
- [X] T049 [P] S'assurer de la présence du visuel placeholder `public/images/default-event.jpg` (accessor `img`)
- [X] T050 [P] Passe accessibilité (clavier/aria sur filtres, galerie/lightbox, compte à rebours) dans les vues `events/*` et `events.js`
- [X] T051 [P] Revue performance : index DB (`pays_id`, `start_date`, `status`), eager-loading anti-N+1 sur listing/détail
- [X] T052 Tests de **non-régression** : `events.index/show/category/calendar/search` → 200 **et affichent les présentiels publiés** (R1/F1) ; route `events.register` résolue et fonctionnelle (C1) ; page « Lomé COM'TOUR » intacte dans `tests/Feature/Events/NonRegressionTest.php`
- [X] T053 Lancer `vendor/bin/pint` et corriger le style sur les fichiers de la feature
- [X] T054 [P] Mettre à jour `specs/004-evenements/quickstart.md` si des écarts d'implémentation apparaissent

---

## Dependencies & Execution Order

- **Setup (Phase 1)** : T001-T004 — aucun prérequis.
- **Foundational (Phase 2)** : T005-T018 — **bloque toutes les user stories**. T011 dépend de T005-T010 ; T014 dépend de T011-T013 ; T016 dépend des migrations/seeders + T015 (route).
- **US1 (Phase 3, P1)** : dépend de Foundational. MVP.
- **US2 (Phase 4, P2)** : dépend de Foundational. T034 dépend de T015 (route), T030, T031. Indépendante d'US1 (testable seule).
- **US4 (Phase 5, P2)** : dépend de Foundational. Indépendante (back-office) ; alimente les données consommées par US1/US2.
- **US3 (Phase 6, P3)** : dépend de Foundational + des fichiers `EventSearchService`/`GrilleEvents`/`grille-events` créés en US1 (les **étend**).
- **Polish (Phase 7)** : dépend de l'ensemble ; T052 (non-régression) dépend de T015, T025, T046.

### Ordre conseillé (livraison incrémentale)

1. Phase 1 → Phase 2 (socle, dont route d'inscription).
2. **US1** (MVP) → démontrable.
3. **US4** (pour produire/gérer le contenu réel) puis **US2** (ou l'inverse — indépendantes).
4. **US3** (amélioration découvrabilité).
5. **Polish** (dont alignement gate legacy T046 et non-régression T052).

## Parallel Execution Examples

- **Phase 2** : T006, T007, T008 (migrations distinctes) puis T009, T010 (modèles) en parallèle ; T012, T013 en parallèle ; T017, T018 en parallèle. T015 (route) parallélisable avec les migrations.
- **US1** : T019, T020 (tests) en parallèle ; T023, T024, T026, T028 (vues/assets distincts) en parallèle après T021/T022.
- **US2** : T032, T033 en parallèle après T031 ; T034 ensuite (dépend T030/T031).
- **US4** : T037, T038 (RelationManagers distincts) en parallèle ; T036 avant T039.
- **US3** : T045 en parallèle des tâches Livewire/service.
- **Polish** : T047-T051, T054 largement parallélisables ; T046 avant T052 ; T052/T053 en fin.

## Implementation Strategy

- **MVP** = Phase 1 + Phase 2 + **US1** : un listing public des événements RPA, groupé par statut temporel, alimenté par le seed — immédiatement démontrable.
- **Incrément 2** = US4 + US2 : gestion de contenu réelle + pages détaillées riches avec CTA dynamique, **inscription interne fonctionnelle** (route C1) et médias post-événement.
- **Incrément 3** = US3 : filtres/tri réactifs + compte à rebours.
- **Durcissement** = Polish : automatisation éditoriale, accessibilité, performance, **alignement du gate legacy (F1)** et **non-régression** (garde-fou des dettes `online`/`pays_id`/route d'inscription).

**Total : 54 tâches** — Setup 4, Foundational 14, US1 10, US2 6, US4 6, US3 5, Polish 9.
