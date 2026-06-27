# Tasks: Section Projets (vitrine)

**Input**: Design documents from `specs/003-projets-vitrine/`
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/, quickstart.md

**Tests**: INCLUS — le plan prévoit explicitement `tests/Feature/Projets/*` et `tests/Unit/Projets/*` en miroir des features 001/002.

**Organization**: Tâches groupées par user story (toutes **P1**, mais interdépendantes via la fondation). Chemins relatifs à la racine du dépôt.

## Format: `[ID] [P?] [Story] Description`

- **[P]** : parallélisable (fichiers distincts, aucune dépendance sur une tâche incomplète).
- **[Story]** : US1 (listing), US2 (détail), US3 (back-office). Setup/Foundational/Polish : pas de label.

## Path Conventions

Monolithe Laravel : `app/`, `database/`, `resources/`, `config/`, `routes/`, `tests/` à la racine (cf. plan.md → Structure Decision).

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Initialisation de la configuration et des points d'extension additifs.

- [X] T001 Créer `config/projets.php` (`per_page`, `per_page_step`, `a_la_une`/featured count) calqué sur `config/media.php`
- [X] T002 [P] Ajouter les entrées Vite `resources/css/projets.css` et `resources/js/projets.js` dans `vite.config.js` (tableau `input`)
- [X] T003 [P] Créer les fichiers d'assets vides `resources/css/projets.css` et `resources/js/projets.js` (squelette + commentaire d'en-tête)
- [X] T004 [P] Préparer les dossiers de stockage disque `public` : `projets/cards`, `projets/principal`, `projets/galerie`, `projets/partenaires`, `projets/temoignages` (gitignore/.gitkeep cohérent avec l'existant média)

**Checkpoint**: Config, assets et stockage prêts.

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Schéma de données, modèles, observer, service de recherche, seed — **bloquant pour toutes les user stories**. Permet de démontrer US1/US2 sans US3 grâce au seeder.

**⚠️ CRITICAL**: Aucune user story ne peut démarrer avant la fin de cette phase.

### Migrations (additives — 7 tables, cf. data-model.md)

- [X] T005 Migration `create_projets_table` : colonnes de `data-model.md §1` (titre, slug, titre_normalise, resume, visuels, contexte/objectifs/description/activites, `statut` enum, `is_published`, `published_at`, `pays_id` integer, `portee` enum, `zone_libelle`, `featured`, `position`, meta SEO, timestamps + softDeletes) + index (`slug` unique, `titre_normalise`, `is_published`, `pays_id`, `position`) dans `database/migrations/`
- [X] T006 [P] Migration `create_category_projet_table` (pivot thématiques) : `projet_id` FK cascade, `category_id` integer (sans contrainte DB), `position`, unique `(projet_id, category_id)`
- [X] T007 [P] Migration `create_partenaires_table` : `nom`, `slug` nullable unique, `logo`, `url`, timestamps
- [X] T008 [P] Migration `create_partenaire_projet_table` (pivot) : `projet_id` FK cascade, `partenaire_id` FK cascade, `position`, unique `(projet_id, partenaire_id)`
- [X] T009 [P] Migration `create_projet_resultats_table` : `projet_id` FK cascade, `libelle`, `valeur`, `unite`, `icone`, `position`
- [X] T010 [P] Migration `create_projet_medias_table` : `projet_id` FK cascade, `type` enum(image,video), `chemin`, `url_embed`, `legende`, `position`
- [X] T011 [P] Migration `create_projet_temoignages_table` : `projet_id` FK cascade, `auteur`, `fonction`, `organisation`, `contenu`, `photo`, `position`

### Modèles (cf. data-model.md)

- [X] T012 Créer `app/Models/Projet.php` : `use Sluggable, SoftDeletes, HasFactory` ; `$casts` (booleans, datetime, integers) ; constantes `$statuts`/`$portees` ; relations `categories()`/`pays()`/`resultats()`/`medias()`/`temoignages()`/`partenaires()` ; scopes `published()`/`byStatut()`/`byThematique()`/`byZone()`/`ordered()` ; accessors `visuel_card_url`/`visuel_principal_url`/`statut_label`
- [X] T013 [P] Créer `app/Models/ProjetResultat.php` (BelongsTo `Projet`, `$fillable`, ordre par `position`)
- [X] T014 [P] Créer `app/Models/ProjetMedia.php` (BelongsTo `Projet` ; accessor `embed_html` via `App\Helper\VideoEmbed` si `type=video`)
- [X] T015 [P] Créer `app/Models/ProjetTemoignage.php` (BelongsTo `Projet`)
- [X] T016 [P] Créer `app/Models/Partenaire.php` (BelongsToMany `Projet`, `$fillable`, accessor `logo_url`)
- [X] T017 Créer `app/Observers/ProjetObserver.php` (génère `slug` via trait, remplit `titre_normalise` via `App\Services\Annuaire\TextNormalizer`) et l'enregistrer dans `app/Providers/AppServiceProvider.php` (ou EventServiceProvider, selon convention 002)

### Service de recherche, factories & seed

- [X] T018 Créer `app/Services/Projets/ProjetSearchService.php` calqué sur `app/Services/Media/MediaSearchService.php` : applique `published()` + filtres `thematique`/`zone`/`statut` + recherche `titre_normalise` + tri `recent|alpha` + eager-load (`categories`,`pays`) + pagination
- [X] T019 [P] Créer factories `database/factories/{ProjetFactory,ProjetResultatFactory,ProjetMediaFactory,ProjetTemoignageFactory,PartenaireFactory}.php`
- [X] T020 Créer `database/seeders/ProjetSeeder.php` idempotent : garantit des catégories `type='projet'` (résolution manuelle de l'id, cf. `MediaSeeder`), crée ≥1 projet par statut avec galerie/partenaires/chiffres/témoignages ; l'enregistrer dans `DatabaseSeeder`

### Tests fondation (Unit)

- [X] T021 [P] Test `tests/Unit/Projets/ProjetModelTest.php` : slug auto, `titre_normalise` rempli, casts, scopes `published`/`byStatut`/`ordered`
- [X] T022 [P] Test `tests/Unit/Projets/ProjetSearchServiceTest.php` : filtres thématique/zone/statut, recherche accent-insensible, tri

**Checkpoint**: Données + modèles + recherche + seed prêts → les user stories peuvent démarrer.

---

## Phase 3: User Story 1 - Parcourir et découvrir les projets (Priority: P1) 🎯 MVP

**Goal**: Page `/projets` listant les projets publiés en cards filtrables, avec bouton « Découvrir ».

**Independent Test**: Seeder appliqué → `/projets` affiche une card par projet publié (nom, visuel, résumé, thématique, zone, badge statut) ; les filtres réduisent la liste ; « Découvrir » mène au détail ; brouillons exclus.

- [X] T023 [US1] Ajouter la route `GET /projets` → `ProjetController@index` (nom `projets.index`) dans `routes/web.php` ; créer `app/Http/Controllers/ProjetController.php` avec `index()`
- [X] T024 [US1] Créer le composant Livewire `app/Livewire/Projets/GrilleProjets.php` calqué sur `app/Livewire/Media/GrilleMedias.php` (props `#[Url]` `q`/`thematique`/`zone`/`statut`/`tri`/`perPage`, `chargerPlus()`, `render()` via `ProjetSearchService`) — cf. `contracts/livewire-grille-projets.md`
- [X] T025 [P] [US1] Créer la vue `resources/views/livewire/projets/grille-projets.blade.php` (barre de filtres thématique/zone/statut + tri, grille de cards, bouton « Charger plus », **état vide** explicite avec « Réinitialiser les filtres »)
- [X] T026 [P] [US1] Créer le composant `resources/views/components/projet-card.blade.php` (visuel, titre, résumé, badges thématique/zone, badge `statut_label` coloré, bouton « Découvrir » → `projets.show`), calqué sur `media-card`
- [X] T027 [US1] Créer la vue `resources/views/projets/index.blade.php` (extends `layouts/front`, charge `@vite(['resources/css/projets.css','resources/js/projets.js'])`, monte `<livewire:projets.grille-projets />`)
- [X] T028 [P] [US1] Styles listing/cards dans `resources/css/projets.css` (grille responsive, badges de statut, état vide)
- [X] T029 [US1] Ajouter l'entrée de navigation « Projets » (→ `projets.index`) dans `resources/views/layouts/front.blade.php`
- [X] T030 [P] [US1] Test `tests/Feature/Projets/ListingTest.php` : card par projet publié, brouillon exclu (SC-002), filtres thématique/zone/statut (SC-004), `chargerPlus`, état vide (FR-007)

**Checkpoint**: US1 livrable et testable indépendamment (avec données du seeder).

---

## Phase 4: User Story 2 - Consulter le détail d'un projet (Priority: P1)

**Goal**: Page détaillée par projet avec toutes les sections, galerie photos/vidéos, et bouton « Nous contacter ».

**Independent Test**: Ouvrir `/projets/{slug}` d'un projet publié → toutes les sections renseignées s'affichent dans l'ordre ; sections optionnelles vides masquées ; galerie fonctionnelle ; « Nous contacter » → `/contact?projet={slug}` ; slug inconnu/brouillon → 404.

- [X] T031 [US2] Ajouter la route `GET /projets/{projet:slug}` → `ProjetController@show` (nom `projets.show`) dans `routes/web.php` ; implémenter `show()` (résolution restreinte aux publiés sinon 404, eager-load `categories`,`pays`,`resultats`,`medias`,`partenaires`,`temoignages`) — cf. `contracts/public-routes.md`
- [X] T032 [US2] Créer la vue `resources/views/projets/show.blade.php` (extends `layouts/front`) : titre + visuel principal → contexte → objectifs → description → activités → chiffres clés → partenaires → galerie → témoignages → bouton « Nous contacter », avec **masquage conditionnel** de chaque section vide (FR-012)
- [X] T033 [P] [US2] Bloc « chiffres clés » mis en valeur (libellé/valeur/unité, FR-013) + bloc partenaires (logos/liens) dans `show.blade.php`
- [X] T034 [US2] Galerie photos/vidéos dans `show.blade.php` : images `<img>` + vidéos via `App\Helper\VideoEmbed` (embed) ou `<video>` natif, tolérante aux médias indisponibles (FR-014, edge case)
- [X] T035 [P] [US2] Implémenter le lightbox de galerie + init embed dans `resources/js/projets.js` (ouverture/fermeture clavier + aria)
- [X] T036 [US2] Bouton « Nous contacter » → `route('contact', ['projet' => $projet->slug])` (`/contact?projet={slug}`, R10) dans `show.blade.php`
- [X] T036b [US2] Consommer le paramètre `projet` côté contact : pré-remplir l'objet/référence du projet dans `app/Livewire/Contactform.php` (lecture de la query `projet` au `mount()`, pré-remplissage non destructif) pour que la prise de contact soit explicitement « en lien avec le projet » (US2-3/FR-015). Modification additive et rétro-compatible — le contact sans paramètre reste inchangé (non-régression)
- [X] T037 [P] [US2] Méta SEO (`meta_titre`/`meta_description` avec fallback `titre`/`resume`) + URL canonique dans `show.blade.php`
- [X] T038 [P] [US2] Test `tests/Feature/Projets/DetailTest.php` : affichage des sections (US2-1), témoignages absents → section masquée (SC-006), galerie images+vidéos (US2-4), bouton contact (SC-007), brouillon/slug inconnu → 404 (FR-016)

**Checkpoint**: US1 + US2 = vitrine publique complète, navigable de bout en bout.

---

## Phase 5: User Story 3 - Gérer les projets depuis le back-office (Priority: P1)

**Goal**: CRUD complet des projets dans Filament (statuts, publication, uploads, partenaires, chiffres clés, témoignages, galerie).

**Independent Test**: Se connecter au back-office, créer un projet complet, le publier → visible côté public ; modifier le statut/contenu → reflété ; supprimer → disparaît + `show` 404 ; brouillon → absent du public.

- [X] T039 [US3] Créer `app/Filament/Resources/ProjetResource.php` (+ `Pages/{List,Create,Edit}`) : formulaire en sections (identité/card, contenu détaillé, classification thématiques/zone, statut & publication, SEO), table (titre, badge statut, publication, thématiques, zone, `published_at`, `position`), filtres table, actions groupées publier/dépublier — cf. `contracts/admin-filament.md`
- [X] T040 [P] [US3] `app/Filament/Resources/ProjetResource/RelationManagers/ResultatsRelationManager.php` (chiffres clés : libelle/valeur/unite/icone/position, réordonnable)
- [X] T041 [P] [US3] `app/Filament/Resources/ProjetResource/RelationManagers/GalerieMediasRelationManager.php` (type image/video, upload `chemin` ou `url_embed`, légende, position)
- [X] T042 [P] [US3] `app/Filament/Resources/ProjetResource/RelationManagers/TemoignagesRelationManager.php` (auteur/fonction/organisation/contenu/photo/position)
- [X] T043 [P] [US3] `app/Filament/Resources/ProjetResource/RelationManagers/PartenairesRelationManager.php` (attache des `partenaires` existants + position)
- [X] T044 [P] [US3] Créer `app/Filament/Resources/PartenaireResource.php` (+ `Pages/`) : CRUD master `nom`/`logo`/`url`
- [X] T045 [P] [US3] Test `tests/Feature/Projets/AdminProjetResourceTest.php` : création projet complet publié → visible public (SC-003, US3-1) ; modif statut reflétée (US3-2) ; suppression → 404 (US3-3) ; brouillon absent (US3-5) ; upload médias associés (US3-4) ; **autorisation** : un utilisateur non-administrateur ne peut accéder ni à la liste ni au formulaire `ProjetResource` (403/redirect) — couvre FR-024

**Checkpoint**: Les trois user stories P1 sont livrées — boucle de contenu complète (créer → publier → consulter).

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Robustesse, accessibilité, performance, non-régression, style.

- [X] T046 [P] Visuel placeholder par défaut (card & détail) quand `visuel_*` est null (edge case image manquante) dans les accessors/vues
- [X] T047 [P] Troncature propre du `resume` en card (edge case description trop longue) dans `projet-card.blade.php`
- [X] T048 [P] Accessibilité : navigation clavier + aria sur filtres (`grille-projets`) et galerie/lightbox (`projets.js`)
- [X] T049 [P] Revue performance : vérifier l'eager-loading (anti N+1) sur listing et détail, index DB utilisés (SC-001/SC-005)
- [X] T050 Test `tests/Feature/Projets/NonRegressionTest.php` : `/`, `/actualites`, `/media`, annuaire répondent 200 et inchangés
- [X] T051 [P] Exécuter `vendor/bin/pint` et corriger le style ; vérifier `npm run build`
- [X] T052 Vérifier le parcours `quickstart.md` de bout en bout (US1/US2/US3 + non-régression)

---

## Dependencies & Execution Order

### Dépendances de phase

- **Setup (Ph1)** : aucune dépendance — démarrage immédiat.
- **Foundational (Ph2)** : dépend de Setup — **BLOQUE toutes les user stories**.
- **US1 (Ph3)** : dépend de Foundational. MVP recommandé (avec seeder).
- **US2 (Ph4)** : dépend de Foundational ; partage `ProjetController` et `resources/js/projets.js` avec US1 (coordonner T023/T031 et T035 si menées en parallèle).
- **US3 (Ph5)** : dépend de Foundational uniquement → **peut être menée en parallèle de US1/US2** (fichiers Filament distincts).
- **Polish (Ph6)** : dépend de l'achèvement des user stories visées.

### Dépendances intra-fondation

- T005 (table `projets`) avant T006–T011 (FK vers `projets`).
- T012 (`Projet`) avant T017 (observer), T018 (service), T020 (seeder).
- T013–T016 (modèles liés) avant T020 (seeder) et les RelationManagers (Ph5).

### Note MVP

Bien que les trois stories soient **P1**, le plus petit incrément démontrable est **Setup + Foundational + US1** (le `ProjetSeeder` fournit les données, sans dépendre de US3). US2 complète la vitrine ; US3 ouvre l'autonomie éditoriale.

---

## Parallel Execution Examples

- **Migrations** : T006, T007, T008, T009, T010, T011 en parallèle après T005.
- **Modèles liés** : T013, T014, T015, T016 en parallèle après T012.
- **Tests fondation** : T021, T022 en parallèle.
- **US1** : T025, T026, T028, T030 en parallèle (vues/styles/test) une fois T023/T024 posés.
- **US3** : T040, T041, T042, T043, T044, T045 en parallèle après T039.
- **Inter-stories** : toute la Phase 5 (US3) peut tourner en parallèle des Phases 3–4 (US1/US2) — fichiers disjoints.
- **Polish** : T046, T047, T048, T049, T051 en parallèle.

---

## Implementation Strategy

1. **Fondation d'abord** : Ph1 + Ph2 (un seul flux, séquentiel sur T005/T012/T017/T018/T020).
2. **MVP** : ajouter US1 (Ph3) → démontrer le listing public avec données seedées.
3. **Vitrine complète** : ajouter US2 (Ph4).
4. **Autonomie éditoriale** : US3 (Ph5), parallélisable avec 2–3.
5. **Durcissement** : Ph6 (placeholders, accessibilité, perf, non-régression, `pint`).

**Total : 53 tâches** — US1 : 8, US2 : 9, US3 : 7, Setup : 4, Foundational : 18, Polish : 7.
