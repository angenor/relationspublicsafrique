# Phase 1 — Data Model : Section Événements (vitrine)

**Évolution additive** d'un schéma existant. La table **`events`** existe déjà (`create_events_table` + `add_view_column`) ; on l'**enrichit** (colonnes vitrine + correctif `pays_id`) et on ajoute **2 tables liées** (`event_speakers`, `event_medias`). On **réutilise** les référentiels `categories` (`type='event'` = type d'événement) et `pays`, ainsi que la table existante `event_registrations` (inscription interne).

**Rappel contrainte legacy** : `categories.id`, `pays.id`, `users.id` sont des `int` **sans AUTO_INCREMENT** → toute FK vers elles est une **colonne `integer` sans contrainte DB** ; ids résolus côté seeder/applicatif (contournement 002/003).

Conventions : les nouvelles tables ont `id` `bigIncrements` + `created_at`/`updated_at`.

---

## 1. `events` — colonnes existantes (rappel, **inchangées**)

| Colonne | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `title` | string | titre (FR-003) |
| `slug` | string, nullable | route-key (`Sluggable`) — utilisé par `events.show` |
| `description` | text | description complète (FR-009) |
| `resume` | text, nullable | courte description card (FR-003) — accessor `resume_text` (fallback `Str::limit(description,200)`) |
| `image` | string, nullable | visuel (`events/…`) — accessor `img` (placeholder `images/default-event.jpg`) |
| `location` | string, nullable | lieu / ville (FR-009) — « En ligne » si `online` (R1) |
| `start_date` | dateTime | début (FR-009, statut temporel) |
| `end_date` | dateTime | fin (statut temporel) |
| `registration_deadline` | dateTime, nullable | date limite d'inscription (FR-011/FR-014) |
| `max_participants` | integer, nullable | capacité (FR-014) |
| `current_participants` | integer, default 0 | compteur inscrits internes |
| `price` | decimal(10,2), default 0 | prix (hors périmètre vitrine, conservé) |
| `status` | string, default `draft` | **statut éditorial** : `draft/published/cancelled/completed` (FR-019) |
| `is_featured` | boolean, default false | mise en avant listing |
| `online` | boolean, default false | **= format en ligne** (R1) — n'est plus un gate de visibilité |
| `view` | integer | compteur de vues (`add_view_column`) |
| `user_id` | integer, nullable | organisateur (FK logique → `users`) |
| `category_id` | integer, nullable | **type d'événement** → `categories` `type='event'` (R4, FK logique) |
| `created_at` / `updated_at` | datetime | |

---

## 2. `events` — colonnes **ajoutées** (migrations additives)

### 2a. `add_pays_id_to_events` *(CORRECTIF — R2)*

| Colonne | Type | Notes |
|---|---|---|
| `pays_id` | integer, nullable, index | FK **logique** → `pays` (sans contrainte DB). Débloque `Event::pays()`, l'usage `EventResource`, et le filtre « par pays » (FR-015). |

### 2b. `add_vitrine_columns_to_events` *(enrichissement vitrine)*

| Colonne | Type | Notes |
|---|---|---|
| `objectifs` | text, nullable | objectifs de l'événement (FR-009) |
| `programme` | longText, nullable | programme / agenda (FR-009) — texte riche |
| `public_cible` | text, nullable | public cible (FR-009) |
| `compte_rendu` | longText, nullable | compte rendu post-événement (texte riche, FR-010/FR-022) |
| `registration_mode` | string(20), default `internal`, index | mode d'inscription `internal`\|`external` (R9, FR-013) |
| `registration_url` | string, nullable | lien externe si `registration_mode='external'` (FR-013) |

> Aucune colonne `temporal_status` : le statut temporel est **dérivé** (R3).

**Constantes (modèle `Event`)** — existantes + ajoutées :
```php
public static array $statuses = [          // existant (éditorial)
    'draft' => 'Brouillon', 'published' => 'Publié',
    'cancelled' => 'Annulé', 'completed' => 'Terminé',
];
public static array $registrationModes = [  // ajouté (R9)
    'internal' => 'Inscription sur le site', 'external' => 'Lien externe',
];
public static array $temporalStatuses = [   // ajouté (R3, affichage badge)
    'upcoming' => 'À venir', 'ongoing' => 'En cours', 'past' => 'Clos',
];
```

**Accessors/scopes du modèle `Event`** :
- *Existants réutilisés* : `is_upcoming`, `is_ongoing`, `is_completed`, `img`, `resume_text`, `link`, scopes `published()`, `upcoming()`, `ongoing()`, `past()`, `featured()`, `byCategory()`, `online()` (conservé mais **plus utilisé comme gate de visibilité**).
- *Ajoutés* :
  - `temporal_status` (accessor) → `upcoming|ongoing|past` (priorité : si `status='cancelled'`, l'UI affiche « Annulé »).
  - `temporal_status_label` → libellé depuis `$temporalStatuses`.
  - `can_register` (**corrigé R1/R9**) : `true` ssi `status='published'`, `status != 'cancelled'`, non clos (`!is_completed`), `registration_mode='internal'`, `registration_deadline` non dépassée, capacité non atteinte. (Ne dépend **plus** de `online`.)
  - `has_post_event_media` : `is_completed && (medias()->exists() || compte_rendu)`.
  - `has_replay` : existence d'un `event_medias.type='replay'`.
  - `format_label` : « En ligne » si `online`, sinon `location` (FR-009).
  - `cta` (logique, peut vivre dans la vue/service) : `register_internal` | `register_external` | `view_replay` | `view_details` selon état (cf. contrat public-routes).
- *Relations ajoutées* : `speakers()` HasMany (`event_speakers`, ordonné `position`), `medias()` HasMany (`event_medias`, ordonné `position`). *Existantes* : `category()`, `pays()`, `user()`, `registrations()`, `registeredUsers()`.

**Règles de validation (FR)** : `title` requis ; `start_date` requis ; `end_date` requis et **≥ `start_date`** ; `registration_deadline` (si présente) ≤ `start_date` ; `status` ∈ `$statuses` ; `registration_mode` ∈ `$registrationModes` ; `registration_url` **requis si** `registration_mode='external'` (URL valide) ; `category_id` parmi catégories `type='event'`.

---

## 3. `event_speakers` (intervenants — R5)

| Colonne | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `event_id` | bigint, FK → `events` (cascade) | |
| `nom` | string | nom de l'intervenant (FR-009) |
| `role` | string, nullable | rôle / qualité (ex. « Modératrice ») |
| `organisation` | string, nullable | organisation / affiliation |
| `photo` | string, nullable | `events/speakers/…` |
| `bio` | text, nullable | courte biographie |
| `position` | integer, default 0, index | ordre d'affichage |

`hasMany` ordonné par `position`. Section détail masquée si aucune ligne (sections optionnelles).

---

## 4. `event_medias` (médias post-événement — R6)

| Colonne | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `event_id` | bigint, FK → `events` (cascade) | |
| `type` | string(20) | `replay` \| `image` \| `document` |
| `chemin` | string, nullable | fichier téléversé (image, vidéo native, document) `events/medias/…` |
| `url_embed` | string, nullable | lien YouTube/Vimeo (replay en embed via `VideoEmbed`) |
| `legende` | string, nullable | légende / titre |
| `position` | integer, default 0, index | ordre d'affichage |

`hasMany` ordonné par `position`. Rendu :
- `type='replay'` : `url_embed` → `App\Helper\VideoEmbed`, sinon `<video>` natif sur `chemin`.
- `type='image'` : galerie `<img>` + lightbox léger (`events.js`).
- `type='document'` : lien de téléchargement (`chemin`).

Constante : `EventMedia::$types = ['replay' => 'Replay vidéo', 'image' => 'Photo', 'document' => 'Document']`.
N'a de sens que pour un événement **clos** (alimente `has_post_event_media`/`has_replay`). Tolérant aux médias indisponibles (edge case).

---

## 5. Tables réutilisées (inchangées)

- **`event_registrations`** (existante) : inscription **interne** (event_id, user_id, status `registered/cancelled/confirmed/attended`, unicité `(event_id,user_id)`). Réutilisée telle quelle par le CTA `internal` (R9). `RegistrationsRelationManager` Filament conservé.
- **`categories`** (`type='event'`) : type d'événement (R4).
- **`pays`** : zone géographique (filtre FR-015), via `events.pays_id` (R2).
- **`users`** : organisateur (`events.user_id`).

---

## Diagramme relationnel (texte)

```text
        ┌───────────────────────────── Event (étendu) ─────────────────────────────┐
        │ id, title, slug, description, resume, image, location,                    │
        │ start_date, end_date, registration_deadline, max/current_participants,    │
        │ price, status(éditorial), is_featured, online(=format), view,             │
        │ user_id→users, category_id→categories(type='event'), pays_id→pays,        │
        │ objectifs, programme, public_cible, compte_rendu,                         │
        │ registration_mode(internal|external), registration_url                    │
        │ [dérivés: temporal_status, can_register, has_replay, has_post_event_media]│
        └───┬───────────────┬───────────────┬──────────────────┬───────────────────┘
            │ 1..n          │ 1..n          │ 1..n             │ n..1 / n..1 / n..1
      event_speakers   event_medias   event_registrations   categories(type='event') / pays / users
      (intervenants)   (replay/image/  (inscription interne   (type / zone / organisateur)
                        document)        existante)
```

## State transitions

- **Statut éditorial** (`status`, saisi) : `draft → published` (Publier) ; `published → draft` (Dépublier) ; `* → cancelled` (Annuler) ; `published → completed` (manuel ou cron optionnel `events:mark-completed`, R8). `cancelled` prime à l'affichage (badge « Annulé », `can_register=false`).
- **Statut temporel** (dérivé, non stocké) : `upcoming` (`now < start_date`) → `ongoing` (`start_date ≤ now ≤ end_date`) → `past` (`now > end_date`). Transition automatique au fil du temps (R3).
- **Inscription** (`can_register`) : ouverte ssi publié, non annulé, non clos, mode `internal`, deadline non dépassée, capacité disponible → sinon « Complet »/« Inscriptions closes »/« Annulé ».

## Mapping exigences → modèle

| FR | Couverture |
|---|---|
| FR-002/FR-003 (cards, statut temporel) | `events.title/image/resume/location/start_date/end_date` + `temporal_status` + `category`/`pays` |
| FR-004/FR-010 (CTA contextuel/dynamique) | accessors `can_register`/`registration_mode`/`has_post_event_media`/`has_replay` → CTA |
| FR-006/FR-019 (publication / éditorial vs temporel) | `status` (gate visibilité, R1) + `temporal_status` dérivé |
| FR-009 (page détail) | `objectifs/programme/public_cible/description` + `event_speakers` + `format_label` |
| FR-011/FR-014 (inscription interne, capacité, deadline) | `event_registrations` + `can_register` + `max/current_participants` + `registration_deadline` |
| FR-013 (interne/externe) | `registration_mode` + `registration_url` (R9) |
| FR-015 (filtres statut/type/pays) | `temporal_status` + `category_id` (type='event') + `pays_id` (R2/R4) via `EventSearchService` |
| FR-016 (tri par date) | scopes `start_date` asc/desc (proche/récent) |
| FR-017 (compte à rebours) | `start_date` → countdown JS (R8) |
| FR-018 (statut auto) | accessors/scopes temporels dérivés (R3) |
| FR-022 (médias post-événement) | `event_medias` (replay/image/document) + `compte_rendu` (R6) |
| FR-020/FR-021/FR-023 (back-office) | `EventResource` étendu + RelationManagers + badge temporel (contrat admin-filament) |
