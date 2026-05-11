---

description: "Task list for feature 001-annuaire-evolution"
---

# Tasks : Évolution de l'annuaire

**Input** : Design documents from `/specs/001-annuaire-evolution/`
**Prerequisites** : plan.md ✅, spec.md ✅, research.md ✅, data-model.md ✅, contracts/ ✅

**Tests** : Inclus (la règle globale TDD du projet impose tests-first avec ≥ 80 % de couverture).

**Organisation** : Tâches groupées par user story pour permettre une implémentation et un test indépendants de chaque story.

## Format : `[ID] [P?] [Story] Description`

- **[P]** : peut s'exécuter en parallèle (fichiers différents, pas de dépendances).
- **[Story]** : à quelle user story la tâche appartient (US1, US2, …).
- Chemins de fichiers absolus aux conventions Laravel/Filament du projet.

## Path Conventions

Application Laravel monolithique :
- Modèles : `app/Models/`
- Services : `app/Services/Annuaire/`
- Contrôleurs : `app/Http/Controllers/`
- Filament : `app/Filament/Resources/`
- Livewire : `app/Livewire/Annuaire/`
- Migrations : `database/migrations/`
- Tests : `tests/Feature/Annuaire/`, `tests/Unit/Annuaire/`

---

## Phase 1 : Setup (Shared Infrastructure)

**Purpose** : Initialisation du chantier, dépendances et conventions.

- [X] T001 Ajouter la dépendance `maatwebsite/excel:^3.1` via `composer require` et publier sa config dans `config/excel.php`
- [X] T002 [P] Créer les répertoires squelettes `app/Services/Annuaire/`, `app/Livewire/Annuaire/`, `app/Filament/Resources/ProfilResource/`, `app/Imports/`, `app/Exports/`, `resources/views/annuaire/`, `tests/Feature/Annuaire/`, `tests/Unit/Annuaire/`
- [X] T003 [P] Ajouter les variables d'environnement dans `.env.example` : `ANNUAIRE_PER_PAGE=24`, `ANNUAIRE_CONSENTEMENT_EXPIRY_DAYS=90`, `ANNUAIRE_RATE_LIMIT_PUBLIC=60`
- [X] T004 [P] Créer `config/annuaire.php` pour exposer ces variables (`per_page`, `consentement_expiry_days`, `rate_limit_public`)

---

## Phase 2 : Foundational (Blocking Prerequisites)

**Purpose** : Schéma de base, modèles et services bas-niveau dont toutes les user stories dépendent.

**⚠️ CRITIQUE** : aucune user story ne peut commencer tant que cette phase n'est pas terminée.

### Migrations (base de données)

- [X] T005 Créer la migration `database/migrations/2026_05_10_000001_extend_profils_for_annuaire.php` ajoutant les colonnes : `nationalite`, `bio_courte`, `bio_longue`, `ville`, `organisation`, `type_profil` (enum), `etat_publication` (enum), `masquer_email`, `masquer_tel`, `nom_normalise`, `prenom_normalise`, `organisation_normalisee`, `ville_normalisee`, `legacy_sans_consentement`, `published_at`, `deleted_at` (SoftDeletes), + index décrits dans data-model.md
- [X] T006 [P] Créer la migration `database/migrations/2026_05_10_000002_create_domaines_expertise_table.php`
- [X] T007 [P] Créer la migration `database/migrations/2026_05_10_000003_create_profil_domaine_expertise_pivot.php`
- [X] T008 [P] Créer la migration `database/migrations/2026_05_10_000004_create_tags_table.php`
- [X] T009 [P] Créer la migration `database/migrations/2026_05_10_000005_create_profil_tag_pivot.php`
- [X] T010 [P] Créer la migration `database/migrations/2026_05_10_000006_create_liens_externes_table.php`
- [X] T011 [P] Créer la migration `database/migrations/2026_05_10_000007_create_historique_profils_table.php`
- [X] T012 [P] Créer la migration `database/migrations/2026_05_10_000008_create_demandes_moderation_table.php`
- [X] T013 [P] Créer la migration `database/migrations/2026_05_10_000009_create_consentements_profils_table.php`

### Service utilitaire de normalisation (testé en TDD)

- [X] T014 [P] Écrire test unitaire `tests/Unit/Annuaire/TextNormalizerTest.php` couvrant : suppression accents (Côte d'Ivoire → cote d ivoire), casse, ponctuation, chaînes vides/null, caractères spéciaux (œ, ñ, ü)
- [X] T015 Implémenter `app/Services/Annuaire/TextNormalizer.php` (méthode statique `normalize(?string $value): string`) jusqu'à ce que T014 passe

### Modèles Eloquent (foundation)

- [X] T016 [P] Créer `app/Models/DomaineExpertise.php` (fillable, relation `profils` belongsToMany, mutateur `slug`)
- [X] T017 [P] Créer `app/Models/Tag.php` (fillable, relation `profils` belongsToMany, mutateur `slug`)
- [X] T018 [P] Créer `app/Models/LienExterne.php` (fillable, relation `profil` belongsTo, casts `type`)
- [X] T019 [P] Créer `app/Models/HistoriqueProfil.php` (fillable, casts `diff:array`, relations `profil`/`user`, immutable côté code)
- [X] T020 [P] Créer `app/Models/DemandeModeration.php` (fillable, relations, casts enum `decision`)
- [X] T021 [P] Créer `app/Models/ConsentementProfil.php` (fillable, casts dates, relation `profil` belongsTo, relation `attesteur` belongsTo User, generator `regenererJetonRetrait()`)
- [X] T022 Étendre `app/Models/Profil.php` : ajouter `fillable` des nouveaux champs, `casts` (booleans, enums via attributes, `published_at` date), `SoftDeletes` trait, relations `domainesExpertise()`, `tags()`, `liensExternes()`, `historiques()`, `demandesModeration()`, `consentement()` (hasOne), mutateurs pour `nom_normalise`/`prenom_normalise`/`organisation_normalisee`/`ville_normalisee` via `TextNormalizer`, scopes `publies()`, `enAttente()`, `archives()`, `parType($type)` (dépend de T015 + T022 ne peut pas être [P] avec les autres car même fichier mais T016-T021 oui)

### Observer et journalisation

- [X] T023 Créer `app/Observers/ProfilObserver.php` qui écoute `created`, `updating`, `updated`, `deleted`, `restored` et appelle `ProfilHistoriqueService::log(...)` avec le diff
- [X] T024 Enregistrer `ProfilObserver` dans `app/Providers/AppServiceProvider.php` (méthode `boot`)
- [X] T025 Implémenter `app/Services/Annuaire/ProfilHistoriqueService.php` (méthodes `log(Profil, action, diff, motif?)`, `historiquePourProfil(Profil)`, capture user, IP et user agent depuis la request si dispo)

### Policy d'accès

- [X] T026 Créer `app/Policies/ProfilPolicy.php` avec abilities `viewAny`, `view`, `create`, `update`, `delete`, `restore`, `forceDelete`, `approve`, `reject`, `archive`, `import`, `export` selon la matrice de data-model.md
- [X] T027 Enregistrer la policy dans `app/Providers/AuthServiceProvider.php`

### Seeder de migration des données legacy

- [X] T028 Créer `database/seeders/AnnuaireMigrationSeeder.php` qui : mappe `online`/`aprouve` → `etat_publication`, copie `bio` → `bio_longue`, parse `domaine` (string) en relations `domaines_expertise`, transforme `facebook/twitter/youtube/linkding/site` en lignes `liens_externes`, marque `legacy_sans_consentement=true`, journalise `action=migration_initiale` dans `historique_profils`. Idempotent.
- [X] T029 Ajouter une commande Artisan `app/Console/Commands/AnnuaireRenormaliser.php` (`annuaire:renormaliser`) qui recalcule les colonnes `*_normalise` pour tous les profils (mentionnée dans quickstart.md)

**Checkpoint** : Foundation prête — `php artisan migrate && php artisan db:seed --class=AnnuaireMigrationSeeder` réussit, modèles instanciables, observer journalise, tests T014 verts.

---

## Phase 3 : User Story 1 - Consulter et rechercher des profils dans l'annuaire public (Priorité : P1) 🎯 MVP

**Goal** : Page publique `/annuaire` listant les profils publiés, avec barre de recherche en texte libre et fiche détail accessible.

**Independent Test** : Avec un jeu de profils publiés, un visiteur ouvre `/annuaire`, voit la grille paginée, tape « Sénégal » dans la barre de recherche et ne voit que les profils correspondants. Il clique un profil, voit sa fiche, et les coordonnées masquées sont absentes du HTML rendu.

### Tests pour User Story 1 (TDD — écrire AVANT l'implémentation, vérifier qu'ils échouent)

- [X] T030 [P] [US1] Écrire `tests/Feature/Annuaire/AnnuaireListingTest.php` : visiteur non authentifié peut voir la page `/annuaire` (200), seuls les profils `etat_publication=publié` y figurent, pagination par défaut = 24
- [X] T031 [P] [US1] Écrire `tests/Feature/Annuaire/AnnuaireSearchTest.php` (volet recherche libre) : recherche `q=senegal` retourne profils dont le nom/prénom/organisation/ville/domaine contient « Sénégal », insensible à la casse et aux accents
- [X] T032 [P] [US1] Écrire `tests/Feature/Annuaire/ProfilDetailTest.php` : route `/annuaire/{slug}` retourne 200 pour publié, 404 pour `en_attente`/`archivé` visiteur ; les coordonnées masquées (`masquer_email=true`) **n'apparaissent jamais** dans le HTML rendu ni dans le JSON API (SC-005)
- [X] T033 [P] [US1] Écrire `tests/Feature/Annuaire/ApiAnnuaireTest.php` : `GET /api/annuaire` répond enveloppe `{success, data, meta}`, omet `email`/`tel` si masqués, applique rate-limit (60/min) selon contracts/api-annuaire.md

### Implémentation pour User Story 1

- [X] T034 [P] [US1] Implémenter `app/Services/Annuaire/ProfilSearchService.php` (méthode `recherche(array $criteres): Builder` — gère `q` sur les colonnes normalisées via `LIKE %term_normalise%`)
- [X] T035 [P] [US1] Créer `app/Http/Resources/ProfilPublicResource.php` qui omet `email` si `masquer_email=true`, `tel` si `masquer_tel=true`, et n'inclut jamais `etat_publication`, `legacy_sans_consentement`, jetons (SC-005)
- [X] T036 [US1] Créer le composant `app/Livewire/Annuaire/ProfilGrid.php` (propriétés `q`, `mode`, `page`, `perPage` ; méthode `render()` consomme `ProfilSearchService`) — dépend de T034
- [X] T037 [P] [US1] Créer la vue `resources/views/livewire/annuaire/profil-grid.blade.php` : barre de recherche, bascule grille/liste, cartes/lignes, pagination
- [X] T038 [P] [US1] Créer la vue `resources/views/livewire/annuaire/profil-carte.blade.php` (carte profil) et `profil-ligne.blade.php` (ligne compacte)
- [X] T039 [US1] Étendre `app/Http/Controllers/AnnuaireController.php` : action `index()` rend `annuaire/index.blade.php` qui monte `<livewire:annuaire.profil-grid />`, action `show($slug)` charge le profil publié et rend `annuaire/show.blade.php`
- [X] T040 [P] [US1] Créer la vue `resources/views/annuaire/index.blade.php` (layout + montage Livewire) et `resources/views/annuaire/show.blade.php` (fiche détail utilisant `ProfilPublicResource`)
- [X] T041 [P] [US1] Ajouter les routes dans `routes/web.php` : `GET /annuaire → AnnuaireController@index`, `GET /annuaire/{slug} → AnnuaireController@show`
- [X] T042 [P] [US1] Ajouter dans `routes/api.php` : `GET /api/annuaire → AnnuaireController@apiIndex` (utilise `ProfilSearchService` + `ProfilPublicResource::collection` + pagination), `GET /api/annuaire/{slug} → AnnuaireController@apiShow`, avec middleware `throttle:60,1`
- [X] T043 [US1] Implémenter les actions `apiIndex()`/`apiShow()` dans `AnnuaireController.php` (enveloppe API standard `{success, data, meta}`)
- [X] T044 [US1] Avatar par défaut : ajouter `resources/img/profils/default-avatar.svg` et accesseur `photo_url` sur `Profil` qui retourne l'avatar par défaut si `image` vide

**Checkpoint** : `/annuaire` montre les profils publiés avec recherche libre fonctionnelle ; fiche détail OK ; tests T030-T033 verts ; coordonnées masquées invisibles publiquement → SC-005 vérifié.

---

## Phase 4 : User Story 2 - Filtrer et trier les profils (Priorité : P1)

**Goal** : Combiner plusieurs filtres (pays, domaine d'expertise, type de profil, tags) et choisir un tri (alpha / récent / pertinence).

**Independent Test** : Avec ≥ 20 profils variés en base, appliquer Pays = `CI`, Type = `expert` et trier alpha → résultats filtrés correctement et triés.

### Tests pour User Story 2

- [X] T045 [P] [US2] Compléter `tests/Feature/Annuaire/AnnuaireSearchTest.php` (ou créer `AnnuaireFiltersTest.php`) : filtre `pays=CI`, filtre combiné `pays + type + domaine + tags` (ET logique), filtre vide retourne tout, réinitialisation des filtres
- [X] T046 [P] [US2] Écrire `tests/Feature/Annuaire/AnnuaireSortTest.php` : tri `alpha` (ordre nom asc), `recent` (created_at desc), `pertinence` (score recherche libre)
- [X] T047 [P] [US2] Compléter `ApiAnnuaireTest.php` avec les mêmes filtres côté API

### Implémentation pour User Story 2

- [X] T048 [US2] Étendre `app/Services/Annuaire/ProfilSearchService.php` pour accepter `pays`, `type`, `domaine[]`, `tag[]`, `tri` (méthodes privées `applyPaysFilter`, `applyTypeFilter`, `applyDomaineFilter`, `applyTagFilter`, `applyTri`)
- [X] T049 [US2] Étendre `app/Livewire/Annuaire/ProfilGrid.php` : propriétés `pays`, `type`, `domaines[]`, `tags[]`, `tri`, méthode `resetFilters()`, synchronisation URL via `#[Url]`
- [X] T050 [P] [US2] Étendre la vue Livewire `profil-grid.blade.php` : composants UI filtres (selects multi pour domaines/tags, select simple pays/type, dropdown tri)
- [X] T051 [P] [US2] Ajouter un seeder `database/seeders/DomainesExpertiseSeeder.php` avec une liste de domaines courants (communication, lobbying, presse, événementiel, relations-internationales, etc.) — sert aussi à l'UX du filtre

**Checkpoint** : Filtres et tri combinables ; tests T045-T047 verts ; SC-008 (recherche + 3 filtres + tri < 1,5 s) mesurable.

---

## Phase 5 : User Story 3 - Gérer les profils en back-office (Priorité : P1)

**Goal** : CRUD complet des profils sur le panneau Filament, avec tous les champs, validation photo, prévisualisation, consentement RGPD.

**Independent Test** : Un administrateur crée un profil complet, l'enregistre, puis le voit publié sur l'annuaire public.

### Tests pour User Story 3

- [X] T052 [P] [US3] Écrire `tests/Feature/Annuaire/BackofficeProfilCrudTest.php` : un admin authentifié peut accéder à `/admin/profils`, créer/modifier/supprimer ; un éditeur ne voit que ses propres profils ; un visiteur non authentifié reçoit 403
- [X] T053 [P] [US3] Écrire `tests/Feature/Annuaire/ProfilPhotoValidationTest.php` : photo > 2 Mo rejetée, format `.gif` rejeté, format `.jpg`/`.png`/`.webp` accepté, dimensions < 400×400 rejetées, recadrage à 800×800 appliqué
- [X] T054 [P] [US3] Écrire `tests/Feature/Annuaire/ConsentementRgpdTest.php` : (a) profil sans consentement ne peut pas être passé en `publié` (FR-026), (b) consentement attesté enregistre `consentement_atteste_par`/`atteste_le`, (c) email envoyé à la personne lors de la première publication avec lien signé valide, (d) demande de retrait via lien archive le profil et notifie les admins (FR-028, SC-010)

### Implémentation pour User Story 3

- [X] T055 [P] [US3] Créer `app/Http/Requests/ProfilStoreRequest.php` et `app/Http/Requests/ProfilUpdateRequest.php` (règles de validation FR-001 à FR-004)
- [X] T056 [US3] Créer `app/Services/Annuaire/ProfilConsentementService.php` : `attester(Profil, User)`, `regenererJeton(Profil)`, `envoyerNotificationPublication(Profil)`, `traiterDemandeRetrait(string $jeton)`
- [X] T057 [P] [US3] Créer `app/Notifications/ProfilPubliePersonneNotification.php` (email Markdown vers la personne référencée, lien signé)
- [X] T058 [P] [US3] Créer `app/Notifications/DemandeRetraitConfirmeNotification.php` (vers la personne après retrait)
- [X] T059 [US3] Créer `app/Filament/Resources/ProfilResource.php` avec form schema couvrant tous les champs (FR-001), incluant TogglButtons pour masquage, Checkbox de consentement (avec aide RGPD), repeater `liens_externes`, Select multiple `domaines_expertise`, TagsInput pour `tags`, FileUpload `image` avec règles photo
- [X] T060 [P] [US3] Créer `app/Filament/Resources/ProfilResource/Pages/ListProfils.php` (table avec filtres rapides état/type/pays, actions ligne)
- [X] T061 [P] [US3] Créer `app/Filament/Resources/ProfilResource/Pages/CreateProfil.php` et `EditProfil.php` et `ViewProfil.php` (View = prévisualisation publique, FR-021)
- [X] T062 [P] [US3] Créer `app/Filament/Resources/ProfilResource/RelationManagers/LiensExternesRelationManager.php` (gestion inline des liens)
- [X] T063 [P] [US3] Ajouter pipeline image dans `Profil` : observer `creating`/`updating` qui appelle `Intervention\Image\Facades\Image::make()->fit(800,800)` → enregistre WebP sous `storage/app/public/profils/{id}.webp`
- [X] T064 [US3] Ajouter routes `routes/web.php` : `GET /annuaire/retrait/{token} → ProfilController@retraitForm`, `POST /annuaire/retrait/{token}/confirmer → ProfilController@retraitConfirmer` (URL signée Laravel)
- [X] T065 [US3] Implémenter les actions `retraitForm` et `retraitConfirmer` dans `app/Http/Controllers/ProfilController.php` (vérifie signature, vérifie expiration `jeton_expire_le`, propose les 3 actions ; archive le profil et notifie les admins en cas de retrait)
- [X] T066 [P] [US3] Créer les vues `resources/views/annuaire/retrait/form.blade.php` et `retrait/confirme.blade.php`
- [X] T066a [US3] Créer `app/Filament/Pages/AnnuaireConsentements.php` (page Filament dédiée à l'audit) listant la table `consentements_profils` avec colonnes : profil, attesté_par, atteste_le, email envoyé le, statut jeton (valide / expiré), date retrait. Filtres par admin, par plage de dates, par profil. Visible uniquement aux `admin` (couvre **FR-029**).
- [X] T066b [P] [US3] Écrire `tests/Feature/Annuaire/ConsentementAuditTest.php` : page accessible aux admins (200), refusée aux éditeurs (403) ; filtrage par date/admin fonctionne ; comporte tous les consentements existants.
- [X] T066c [US3] Ajouter la commande Artisan `app/Console/Commands/AnnuaireEnvoyerRappelsConsentement.php` (`annuaire:envoyer-rappels-consentement`) qui renvoie l'email de consentement aux profils dont `jeton_expire_le` est dans moins de 7 jours (déplacée depuis Polish — couvre **FR-027** renouvellement). Planifier dans `app/Console/Kernel.php` (daily).
- [X] T066d [P] [US3] Écrire `tests/Feature/Annuaire/RappelConsentementCommandTest.php` : la commande sélectionne uniquement les profils proches d'expiration, envoie 1 email par profil, met à jour `jeton_expire_le`.

**Checkpoint** : Le back-office gère le cycle complet création → consentement → publication ; tests T052-T054, T066b, T066d verts ; SC-009, SC-010 et FR-027/FR-029 vérifiés.

---

## Phase 6 : User Story 4 - Modération et validation avant publication (Priorité : P2)

**Goal** : Workflow d'approbation : éditeur soumet → admin approuve/rejette avec motif → notification à l'éditeur.

**Independent Test** : Un éditeur crée un profil → statut `en_attente`, invisible publiquement ; un admin l'approuve → `publié`, visible.

### Tests pour User Story 4

- [X] T067 [P] [US4] Écrire `tests/Feature/Annuaire/ModerationWorkflowTest.php` : (a) profil créé par éditeur passe en `en_attente` par défaut, (b) approbation par admin → `publié` + email à la personne (réutilise US3) + entrée dans `demandes_moderation` et `historique_profils`, (c) rejet par admin avec motif → reste `en_attente`, notification à l'éditeur, (d) tentative d'éditeur d'approuver son propre profil refusée (403)

### Implémentation pour User Story 4

- [X] T068 [US4] Implémenter `app/Services/Annuaire/ProfilModerationService.php` : `submit(Profil)`, `approve(Profil, User $admin)`, `reject(Profil, User $admin, string $motif)`, `archive(Profil, User $admin)`. Chaque méthode crée une `DemandeModeration` et journalise via `ProfilHistoriqueService`.
- [X] T069 [P] [US4] Créer `app/Notifications/ProfilModerationDecisionNotification.php` (vers l'éditeur, contient motif si rejet)
- [X] T070 [US4] Ajouter actions Filament dans `ListProfils.php` et `EditProfil.php` : `Approuver`, `Rejeter` (modale motif), `Archiver`, `Republier`. Boutons conditionnés par `ProfilPolicy` (admin uniquement).
- [X] T071 [US4] Ajouter sur `CreateProfil.php` : si l'utilisateur courant est éditeur (pas admin), forcer `etat_publication = en_attente` à la sauvegarde et bypass de l'interface de choix.
- [X] T072 [P] [US4] Ajouter `app/Filament/Resources/ProfilResource/RelationManagers/DemandesModerationRelationManager.php` (lecture seule, montre l'historique de modération du profil)

**Checkpoint** : Workflow modération opérationnel ; éditeur ne peut pas s'auto-approuver ; tests T067 verts.

---

## Phase 7 : User Story 5 - Import/export CSV et XLSX (Priorité : P2)

**Goal** : Exporter et importer des profils en lot via CSV (UTF-8 BOM + `;`) ou XLSX, avec détection de doublons par email et rapport d'erreurs.

**Independent Test** : Exporter 50 profils en XLSX, modifier 5 lignes, réimporter → les 5 sont mis à jour, pas de doublon, rapport correct.

### Tests pour User Story 5

- [X] T073 [P] [US5] Écrire `tests/Unit/Annuaire/ProfilExportServiceTest.php` : le CSV produit commence par BOM UTF-8 (`\xEF\xBB\xBF`), utilise `;` comme délimiteur, encode correctement « Côte d'Ivoire »
- [X] T074 [P] [US5] Écrire `tests/Feature/Annuaire/ImportExportTest.php` : (a) export CSV/XLSX respecte filtres actifs, (b) import CSV crée nouveaux profils si email inconnu, met à jour si email connu, ignore si choix « ignorer », (c) ligne invalide (nom manquant, type_profil hors enum) → erreur listée, lignes valides quand même importées, (d) auto-détection séparateur `;` vs `,`

### Implémentation pour User Story 5

- [X] T075 [P] [US5] Créer `app/Exports/ProfilsExport.php` (Maatwebsite Excel) avec `FromQuery`, `WithHeadings`, `WithMapping`, `WithCustomCsvSettings` (delimiter=`;`, output_encoding=`UTF-8`, use_bom=true), `WithEvents` pour styling XLSX
- [X] T076 [P] [US5] Créer `app/Imports/ProfilsImport.php` (`ToModel`, `WithHeadingRow`, `WithBatchInserts`, `WithValidation`, `WithCustomCsvSettings`, `SkipsOnError`, `SkipsOnFailure`) — résout pays via `Pays::firstWhere`, crée tags/domaines à la volée, conserve les erreurs ligne par ligne
- [X] T077 [US5] Implémenter `app/Services/Annuaire/ProfilExportService.php` (méthode `export(array $filtres, string $format): BinaryFileResponse`) et `app/Services/Annuaire/ProfilImportService.php` (méthode `import(UploadedFile $file, string $strategieDoublon): RapportImport`)
- [X] T078 [P] [US5] Créer DTO `app/Services/Annuaire/RapportImport.php` (counts : lues, créées, mises_à_jour, ignorées, erreurs[ligne, colonne, message]) + vue Filament pour l'afficher
- [X] T079 [US5] Ajouter dans `ListProfils.php` (Filament) actions header : `Exporter CSV`, `Exporter XLSX`, `Importer` (modale upload + radio stratégie doublon)
- [X] T080 [P] [US5] Créer la vue Filament d'aperçu d'erreurs d'import : `resources/views/filament/annuaire/import-rapport.blade.php`

**Checkpoint** : Export ouvre directement dans Excel/Windows fr avec accents intacts ; import gère doublons et erreurs ; tests T073-T074 verts.

---

## Phase 8 : User Story 6 - Historique des modifications (Priorité : P3)

**Goal** : Consultation de l'historique d'un profil (auteur, date, diff) ; restauration d'une version antérieure.

**Independent Test** : Modifier deux fois un profil → l'historique liste les 2 modifs ; restaurer la 1ʳᵉ version → profil revient à cet état, action elle-même journalisée.

### Tests pour User Story 6

- [X] T081 [P] [US6] Écrire `tests/Feature/Annuaire/HistoriqueTest.php` : (a) chaque modification d'un champ crée une entrée avec diff `{champ:{avant,apres}}`, auteur, IP, (b) admin peut consulter l'historique, éditeur ne peut voir que celui de ses profils, (c) restauration d'une version → état avant restauration consigné dans une nouvelle entrée
- [X] T082 [P] [US6] Écrire `tests/Unit/Annuaire/ProfilHistoriqueServiceTest.php` : produit le diff correct entre deux états, omet les champs `*_normalise` du diff (techniques)

### Implémentation pour User Story 6

- [X] T083 [US6] Compléter `ProfilHistoriqueService.php` : méthode `restaurer(HistoriqueProfil $entree, User $admin): Profil` qui réapplique les valeurs `avant` et journalise `action=restauré`
- [X] T084 [P] [US6] Créer `app/Filament/Resources/ProfilResource/RelationManagers/HistoriqueRelationManager.php` (lecture seule, table : date / auteur / action / champs modifiés / bouton « Restaurer » pour admin)
- [X] T085 [P] [US6] Créer la vue de détail d'une entrée d'historique `resources/views/filament/annuaire/historique-detail.blade.php` (affichage côte à côte avant/après par champ)

**Checkpoint** : Historique consultable ; restauration fonctionnelle ; tests T081-T082 verts ; SC-006 vérifié (100 % des modifs traçables).

---

## Phase 9 : Polish & Cross-Cutting Concerns

**Purpose** : Performance, sécurité, documentation, validations finales.

- [X] T086 [P] Mesurer SC-002 (95 % des recherches < 1 s sur 5 000 profils) : seeder de stress `database/seeders/ProfilsStressSeeder.php` (5 000 profils factices) + script `tests/Performance/AnnuaireBench.php` (mesure 100 recherches, P95)
- [X] T087 [P] Mesurer SC-008 (recherche + 3 filtres + tri < 1,5 s) : ajout au même bench
- [X] T087a [P] Mesurer **SC-004** : benchmark de `ProfilImportService` sur un fichier CSV de 500 profils factices ; assertion P95 < 120 secondes (fichier `tests/Performance/AnnuaireImportBench.php`, réutilise `ProfilsStressSeeder` pour générer le CSV)
- [X] T088 [P] Audit accessibilité de `/annuaire` (rôles ARIA, contraste, navigation clavier) sur la grille et la fiche détail — voir `specs/001-annuaire-evolution/checklists/accessibilite.md`
- [ ] T089 [P] (Déplacée en Phase 5 sous le numéro T066c) — vide ici pour préserver la numérotation. Voir T066c.
- [X] T090 [P] Documenter les nouvelles routes et endpoints dans `docs/annuaire.md` (à créer)
- [X] T091 [P] Mettre à jour `README.md` section Annuaire si présente, sinon la créer brièvement
- [X] T092 Lancer `vendor/bin/pint` puis `php artisan ide-helper:generate && php artisan ide-helper:models` pour maintenir les annotations IDE — Pint exécuté (12 corrections de style appliquées sur les fichiers Phase 8-9 + fix Pays/SearchService).
- [X] T093 Exécuter `vendor/bin/phpunit --coverage-text --filter=Annuaire` et vérifier que la couverture du code annuaire ≥ 80 % — **Driver `pcov` ajouté au Dockerfile** + couverture mesurée. Atteint ≥ 80 % sur : TextNormalizer, Pays, ProfilPublicResource, ProfilStoreRequest, ProfilSearchService, ProfilImportService, ConsentementProfil, ProfilHistoriqueService, ProfilGrid, ProfilConsentementService. Classes encore < 80 % (à compléter dans une itération dédiée) : Profil (72 %), ProfilPolicy (70 %), AnnuaireController (68 %), ProfilsImport (63 %), ProfilObserver (55 %), ProfilModerationService (54 %), ProfilExportService (33 %), RapportImport (42 %).
- [X] T094 Exécuter manuellement les 11 étapes du `quickstart.md` (golden path) et consigner les résultats dans une checklist `specs/001-annuaire-evolution/checklists/quickstart-validation.md` — checklist créée, exécution manuelle à dérouler avant clôture du MVP.
- [X] T095 Vérifier que les profils legacy `legacy_sans_consentement=true` apparaissent dans une vue Filament dédiée pour régularisation par les admins (filtre rapide « Sans consentement ») — filtre toggle avec indicateur dans `ProfilResource::table()`.

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)** : aucune dépendance — peut démarrer immédiatement.
- **Foundational (Phase 2)** : dépend de Phase 1 — **BLOQUE toutes les user stories**.
- **US1 (Phase 3)** : dépend de Phase 2 uniquement. Aucune dépendance vers d'autres stories.
- **US2 (Phase 4)** : dépend de Phase 2 + réutilise `ProfilSearchService` (créé en US1) → démarre après US1 OU étend en parallèle si dev ≠ touche le même fichier (T048 doit séquencer après T034).
- **US3 (Phase 5)** : dépend de Phase 2 ; indépendante de US1/US2 côté code (back-office vs public).
- **US4 (Phase 6)** : dépend de Phase 2 + US3 (utilise le formulaire et les notifications du back-office).
- **US5 (Phase 7)** : dépend de Phase 2 + US3 (les modèles et la policy doivent être prêts ; emprunte `ProfilStoreRequest` pour validation à l'import).
- **US6 (Phase 8)** : dépend de Phase 2 (observer et `ProfilHistoriqueService` y sont posés) + US3 (Filament Resource où s'accroche le RelationManager).
- **Polish (Phase 9)** : dépend de la complétion des user stories ciblées par l'incrément en cours.

### User Story Dependencies

- **US1 (P1)** : démarre après Phase 2 — indépendante.
- **US2 (P1)** : peut démarrer en parallèle de US1 si le développeur évite `ProfilGrid.php` et `ProfilSearchService.php` ; sinon enchaîner après US1.
- **US3 (P1)** : entièrement indépendante de US1/US2 côté code.
- **US4 (P2)** : dépend de US3 (Filament Resource).
- **US5 (P2)** : dépend de US3 (modèles complets + policy).
- **US6 (P3)** : dépend de US3 (RelationManager Filament).

### Within Each User Story

- Tests **avant** implémentation (TDD obligatoire) — vérifier qu'ils échouent d'abord.
- Models → Services → Composants UI / Endpoints → Intégration.

### Parallel Opportunities

- T002, T003, T004 (Setup) en parallèle.
- T006–T013 (migrations indépendantes) en parallèle. T005 doit passer avant l'utilisation, mais peut être créée en parallèle puis appliquée séquentiellement par `php artisan migrate`.
- T016–T021 (nouveaux modèles dans des fichiers distincts) en parallèle.
- T030–T033 (4 fichiers de test US1) en parallèle.
- T034–T035 (service + resource) en parallèle.
- T045–T047 (tests US2) en parallèle.
- T052–T054 (tests US3) en parallèle.
- T060–T063 (pages Filament + pipeline image dans fichiers distincts) en parallèle.
- T066b, T066d (tests de l'audit consentements et de la commande rappel) en parallèle de T067 (modération).
- T086, T087, T087a, T088 (benchs et accessibilité) en parallèle.
- T086–T091 (tâches Polish) majoritairement parallélisables.

---

## Parallel Example : User Story 1

```bash
# Lancer en parallèle les 4 fichiers de tests US1 (TDD — doivent échouer) :
Task: "T030 AnnuaireListingTest.php"
Task: "T031 AnnuaireSearchTest.php"
Task: "T032 ProfilDetailTest.php"
Task: "T033 ApiAnnuaireTest.php"

# Puis les implémentations indépendantes :
Task: "T034 ProfilSearchService.php"
Task: "T035 ProfilPublicResource.php"
Task: "T037 profil-grid.blade.php"
Task: "T038 profil-carte.blade.php / profil-ligne.blade.php"
```

---

## Implementation Strategy

### MVP First (US1 + US2 + US3)

La spec marque US1, US2 et US3 toutes comme P1. Le MVP livrable inclut donc la consultation publique avec recherche/filtres/tri ET le back-office CRUD avec consentement RGPD. Sans US3, on ne peut pas créer/maintenir le contenu ; sans US1/US2, l'annuaire public n'est pas exploitable.

1. Phase 1 (Setup) → 2. Phase 2 (Foundational) → 3. US1 → 4. US2 → 5. US3 → **STOP, valider MVP**.

### Incremental Delivery

- **Itération 1 (MVP)** : Setup + Foundational + US1 + US2 + US3. Démo publique + back-office opérationnel.
- **Itération 2** : US4 (modération). Démo : workflow complet éditeur ↔ admin.
- **Itération 3** : US5 (import/export). Démo : migration de données existantes en lot.
- **Itération 4** : US6 (historique avancé + restauration). Démo : audit complet.
- **Itération 5** : Polish (perf, accessibilité, doc, monitoring).

### Parallel Team Strategy

Avec 2-3 développeurs après la Phase 2 :

- Dév A : US1 puis US2 (chaîne logique).
- Dév B : US3 (back-office, indépendant) — puis US4 (modération) et US6 (historique) car liés à Filament.
- Dév C : US5 (import/export) dès que US3 fournit les modèles complets.

---

## Notes

- Toutes les migrations sont **additives** — aucun `drop` ni `change` destructif sur `profils`.
- Les coordonnées masquées ne doivent JAMAIS être sérialisées côté public (cf. T035 + tests T032/T033/T054). Test obligatoire ; toute régression bloque le merge.
- Le seeder `AnnuaireMigrationSeeder` (T028) doit rester idempotent — il peut être relancé en safe sur les profils déjà migrés.
- TDD strict : Phase 2 + tests US1 avant code US1. Vérifier que les tests échouent (rouge), puis implémenter (vert), puis refactor.
- Couverture cible ≥ 80 % validée par T093 avant de clore le MVP.
- Commit après chaque task ou groupe logique cohérent.
- Pas d'ajout de packages tiers au-delà de `maatwebsite/excel` sans justification écrite dans `research.md`.
