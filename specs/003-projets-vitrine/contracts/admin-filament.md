# Contrat — Back-office Filament (`ProjetResource`, `PartenaireResource`)

Calqué sur `App\Filament\Resources\MediaResource` (+ ses RelationManagers). Accès réservé aux administrateurs (FR-024) selon la policy du panneau Filament existant.

## `ProjetResource`

- **Modèle** : `App\Models\Projet`. Navigation : groupe « Projets » (ou « Contenus »). Slug Filament `projets`.
- **Recherche/colonnes table** : `titre`, badge `statut`, `is_published` (toggle/icone), thématiques, zone, `published_at`, `position`. Filtres table : `statut`, `is_published`, thématique, portée.
- **Actions** : Créer / Éditer / Supprimer (soft delete) + actions groupées (publier/dépublier, changer statut). FR-017/FR-019/FR-020.

### Formulaire (sections)

1. **Identité & card** : `titre` (→ slug auto), `resume` (≤ 280, FR-002), `visuel_card` (FileUpload image), `featured` (toggle), `position`.
2. **Contenu détaillé** : `contexte`, `objectifs`, `description` (RichEditor), `activites`, `visuel_principal` (FileUpload image). FR-010.
3. **Classification** : `categories` (multi-select `type='projet'`, créable), `portee` (select), `pays_id` (select depuis `pays`, requis si `portee=pays`), `zone_libelle` (requis si `portee≠pays`). FR-002/FR-005.
4. **Statut & publication** : `statut` (select `actif|realise|en_developpement`, FR-003/FR-019), `is_published` (toggle), `published_at` (DateTimePicker). FR-006/FR-020.
5. **SEO (optionnel)** : `meta_titre`, `meta_description`.

### RelationManagers (FR-021/FR-022/FR-023)

| RelationManager | Table | Champs éditables | Notes |
|---|---|---|---|
| `ResultatsRelationManager` | `projet_resultats` | `libelle`, `valeur`, `unite`, `icone`, `position` | chiffres clés réordonnables (FR-023) |
| `GalerieMediasRelationManager` | `projet_medias` | `type` (image/video), `chemin` (upload si image), `url_embed` (si vidéo), `legende`, `position` | upload images + embed vidéo (FR-021) |
| `TemoignagesRelationManager` | `projet_temoignages` | `auteur`, `fonction`, `organisation`, `contenu`, `photo`, `position` | optionnel (FR-011) |
| `PartenairesRelationManager` | `partenaire_projet` | attache des `partenaires` existants + `position` | sélection depuis la table master (FR-022) |

> Uploads sur disque `public` : `projets/cards`, `projets/principal`, `projets/galerie`, `projets/partenaires`, `projets/temoignages` (miniatures via `intervention/image`).

## `PartenaireResource`

- **Modèle** : `App\Models\Partenaire` (master réutilisable). CRUD : `nom`, `logo` (FileUpload), `url`. Permet de gérer les partenaires indépendamment et de les attacher à plusieurs projets (R7/FR-022).

## Thématiques

Gérées via la `CategoryResource` existante (filtrer/créer des catégories `type='projet'`) — pas de nouvelle ressource dédiée (R2).

## Tests (Feature, Filament)

- Création d'un projet complet (tous champs + ≥1 média galerie + ≥1 chiffre clé + ≥1 partenaire) → visible publiquement une fois publié (SC-003, scénario US3-1).
- Modification de `statut`/contenu reflétée côté public (US3-2).
- Suppression → disparaît du listing et `show` → 404 (US3-3).
- Projet non publié → absent du public (US3-5/SC-002).
- Upload d'images/vidéos → médias associés et rendus sur le détail (US3-4).
