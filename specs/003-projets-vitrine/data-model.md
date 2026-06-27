# Phase 1 — Data Model : Section Projets (vitrine)

Schéma additif (7 tables nouvelles) réutilisant les référentiels existants `categories` (type `projet`), `pays` et `users`. **Rappel contrainte legacy** : `categories.id`, `pays.id`, `users.id` sont des `int` **sans AUTO_INCREMENT** → toute FK vers elles est une **colonne `integer` sans contrainte DB** ; les ids sont résolus côté seeder/applicatif (même contournement que `002-media-newsroom`).

Conventions : toutes les nouvelles tables ont `id` `bigIncrements`, `created_at`/`updated_at`. `projets` porte `deleted_at` (SoftDeletes).

---

## 1. `projets` (entité centrale)

| Colonne | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `titre` | string | nom du projet (FR-002) |
| `slug` | string, unique, index | route-key (trait `Sluggable`, FR-009) |
| `titre_normalise` | string, nullable, index | rempli par `TextNormalizer` (recherche) |
| `resume` | string(280) | brève description card 2–3 lignes (FR-002) |
| `visuel_card` | string, nullable | chemin image de card (`projets/cards/…`) |
| `visuel_principal` | string, nullable | chemin visuel principal détail (`projets/principal/…`) |
| `contexte` | text, nullable | contexte / problématique (FR-010) |
| `objectifs` | text, nullable | objectifs du projet |
| `description` | longText, nullable | description détaillée |
| `activites` | longText, nullable | activités réalisées |
| `statut` | enum(`actif`,`realise`,`en_developpement`) | statut métier, badge (FR-003) |
| `is_published` | boolean, default false, index | visibilité publique (FR-006/FR-020) |
| `published_at` | datetime, nullable | date de mise en ligne |
| `pays_id` | integer, nullable, index | FK logique → `pays` (sans contrainte DB) (R3) |
| `portee` | enum(`pays`,`regional`,`continental`), default `pays` | nature de la zone (R3) |
| `zone_libelle` | string, nullable | libellé portée non nationale (« Afrique de l'Ouest ») |
| `featured` | boolean, default false | mise en avant listing (FR-025) |
| `position` | integer, default 0, index | ordre d'affichage manuel (FR-025) |
| `meta_titre` | string, nullable | SEO (optionnel) |
| `meta_description` | string, nullable | SEO (optionnel) |
| `created_at` / `updated_at` / `deleted_at` | timestamps + soft delete | |

**Scopes** : `published()` (`is_published = true` et `published_at <= now()`), `byStatut($s)`, `byThematique($catId)`, `byZone($paysId|portee)`, `ordered()` (`featured desc, position asc, published_at desc`).
**Accessors** : `visuel_card_url`, `visuel_principal_url` (placeholder si null), `statut_label` (depuis `Projet::$statuts`).
**Relations** : `categories()` BelongsToMany (thématiques), `pays()` BelongsTo (logique), `resultats()` HasMany, `medias()` HasMany (galerie), `temoignages()` HasMany, `partenaires()` BelongsToMany.

**Constantes** :
```php
public static array $statuts = [
    'actif' => 'Actif',
    'realise' => 'Réalisé',
    'en_developpement' => 'En développement',
];
public static array $portees = [
    'pays' => 'National', 'regional' => 'Régional', 'continental' => 'Continental',
];
```

**Règles de validation (FR)** : `titre` requis ; `resume` requis (≤ 280) ; `statut` ∈ `$statuts` ; `portee` ∈ `$portees` ; `pays_id` requis si `portee = pays` ; `zone_libelle` requis si `portee ≠ pays`.

---

## 2. `category_projet` (pivot thématiques — R2)

| Colonne | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `projet_id` | bigint, FK → `projets` (cascade) | |
| `category_id` | integer, index | FK logique → `categories` (type `projet`, sans contrainte DB) |
| `position` | integer, default 0 | ordre d'affichage des thématiques |

Unicité `(projet_id, category_id)`. Un projet a 1..n thématiques (FR-002/FR-005).

---

## 3. `partenaires` (table master réutilisable — R7)

| Colonne | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `nom` | string | |
| `slug` | string, unique, nullable | |
| `logo` | string, nullable | `projets/partenaires/…` |
| `url` | string, nullable | site du partenaire |
| `created_at` / `updated_at` | timestamps | |

## 4. `partenaire_projet` (pivot — R7)

| Colonne | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `projet_id` | bigint, FK → `projets` (cascade) | |
| `partenaire_id` | bigint, FK → `partenaires` (cascade) | |
| `position` | integer, default 0 | ordre d'affichage |

Unicité `(projet_id, partenaire_id)`.

---

## 5. `projet_resultats` (chiffres clés / impact — R8)

| Colonne | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `projet_id` | bigint, FK → `projets` (cascade) | |
| `libelle` | string | ex. « Bénéficiaires formés » |
| `valeur` | string | ex. « 1 200 » (string pour formats libres : %, +, etc.) |
| `unite` | string, nullable | ex. « personnes », « pays » |
| `icone` | string, nullable | nom d'icône optionnel |
| `position` | integer, default 0 | ordre |

`hasMany` ordonné. Section masquée si vide (FR-012/FR-013).

---

## 6. `projet_medias` (galerie photos/vidéos — R6)

| Colonne | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `projet_id` | bigint, FK → `projets` (cascade) | |
| `type` | enum(`image`,`video`) | |
| `chemin` | string, nullable | fichier téléversé (image, ou vidéo native) |
| `url_embed` | string, nullable | lien YouTube/Vimeo (si `type=video` en embed) |
| `legende` | string, nullable | légende |
| `position` | integer, default 0 | ordre galerie |

`hasMany` ordonné. Pour `type=video`, l'iframe est produite par `App\Helper\VideoEmbed` à partir de `url_embed` ; sinon `<video>` natif sur `chemin`. Galerie tolérante aux médias indisponibles (edge case).

---

## 7. `projet_temoignages` (témoignages optionnels — R9)

| Colonne | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `projet_id` | bigint, FK → `projets` (cascade) | |
| `auteur` | string | nom de la personne |
| `fonction` | string, nullable | fonction / rôle |
| `organisation` | string, nullable | organisation |
| `contenu` | text | citation |
| `photo` | string, nullable | `projets/temoignages/…` |
| `position` | integer, default 0 | ordre |

`hasMany` ordonné. Section entièrement masquée si aucune ligne (FR-011/FR-012).

---

## Diagramme relationnel (texte)

```text
              ┌──────────────────────────── Projet ────────────────────────────┐
              │ id, titre, slug, titre_normalise, resume, visuels,             │
              │ contexte, objectifs, description, activites,                   │
              │ statut(enum), is_published, published_at,                      │
              │ pays_id→pays, portee, zone_libelle, featured, position, SEO    │
              └───┬─────────┬──────────┬───────────┬───────────────┬───────────┘
                  │ n..n    │ 1..n     │ 1..n      │ 1..n          │ n..n
        category_projet  projet_     projet_     projet_       partenaire_
        → categories     resultats   medias      temoignages   projet → partenaires
        (type='projet')  (chiffres)  (galerie)   (optionnel)   (master réutilisable)
                  │
               pays_id ───────────────→ pays (référentiel existant)
```

## Mapping exigences → modèle

| FR | Couverture |
|---|---|
| FR-002 (card) | `projets.titre/visuel_card/resume` + `categories` + `pays_id`/`zone_libelle` + `statut` |
| FR-003 (badge statut) | `projets.statut` enum + `$statuts` |
| FR-005 (filtres) | scopes `byThematique`/`byZone`/`byStatut` via `ProjetSearchService` |
| FR-006/FR-020 (publication) | `is_published` + `published_at` + scope `published()` |
| FR-010 (détail) | colonnes `contexte/objectifs/description/activites/visuel_principal` + relations `resultats/partenaires/medias` |
| FR-011/FR-012 (sections optionnelles) | relations `hasMany` masquées si vides |
| FR-013 (chiffres clés) | `projet_resultats` (libelle/valeur/unite) |
| FR-014 (galerie) | `projet_medias` (image/video) |
| FR-022 (partenaires/témoignages) | `partenaires` + pivot, `projet_temoignages` |
| FR-025 (ordre/mise en avant) | `featured` + `position` + scope `ordered()` |
