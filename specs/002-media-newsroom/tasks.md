---
description: "Task list for Section Média (002-media-newsroom)"
---

# Tasks: Section Média (newsroom / magazine)

**Input**: Design documents from `specs/002-media-newsroom/`
**Prerequisites**: [plan.md](./plan.md), [spec.md](./spec.md), [research.md](./research.md), [data-model.md](./data-model.md), [contracts/](./contracts/)

**Tests**: INCLUS — la spec (Success Criteria vérifiables, dont la non-régression SC-008), le `plan.md` (tests dans chaque lot) et la convention du projet (`tests/Feature/Annuaire`, `tests/Unit/Annuaire`) imposent une couverture PHPUnit. Chaque user story porte ses tests.

**Organization**: Tâches groupées par user story (US1→US6) pour une implémentation et une validation indépendantes.

## Format: `[ID] [P?] [Story] Description`

- **[P]** : parallélisable (fichiers différents, aucune dépendance sur une tâche incomplète)
- **[Story]** : US1…US6 (cf. spec.md)
- Chemins de fichiers absolus depuis la racine du dépôt

## Path Conventions

Monolithe Laravel (cf. plan.md « Project Structure ») : `app/`, `database/`, `resources/`, `routes/`, `config/`, `tests/` à la racine. Aucune route/vue/modèle existants modifiés, hormis les points d'extension (`routes/web.php`, `vite.config.js`, `app/Console/Kernel.php`, `app/Providers/AppServiceProvider.php`, nav dans `resources/views/layouts/front.blade.php`).

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Initialisation des dépendances et de la configuration propres à la section.

- [X] T001 Ajouter la dépendance `plyr` (npm) et déclarer 2 entrées Vite (`resources/css/media.css`, `resources/js/media.js`) dans `vite.config.js` ; créer les stubs `resources/css/media.css` et `resources/js/media.js`
- [X] T002 [P] Créer `config/media.php` (clés `per_page`, `per_page_step`, `a_la_une`, `rate_limit_public`, `popularity_half_life_days`) — miroir de `config/annuaire.php`
- [X] T003 [P] Garantir le lien symbolique storage (`php artisan storage:link`) et la présence des répertoires `storage/app/public/media/{covers,audio,subtitles,og,series}`

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Couche de données et modèles partagés par toutes les user stories.

**⚠️ CRITICAL**: Aucune user story ne peut démarrer avant la fin de cette phase.

- [X] T004 Migration `create_media_table` (toutes colonnes + casts implicites + index `idx_media_status_pub`, `idx_media_titre_norm`, `idx_media_type`, `idx_media_featured`, `idx_media_popularity`) dans `database/migrations/` (cf. data-model.md §`media`)
- [X] T005 [P] Migration `create_media_series_table` (titre, slug, titre_normalise, description, cover_image, type, online, position) dans `database/migrations/`
- [X] T006 [P] Migration `create_category_media_table` (pivot `category_id`/`media_id` cascade + `position` + `unique`) dans `database/migrations/` — patron `category_post`
- [X] T007 [P] Migration `create_media_tag_table` (PK composite `media_id`/`tag_id`, cascade) dans `database/migrations/` — patron exact `profil_tag` (réutilise la table `tags`)
- [X] T008 [P] Migration `create_media_authors_table` (`media_id`/`user_id` cascade, `role`, `position`, `unique`) dans `database/migrations/`
- [X] T009 [P] Helper `app/Helper/VideoEmbed.php` (parse URL YouTube/Vimeo → URL d'iframe + ratio 16:9)
- [X] T010 Modèle `app/Models/Media.php` : traits `App\Helper\Sluggable` + `SoftDeletes` + `HasFactory`, `$casts`, relations (`categories`/`tags`/`auteurs`/`contributions` (hasMany `MediaAuthor`)/`auteurPrincipal`/`pays`/`serie`/`commentaires`/`commentairesApprouves`), scopes (`published`/`featured`/`pinned`/`ofType`/`recent`/`popular`/`recommended`), accessors (`link`/`img`/`audioUrl`/`durationHuman`/`readingTimeHuman`/`embedHtml`) + modèle pivot léger `app/Models/MediaAuthor.php` — dépend de T004–T009
- [X] T011 [P] Modèle `app/Models/MediaSerie.php` (Sluggable, `medias()` ordonné saison/épisode/position) — dépend de T005
- [X] T012 Observer `app/Observers/MediaObserver.php` (`creating`→slug depuis `titre` ; `saving`→`titre_normalise` via `App\Services\Annuaire\TextNormalizer` + `reading_time` mots/200 ; `saved`→hook publication, no-op pour l'instant) + enregistrement dans `app/Providers/AppServiceProvider.php::boot()` — dépend de T010
- [X] T013 [P] Factories `database/factories/MediaFactory.php` et `MediaSerieFactory.php` — dépend de T010, T011
- [X] T014 Seeder `database/seeders/MediaSeeder.php` idempotent (catégories `type='media'`, séries, ≥1 contenu par type, dont `featured`, `is_pinned`, `scheduled`, `archived`) — dépend de T013
- [X] T015 [P] Tests unitaires `tests/Unit/Media/MediaModelTest.php` (génération du slug, `titre_normalise`/normalisation, `reading_time`, scopes `published`/`recent`/`popular`/`recommended`) — dépend de T010, T012

**Checkpoint**: Socle de données prêt — les user stories peuvent démarrer (en parallèle si l'équipe le permet).

---

## Phase 3: User Story 1 — Publier et organiser les contenus média (back-office) (Priority: P1) 🎯 MVP

**Goal**: Permettre à l'équipe éditoriale de créer/éditer/publier des contenus de tous types et de gérer séries, mise en avant, statut et publication programmée.

**Independent Test**: Créer en admin un contenu de chaque type (champs conditionnels corrects), publier, vérifier l'enregistrement et l'invisibilité publique tant que non publié ; programmer un contenu et constater sa publication automatique à l'échéance.

### Tests (US1)

- [X] T016 [P] [US1] Feature test `tests/Feature/Media/MediaAdminTest.php` (création de chaque type via le form, bulk `publish`, visibilité par statut)

### Implementation (US1)

- [X] T017 [US1] `app/Filament/Resources/MediaResource.php` — form (Sections « Contenu / Média conditionnelle / Couverture & SEO / Classement / Série / Publication »), visibilité des champs média **pilotée par `media_kind`** (`->visible(fn (\Filament\Forms\Get $get) => ...)`, valable pour interview/reportage multimédia), slug auto via `afterStateUpdated`, uploads (cover image, audio `acceptedFileTypes([...audio...])`, `.vtt`), `embed_url` validé YouTube/Vimeo, **co-auteurs via `Repeater::make('contributions')->relationship()`** (user + rôle + position)
- [X] T018 [US1] `MediaResource` — table (ImageColumn, BadgeColumn `type`/`status` couleurs pattern `EventResource`, IconColumn `featured`/`is_pinned`, filtres `type`/`status`/`serie`/`categories`/`featured`, bulk `publish`/`archive`/`feature`)
- [X] T019 [P] [US1] Pages `app/Filament/Resources/MediaResource/Pages/{ListMedia,CreateMedia,EditMedia}.php`
- [X] T020 [US1] Méthodes modèle `Media::publish()/archive()/feature()` (utilisées par les bulk actions) dans `app/Models/Media.php`
- [X] T021 [US1] `app/Filament/Resources/MediaSerieResource.php` (form/table) + Pages `MediaSerieResource/Pages/*`
- [X] T022 [US1] `app/Filament/Resources/MediaSerieResource/RelationManagers/EpisodesRelationManager.php` (épisodes ordonnés saison/épisode/position, pattern `RegistrationsRelationManager`)
- [X] T023 [US1] Commande `app/Console/Commands/MediaPublishScheduled.php` (`media:publish-scheduled` : `scheduled→published` quand `published_at<=now()`) + planification `->everyMinute()` dans `app/Console/Kernel.php`

**Checkpoint**: US1 fonctionnelle — production de contenu possible.

---

## Phase 4: User Story 2 — Découvrir et lire les contenus sur /media (Priority: P1) 🎯 MVP

**Goal**: Page d'accueil de section (À la une, Dernières, Tendances, sélection éditoriale) au design minimaliste avec menu latéral gauche, et page de détail aérée (auteur, partage, similaires, préc/suiv).

**Independent Test**: Visiter `/media` → rubriques correctes ; ouvrir un article publié → détail complet ; un contenu `draft/scheduled/archived` → 404 ; `/` et `/actualites` inchangés.

### Tests (US2)

- [X] T024 [P] [US2] Feature test `tests/Feature/Media/MediaPublicTest.php` (home : rubriques ; show : publié=200, non publié=404, incrément vues ; **non-régression `/` et `/actualites`**)

### Implementation (US2)

- [X] T025 [US2] Layout `resources/views/layouts/media.blade.php` (flex 2 colonnes, sidebar gauche fixe, `@livewireStyles/@livewireScripts`, `theme-variables.css`, `@vite(['resources/css/media.css','resources/js/media.js'])`, burger mobile) + `resources/views/components/media-sidebar.blade.php`
- [X] T026 [P] [US2] Composants `resources/views/components/media-card.blade.php` (cover, badge type, durée/temps de lecture, titre, auteur) et `media-ligne.blade.php` (pattern `profil-carte`/`profil-ligne`)
- [X] T027 [US2] `app/Http/Controllers/MediaController.php@home` (aLaUne/dernieres/tendances/selection via scopes + `categoriesMedia` sidebar) + route `media.home` (`/media`) dans `routes/web.php`
- [X] T028 [US2] Vue `resources/views/media/home.blade.php` (zone À la une 1-3, Dernières publications, Tendances/plus lus, sélection éditoriale, grille des derniers paginée)
- [X] T029 [US2] `MediaController@show` (filtre strict slug+id + `published()`, `increment('view')`, similaires, préc/suiv par date, `\Share`) + route `media.show` (`/media/{slug}-{id}` avec `->where([...])`) dans `routes/web.php`
- [X] T030 [US2] Vue `resources/views/media/show.blade.php` (chapô, corps, bloc auteur(s), `@section('meta')` SEO/OG depuis champs dédiés, partage LinkedIn/X/Facebook + `mailto:`, similaires, navigation préc/suiv)
- [X] T031 [US2] Ajouter l'entrée de navigation « Média » → `route('media.home')` dans `resources/views/layouts/front.blade.php`

**Checkpoint**: US1 + US2 = **MVP** (contenu produit en admin et consultable publiquement).

---

## Phase 5: User Story 3 — Rechercher, filtrer et trier (Priority: P2)

**Goal**: Explorateur réactif (recherche titre/mot-clé/auteur ; filtres type/thématique/pays/auteur ; tri récent/populaire/recommandé ; « Charger plus » ; état dans l'URL).

**Independent Test**: Sur `/media/explorer`, chaque filtre restreint le jeu, la combinaison est un ET, le tri change l'ordre, l'URL est partageable/rechargeable, « Charger plus » ajoute des items.

### Tests (US3)

- [X] T032 [P] [US3] Feature test `tests/Feature/Media/MediaExplorerTest.php` (recherche normalisée, filtres combinés, tri, état URL, `loadMore`)
- [X] T033 [P] [US3] Test unitaire `tests/Unit/Media/MediaSearchServiceTest.php` (terme/filtres/tri)

### Implementation (US3)

- [X] T034 [US3] `app/Services/Media/MediaSearchService.php` (`recherche(array): Builder` base `published()`, `applyTermeRecherche` normalisé `LIKE %m1%m2%` sur `titre_normalise` + relations pays/tags/categories/auteurs, filtres type/categorie/pays/auteur, tri recent/populaire/recommande) — patron `ProfilSearchService`
- [X] T035 [US3] Composant `app/Livewire/Media/GrilleMedias.php` (props `#[Url]` `q/type/categorie/pays/auteur/tri/mode/perPage`, `updating()`→`resetPage()` sauf `perPage`, `setMode`, `resetFilters`, `loadMore`, `render()` via service) — patron `ProfilGrid` — dépend de T034
- [X] T036 [US3] Vue `resources/views/livewire/media/grille-medias.blade.php` (barre de filtres `wire:model.live.debounce`, grille/liste, bouton « Charger plus » + repli `links()`, état vide, aria)
- [X] T037 [US3] `MediaController@index` + route `media.index` (`/media/explorer`) + vue `resources/views/media/index.blade.php` hébergeant `<livewire:media.grille-medias />`
- [X] T038 [US3] Commande `app/Console/Commands/MediaRecomputePopularity.php` (`media:recompute-popularity` : `popularity_score` = vues pondérées par fraîcheur) + planification `->hourly()` dans `app/Console/Kernel.php`

**Checkpoint**: US1 + US2 + US3 indépendamment fonctionnelles.

---

## Phase 6: User Story 4 — Écouter les podcasts et regarder les vidéos (Priority: P2)

**Goal**: Lecteur audio (Plyr) avec durée/sous-titres/téléchargement ; vidéo embarquée YouTube/Vimeo responsive ; séries/playlists (saison/épisode) avec navigation entre épisodes.

**Independent Test**: Ouvrir un podcast (lecture, durée, sous-titres, téléchargement si activé), une vidéo (embed responsive), parcourir une série multi-épisodes ordonnée.

### Tests (US4)

- [X] T039 [P] [US4] Feature test `tests/Feature/Media/MediaPlayersTest.php` (markup lecteur audio + bouton téléchargement conditionnel + `<track>` ; iframe embed ; page série ordonnée saison/épisode)
- [X] T040 [P] [US4] Test unitaire `tests/Unit/Media/VideoEmbedTest.php` (parsing YouTube `watch?v=`, `youtu.be`, Vimeo)

### Implementation (US4)

- [X] T041 [US4] `resources/js/media.js` — `import Plyr`, initialisation des lecteurs `<audio>` et des embeds, activation des captions
- [X] T042 [P] [US4] `resources/css/media.css` — styles lecteur + conteneur d'embed responsive 16:9
- [X] T043 [US4] Étendre `resources/views/media/show.blade.php` : lecteur audio (podcast), iframe vidéo (`Media::embedHtml`), `<track kind="captions">` (sous-titres), bouton téléchargement si `audio_downloadable`
- [X] T044 [US4] `MediaController@serie` + route `media.serie` (`/media/series/{slug}`) + vue `resources/views/media/serie.blade.php` (playlist ordonnée, saison/épisode)
- [X] T045 [US4] Navigation préc/suiv prioritairement par série (saison/épisode) dans `MediaController@show` + affichage de la playlist de la série sur la page détail

**Checkpoint**: Podcasts, vidéos et séries opérationnels.

---

## Phase 7: User Story 5 — Newsletter / abonnement (Priority: P3)

**Goal**: Abonnement opt-in par email (confirmation + désabonnement via URL signée) et alertes à la publication.

**Independent Test**: S'abonner → confirmer (URL signée) → publier un média → alerte envoyée → se désabonner ; pas de doublon ; honeypot rejette les bots.

### Tests (US5)

- [X] T046 [P] [US5] Feature test `tests/Feature/Media/NewsletterTest.php` (`Notification::fake` ; subscribe→confirm signé→notify à la publication→unsubscribe ; anti-doublon ; honeypot ; signature invalide=403)

### Implementation (US5)

- [X] T047 [US5] Migration `create_newsletter_subscribers_table` (`email` unique, `token` unique, `status`, `confirmed_at`) dans `database/migrations/`
- [X] T048 [P] [US5] Modèle `app/Models/NewsletterSubscriber.php` + `database/factories/NewsletterSubscriberFactory.php`
- [X] T049 [US5] `app/Http/Controllers/NewsletterController.php` (`subscribe`, `confirm` [signed], `unsubscribe` [signed]) + routes `media.newsletter.*` dans `routes/web.php`
- [X] T050 [P] [US5] Notifications `app/Notifications/NewsletterConfirmationNotification.php` (URL signée) et `NewMediaPublishedNotification.php` (canal `mail`, `Queueable`) — patron `ProfilPubliePersonneNotification`
- [X] T051 [US5] Brancher `MediaObserver::saved` : à la transition `→published`, dispatcher `NewMediaPublishedNotification` aux abonnés `confirmed` via `Notification::route('mail', $email)` (idempotent) — dépend de T012, T050
- [X] T052 [US5] Composant/section formulaire d'abonnement (honeypot + throttle `media.newsletter.subscribe`) + CTA « S'abonner » sur `media/home` et `media/show`
- [X] T053 [US5] `app/Filament/Resources/NewsletterSubscriberResource.php` (lecture, filtre `status`, export CSV) + Pages

**Checkpoint**: Newsletter opt-in et alertes fonctionnelles.

---

## Phase 8: User Story 6 — Commenter avec modération (Priority: P3)

**Goal**: Soumission de commentaires (masqués jusqu'à modération) et affichage des commentaires approuvés ; modération en back-office.

**Independent Test**: Soumettre un commentaire → masqué ; l'approuver en admin → visible ; le rejeter → masqué ; honeypot rejette les bots.

### Tests (US6)

- [X] T054 [P] [US6] Feature test `tests/Feature/Media/CommentsTest.php` (soumission `pending` non visible, `approve`→visible, `reject`→masqué, honeypot, throttle)

### Implementation (US6)

- [X] T055 [US6] Migration `create_media_comments_table` (`media_id`/`parent_id`, `author_name`/`author_email`, `body`, `status` index, `ip_address`) dans `database/migrations/`
- [X] T056 [P] [US6] Modèle `app/Models/MediaComment.php` + `database/factories/MediaCommentFactory.php` (relations déjà déclarées sur `Media` en T010)
- [X] T057 [US6] `MediaController@storeComment` + route `media.comments.store` (validation + honeypot + throttle, état initial `pending`)
- [X] T058 [US6] Formulaire de commentaire + liste des commentaires `approved` sur `resources/views/media/show.blade.php`
- [X] T059 [US6] `app/Filament/Resources/MediaResource/RelationManagers/CommentairesRelationManager.php` (actions `approve`/`reject` conditionnelles) + l'enregistrer dans `MediaResource::getRelations()`
- [X] T060 [US6] `app/Filament/Resources/MediaCommentResource.php` (file de modération globale, filtre `status`, badge de navigation = nb `pending`) + Pages

**Checkpoint**: Toutes les user stories indépendamment fonctionnelles.

---

## Phase 9: Polish & Cross-Cutting Concerns

**Purpose**: Améliorations transverses.

- [X] T061 [P] Scroll infini (IntersectionObserver) en complément de « Charger plus » dans `resources/js/media.js`
- [X] T062 [P] Passe d'accessibilité (navigation clavier des lecteurs, `aria-*`, captions) sur `resources/views/media/*` et `layouts/media.blade.php`
- [X] T063 [P] Revue performance : confirmer l'eager-loading (`with([...])`) et l'usage des index (absence de N+1) sur home/explorer/show
- [X] T064 [P] Visuels de repli (cover/og par défaut) et états vides homogènes
- [X] T065 Exécuter `vendor/bin/pint` sur les fichiers créés/modifiés
- [X] T066 Exécuter la checklist de `quickstart.md` + la suite `vendor/bin/phpunit --filter=Media`
- [X] T067 [P] Compléter les notes domaine « Média » dans `CLAUDE.md` (cluster de modèles)

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)** : aucune dépendance.
- **Foundational (Phase 2)** : dépend de Setup — **BLOQUE toutes les user stories**.
- **User Stories (Phases 3-8)** : dépendent de Foundational ; ensuite parallélisables, ou séquentielles par priorité (P1→P2→P3).
- **Polish (Phase 9)** : dépend des user stories ciblées.

### User Story Dependencies

- **US1 (P1)** : après Foundational. Indépendante.
- **US2 (P1)** : après Foundational. Indépendante (a besoin de contenus publiés — fournis par le `MediaSeeder` ou par US1).
- **US3 (P2)** : après Foundational. Réutilise le layout/cartes de US2 mais testable seule.
- **US4 (P2)** : après Foundational ; étend la vue `media/show` de US2 (T043/T045 supposent T030).
- **US5 (P3)** : après Foundational ; T051 dépend de l'observer (T012) ; CTA s'appuie sur les vues US2.
- **US6 (P3)** : après Foundational ; T058 s'appuie sur la vue `media/show` (US2) ; T059 sur `MediaResource` (US1).

### Within Each User Story

- Tests d'abord (doivent échouer avant implémentation), puis Modèles → Services → Contrôleurs/Composants → Vues → Intégration.

### Parallel Opportunities

- Setup : T002, T003 en parallèle.
- Foundational : migrations T005–T008 + helper T009 en parallèle ; puis T010 ; puis T011/T013/T015 en parallèle.
- Une fois Foundational fini : US1, US2, US3, US4, US5, US6 peuvent être menées en parallèle par plusieurs développeurs (en gardant à l'esprit les couplages de vues notés ci-dessus).
- Les tests `[P]` d'une même story sont parallélisables.

---

## Parallel Example: User Story 1

```bash
# Test (US1) — peut être écrit en parallèle de la préparation :
Task: "Feature test MediaResource dans tests/Feature/Media/MediaAdminTest.php"

# Pages Filament en parallèle de la table (fichiers distincts) :
Task: "Pages ListMedia/CreateMedia/EditMedia dans app/Filament/Resources/MediaResource/Pages/"
```

## Parallel Example: Foundational

```bash
# Migrations indépendantes (fichiers distincts) :
Task: "Migration create_media_series_table"
Task: "Migration create_category_media_table"
Task: "Migration create_media_tag_table"
Task: "Migration create_media_authors_table"
Task: "Helper VideoEmbed dans app/Helper/VideoEmbed.php"
```

---

## Implementation Strategy

### MVP First (US1 + US2 — les deux P1)

1. Phase 1 (Setup) → Phase 2 (Foundational).
2. Phase 3 (US1) : produire du contenu en admin.
3. Phase 4 (US2) : exposer le contenu sur `/media`.
4. **STOP & VALIDATE** : `/media` consultable, contenus créés en admin, `/` et `/actualites` intacts.
5. Démo / déploiement possible.

### Incremental Delivery

1. Setup + Foundational → socle prêt.
2. + US1 + US2 → MVP (création + consultation).
3. + US3 → recherche/filtres/tri.
4. + US4 → podcasts/vidéos/séries.
5. + US5 → newsletter ; + US6 → commentaires.
6. Phase 9 → polish (scroll infini, a11y, perf, style, validation).

### Parallel Team Strategy

Après Foundational : Dev A → US1, Dev B → US2, Dev C → US3, puis répartition US4/US5/US6, en respectant les couplages de vues (US4/US6 étendent `media/show` livré par US2).

---

## Notes

- **67 tâches** : Setup (3), Foundational (12), US1 (8), US2 (8), US3 (7), US4 (7), US5 (8), US6 (6), Polish (7).
- `[P]` = fichiers différents, sans dépendance incomplète. `[US#]` = traçabilité vers la user story.
- Toutes les migrations sont **additives** ; aucune table existante n'est modifiée.
- Vérifier que les tests échouent avant d'implémenter ; committer après chaque tâche ou groupe logique.
- Ops (prod) : cron `schedule:run` (T023/T038) et driver de queue `database`/`redis` + table `jobs` pour les notifications (cf. quickstart.md).
