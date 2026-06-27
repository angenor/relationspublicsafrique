# Contrat — Composant Livewire `App\Livewire\Events\GrilleEvents`

Listing réactif de `/evenements` : filtres (statut temporel / type / pays), tri, recherche, « charger plus ». **Calqué sur `App\Livewire\Projets\GrilleProjets`** (lui-même dérivé de `GrilleMedias`). Rendu dans `resources/views/livewire/events/grille-events.blade.php`, intégré par `events/index.blade.php` (layout `front`).

## Propriétés publiques (liées à l'URL)

| Propriété | `#[Url(as:)]` | Type | Défaut | Rôle |
|---|---|---|---|---|
| `q` | `q` | string | `''` | recherche texte (titre/description/lieu) |
| `statut` | `statut` | string | `''` | statut **temporel** : `upcoming` \| `ongoing` \| `past` (FR-015) |
| `type` | `type` | string | `''` | id catégorie `type='event'` (type d'événement, FR-015) |
| `pays` | `pays` | string | `''` | `pays_id` (FR-015) |
| `tri` | `tri` | string | `''` | `proche` (start_date asc) \| `recent` (start_date desc) (FR-016) |
| `perPage` | `per_page` | int | `config('events.per_page')` | taille de page (charger plus) |

## Méthodes

- `mount()` : initialise `perPage` depuis `config('events.per_page')` si ≤ 0.
- `updating($name, $value)` : si `$name ∈ {q, statut, type, pays, tri}` → `resetPage()` **et** `perPage = config('events.per_page')` (évite un « charger plus » gonflé persistant après changement de filtre).
- `resetFilters()` : `reset(['q','statut','type','pays','tri'])` + `resetPage()`.
- `chargerPlus()` : `perPage += config('events.per_page_step')`.
- `render()` : délègue la requête à `App\Services\Events\EventSearchService` et renvoie la vue avec le paginator + les listes de filtres (`categories` type='event', `pays`).

## Requête (via `EventSearchService`)

- Base : `Event::query()->published()` (R1 — **pas** de `->online()`), eager-load `category`, `pays`.
- `q` → `where(title|description|location like %q%)`.
- `statut` → scope temporel : `upcoming()` / `ongoing()` / `past()`.
- `type` → `byCategory((int) type)`.
- `pays` → `where('pays_id', (int) pays)`.
- `tri` → `orderBy('start_date', 'asc')` (`proche`, défaut) ou `'desc'` (`recent`). Par défaut sans filtre statut : à venir/en cours d'abord (start_date asc), passés ensuite — l'implémentation peut grouper par sections.
- Pagination : `paginate($perPage)`.

## Sortie (vue `grille-events`)

- Grille de `event-card` (composant `resources/views/components/event-card.blade.php`) : visuel (`img`), titre, date(s), lieu/format (`format_label`), badge `temporal_status_label` (ou « Annulé »), `resume_text`, CTA contextuel (matrice CTA, cf. public-routes), **compte à rebours** si `temporal_status='upcoming'`.
- Barre de filtres : select statut (`$temporalStatuses`), select type (catégories `type='event'`), select pays, select tri ; bouton « Réinitialiser ».
- Bouton « Charger plus » tant que `hasMorePages`.
- **État vide** explicite (FR-007) si aucun résultat.

## Tests de contrat (Feature/Livewire)

- Changer un filtre met à jour la liste et **réinitialise** la pagination + `perPage`.
- `statut=past` ne renvoie que des événements clos ; `statut=upcoming` que des à venir ; `statut=ongoing` que des en cours (SC-002/SC-007).
- `type`/`pays` filtrent correctement et se combinent (intersection).
- `tri=recent` inverse l'ordre par `start_date`.
- `chargerPlus()` augmente `perPage` du pas configuré.
- Les paramètres sont reflétés dans l'URL (partage/rechargement conserve l'état).
- Seuls des événements **publiés** apparaissent (FR-006).
