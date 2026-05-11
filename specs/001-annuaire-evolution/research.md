# Phase 0 — Research : Évolution de l'annuaire

> **Note** : §10 (rôles d'autorisation) ajouté suite à `/speckit-analyze` pour lever l'ambiguïté I1 sur l'implémentation des rôles `admin` / `éditeur`.

## 1. Recherche insensible aux accents et à la casse

- **Decision** : Maintenir des colonnes pré-normalisées (`nom_normalise`, `prenom_normalise`, `organisation_normalisee`, `ville_normalisee`) automatiquement remplies par mutateur Eloquent via un service `TextNormalizer`. Requête de recherche sur les colonnes normalisées avec `LIKE %terme_normalise%`.
- **Rationale** :
  - Solution portable MySQL + SQLite (les tests tournent en SQLite).
  - Pas de dépendance à `utf8mb4_unicode_ci` qui gère mal certains caractères (œ, ñ).
  - Évite les fonctions SQL lourdes (`CONVERT`, `COLLATE`) à chaque requête → recherche rapide.
  - Indexable.
- **Alternatives considered** :
  - `MATCH ... AGAINST` (FULLTEXT) : performant mais comportement variable entre MySQL/SQLite, gestion accents limitée sans `WITH PARSER ngram`.
  - Meilisearch/Algolia : surdimensionné pour 5 000 profils, coût opérationnel, déploiement supplémentaire.
  - Conversion à la volée via `LOWER()` + extensions Unicode : pas portable SQLite.

**Implémentation** : `TextNormalizer::normalize()` = `mb_strtolower(transliterator_transliterate('Any-Latin; Latin-ASCII; [:Punctuation:] Remove', $value))`.

## 2. Bibliothèque export/import CSV et XLSX

- **Decision** : `maatwebsite/excel` ^3.1.
- **Rationale** :
  - Wrapper Laravel mature autour de PhpSpreadsheet.
  - Supporte explicitement le BOM (`WithCustomCsvSettings` + `output_encoding`).
  - Permet le délimiteur `;` (paramétrable `delimiter`).
  - API `FromQuery`, `ToModel`, `Importable` facilement testable.
  - Large adoption dans l'écosystème Laravel → maintenance assurée.
- **Alternatives considered** :
  - `openspout/openspout` : performant pour gros volumes mais API plus bas niveau, moins d'intégrations Laravel natives.
  - PhpSpreadsheet brut : verbeux, sans helpers Laravel.
  - Génération CSV à la main via `fputcsv` : faisable pour export mais l'import (XLSX) impose un parser ; uniformiser sur une seule lib.

## 3. Validation et redimensionnement de la photo

- **Decision** : Utiliser `intervention/image` (déjà présent). Pipeline :
  1. Laravel Validator : `image|mimes:jpg,jpeg,png,webp|max:2048|dimensions:min_width=400,min_height=400`.
  2. `Intervention\Image\Facades\Image::make($file)->fit(800, 800)` pour recadrage carré + redimensionnement à 800×800.
  3. Stockage sous `storage/app/public/profils/{id}.webp` (conversion WebP pour économiser).
- **Rationale** : déjà disponible, ratio carré (1:1) garanti par `fit()` qui crop intelligemment.
- **Alternatives considered** : Spatie Media Library (overkill pour un seul fichier par profil), traitement async via job (utile si volume > 100 imports concurrents — pas le cas ici).

## 4. Jeton de retrait RGPD

- **Decision** : `Str::random(64)` stocké sur `consentements_profils.jeton_retrait`, URL signée Laravel (`URL::signedRoute('annuaire.retrait', ['token' => $token], now()->addDays(90))`), renouvelable via lien email envoyé tous les 90 jours.
- **Rationale** :
  - URL signée empêche la falsification du token.
  - Expiration glissante évite qu'un lien intercepté reste valide indéfiniment.
  - Jeton stocké en clair côté DB acceptable (équivalent à un mot de passe usage-unique) ; alternative : hash SHA-256, à arbitrer si revue sécurité l'impose.
- **Alternatives considered** :
  - JWT signé sans persistance : impossible d'invalider après émission, mauvaise UX en cas de demande de regénération.
  - Magic link via package tiers (Spatie Magic Link) : dépendance supplémentaire pour 1 usage.

## 5. Workflow de modération

- **Decision** : enum simple `état_publication` (`en_attente`, `publié`, `archivé`) + table `demandes_moderation` qui historise chaque décision (moderateur, date, motif). Logique encapsulée dans `ProfilModerationService` avec méthodes `submit()`, `approve()`, `reject($motif)`, `archive()`.
- **Rationale** : 3 états seulement, peu de transitions, une state machine library serait du sur-engineering.
- **Alternatives considered** : `spatie/laravel-model-states` (overhead pour 3 états).

## 6. Historique des modifications

- **Decision** : Table `historique_profils` alimentée par `ProfilObserver` (events `created`, `updated`, `deleted`, `restored`) + appels explicites depuis les services pour les actions hors Eloquent (validation, restauration). Stocke un diff JSON champ par champ (avant/après).
- **Rationale** :
  - Contrôle total du format diff (utile pour l'UI de l'historique).
  - Pas de dépendance externe.
  - Permet d'attacher l'IP et l'utilisateur facilement.
- **Alternatives considered** : `spatie/laravel-activitylog` (excellent mais ajoute une dépendance ; à reconsidérer si l'app a déjà besoin de tracer d'autres modèles).

## 7. Architecture d'affichage public

- **Decision** : Livewire 3 pour la page liste (`/annuaire`) — composant `ProfilGrid` qui gère barre de recherche, filtres, tri, pagination, bascule grille/liste. Blade simple pour la fiche détail (`/annuaire/{slug}`).
- **Rationale** :
  - Livewire est déjà utilisé dans le projet (cohérent avec la stack actuelle).
  - Réactivité serveur sans état JS complexe ni endpoint API dédié.
  - SEO préservé (rendu serveur).
- **Alternatives considered** :
  - Vue 3 + endpoint REST : possible mais redondant avec Livewire pour le même besoin.
  - Page Blade pure + form GET : pas assez fluide pour la combinaison filtres + tri en live.

## 8. Index et performances

- **Decision** : Index composites
  - `(état_publication, type_profil)` pour le filtrage public.
  - Index simples sur `nom_normalise`, `prenom_normalise`, `organisation_normalisee`, `pays_id`, `ville_normalisee`.
  - Index sur `historique_profils(profil_id, created_at)`.
- **Rationale** : 5 000 profils tiennent en RAM ; les index couvrent les filtres et tris fréquents et permettent de tenir SC-002 (< 1 s) sans recourir à un index externe.

## 9. Migration des données existantes (`profils` actuel)

- **Decision** :
  - Mapping `online` → `état_publication` : `online=1 ⟹ publié`, `online=0 ⟹ archivé`.
  - Mapping `aprouve` : si `aprouve=0` ⟹ forcer `en_attente` (overwrite). Sinon laisser au mapping ci-dessus.
  - `type_profil` initialisé à `autre` pour tous les profils existants.
  - `domaine` (string) parsé en plusieurs `DomaineExpertise` (split sur virgule/point-virgule) au seeder de migration ; en cas de doute, créer un seul domaine pour la valeur existante.
  - `facebook`, `twitter`, `youtube`, `linkding`, `site` → migrer en lignes `LienExterne` (types : facebook, twitter, youtube, linkedin, site_web).
  - Pas de consentement attesté pour le legacy : laisser `null`, mais autoriser la consultation publique pour ne pas casser l'existant. Un script post-déploiement signalera ces profils aux administrateurs pour régularisation.
- **Rationale** : transition douce, pas de blocage des données existantes. La trace `legacy_sans_consentement = true` permet de cibler les régularisations.
- **Alternatives considered** : Bloquer tous les profils legacy en `en_attente` jusqu'à régularisation → trop disruptif.

## 10. Rôles `admin` et `éditeur` — mécanique d'autorisation

- **Decision** : réutiliser le champ existant `users.type` (string) déjà utilisé par `User::canAccessPanel()` (constate `app/Models/User.php`). Valeurs reconnues pour cette feature : `admin`, `editeur`. Tout autre valeur (ou null) ⇒ aucun accès au back-office annuaire.
- **Rationale** : pas de dépendance nouvelle (`spatie/laravel-permission` non installé), cohérence avec l'authentification Filament déjà en place. Évite une refonte du modèle d'autorisation pour une seule feature.
- **Conséquences** :
  - `ProfilPolicy` s'appuie sur `$user->type === 'admin'` (toutes capacités) et `$user->type === 'editeur'` (création/édition de ses propres profils, soumission pour validation).
  - Les éditeurs existants doivent recevoir `type = 'editeur'` (script de seed/admin opérationnel — pas dans le périmètre de cette feature, à coordonner avec l'équipe).
  - Le code legacy `canAccessPanel()` contient un bug (`=` au lieu de `===`) à corriger en passant (hors scope strict, mais signalé) — ajouter un test régression.
- **Alternatives considered** :
  - Installer `spatie/laravel-permission` : surdimensionné pour 2 rôles ; introduit migrations supplémentaires sur `roles`/`permissions` et complexifie l'authentification existante.
  - Utiliser un middleware `role:admin` ad hoc : revient au même, masqué derrière une couche.

## 11. Notifications email

- **Decision** : Notifications Laravel standard (`Illuminate\Notifications\Notification`) avec canaux `mail`. Templates Markdown sous `resources/views/vendor/notifications/`. Envoi via la file (`ShouldQueue`) pour ne pas bloquer le back-office lors d'imports en masse.
- **Rationale** : infrastructure existante, gestion async via les workers Laravel déjà configurés.
- **Alternatives considered** : Service tiers (Mailgun events, Sendgrid templates) — pas justifié pour 50 notifications/jour.
