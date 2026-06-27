# Contrat — Composant Livewire `GrilleProjets`

`App\Livewire\Projets\GrilleProjets` — calqué sur `App\Livewire\Media\GrilleMedias` (lui-même issu de `Annuaire\ProfilGrid`). Pilote le listing réactif `/projets` : filtres, recherche, tri, « charger plus ». Vue : `resources/views/livewire/projets/grille-projets.blade.php`.

## Propriétés publiques (liées à l'URL via `#[Url]`)

| Propriété | Type | Défaut | Rôle |
|---|---|---|---|
| `q` | string | `''` | recherche plein-texte (`titre_normalise`) |
| `thematique` | ?int | `null` | id catégorie (`type='projet'`) (FR-005) |
| `zone` | ?string | `null` | `pays:{id}` ou `portee:{regional|continental}` (FR-005) |
| `statut` | ?string | `null` | `actif|realise|en_developpement` (FR-005) |
| `tri` | string | `'recent'` | `recent` (published_at desc) ou `alpha` (titre) |
| `perPage` | int | `config('projets.per_page')` | taille de page courante |

> Toutes les propriétés de filtre sont en query-string (`#[Url]`) → état partageable/bookmarkable et testable (SC-004).

## Comportements

- **`updated*`** : tout changement de filtre/recherche/tri **réinitialise** la pagination (`perPage` au pas initial) et recharge via `ProjetSearchService`.
- **`chargerPlus()`** : incrémente `perPage` de `config('projets.per_page_step')` (FR-008/SC-005). Un IntersectionObserver peut déclencher le chargement en complément du bouton.
- **`render()`** : délègue à `App\Services\Projets\ProjetSearchService` qui applique `published()` + filtres + tri + eager-load (`categories`, `pays`) et retourne une collection paginée. Anti-N+1 garanti.
- **État vide** (FR-007) : la vue affiche un message explicite quand le résultat est vide (aucun projet, ou filtre sans correspondance), avec action « Réinitialiser les filtres ».

## Données fournies à la vue (par card — FR-002)

`visuel_card_url`, `titre`, `slug` (→ `route('projets.show', $slug)`), `resume`, thématiques (badges), zone (libellé pays ou `zone_libelle`), `statut_label` (badge coloré par statut).

## Source des options de filtre

- **Thématiques** : `Category::where('type','projet')->orderBy('titre')` (réutilise `categories`).
- **Zones** : pays distincts référencés par des projets publiés + entrées portée (Régional/Continental).
- **Statuts** : `Projet::$statuts`.

## Tests (Feature/Unit)

- Filtrer par `thematique`/`zone`/`statut` met à jour la liste et l'URL (SC-004).
- `chargerPlus()` augmente le nombre d'items rendus (SC-005).
- `q` filtre de façon accent-insensible (via `titre_normalise`).
- Résultat vide → message d'état vide (SC-006/FR-007).
- Les brouillons n'apparaissent jamais quel que soit le filtre (SC-002).
