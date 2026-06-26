# Contract — Livewire `App\Livewire\Media\GrilleMedias`

Calqué sur `app/Livewire/Annuaire/ProfilGrid.php`. Vue : `resources/views/livewire/media/grille-medias.blade.php`.

## Propriétés publiques (toutes `#[Url]`)

| Propriété | Type | `#[Url(as:)]` | Défaut | Rôle |
|---|---|---|---|---|
| `q` | `string` | `q` | `''` | recherche titre/mot-clé/auteur |
| `type` | `string` | `type` | `''` | `article\|interview\|podcast\|video\|reportage` |
| `categorie` | `string` | `cat` | `''` | slug catégorie/thématique |
| `pays` | `string` | `pays` | `''` | slug pays |
| `auteur` | `string` | `auteur` | `''` | id/slug auteur |
| `tri` | `string` | `tri` | `''` | `recent\|populaire\|recommande` |
| `mode` | `string` | `mode` | `grille` | `grille\|liste` |
| `perPage` | `int` | `per_page` | `config('media.per_page',12)` | pagination / charger plus |

## Méthodes
- `updating($name, $value): void` — si `$name ∈ {q,type,categorie,pays,auteur,tri,mode}` → `resetPage()` (⚠️ **pas** sur `perPage`, pour préserver « charger plus »).
- `setMode(string $mode): void` — valide `in_array($mode,['grille','liste'])`.
- `resetFilters(): void` — `reset(['q','type','categorie','pays','auteur','tri'])` + `resetPage()`.
- `loadMore(): void` — `perPage += config('media.per_page_step', 12)` (sans `resetPage()`).
- `render()` — appelle `MediaSearchService::recherche([...])`, `->with([...])->paginate($perPage)` ; passe `typeOptions`, `categorieOptions`, `paysOptions`, `auteurOptions`, `triOptions`.

## `MediaSearchService::recherche(array $criteres): Builder`
Calqué sur `ProfilSearchService` :
- base : `Media::query()->published()`
- `q` → `applyTermeRecherche` : `LIKE '%mot1%mot2%'` sur `titre_normalise` + `orWhereHas('pays', name_normalise)`, `orWhereHas('tags', slug/libelle)`, `orWhereHas('categories', name)`, `orWhereHas('auteurs', name)`.
- `type` → `where('type', $type)`
- `categorie` → `whereHas('categories', slug)`
- `pays` → `whereHas('pays', slug)` (ou `pays_id`)
- `auteur` → `whereHas('auteurs')` ou `where('user_id')`
- `tri` : `recent` → `recent()` ; `populaire` → `popular()` ; `recommande` → `recommended()` ; défaut → `recent()`.

## Vue (Bootstrap, miroir `profil-grid.blade.php`)
- barre de filtres : `input[type=search] wire:model.live.debounce.400ms="q"`, `select wire:model.live` pour type/catégorie/pays/auteur/tri, bouton `wire:click="resetFilters"`, toggle grille/liste `wire:click="setMode(...)"`.
- grille : `row` + `col-12 col-sm-6 col-lg-4 col-xxl-3` → `@include('components.media-card')`.
- liste : `list-group` → `@include('components.media-ligne')`.
- état vide : `@if ($medias->total() === 0)` message + bouton réinitialiser.
- bas de liste : si `$medias->hasMorePages()` → bouton « Charger plus » `wire:click="loadMore"` (+ `wire:loading`), repli `{{ $medias->withQueryString()->links() }}`.
- accessibilité : `aria-busy` sur `wire:loading`, `aria-live="polite"` sur la zone résultats (pattern annuaire).

## Tests de contrat
- saisie `q` filtre par titre normalisé (accents-insensible).
- chaque filtre restreint le jeu ; combinaison = ET logique.
- `tri` modifie l'ordre (recent/populaire/recommande).
- `loadMore` augmente le nombre d'items sans changer de page.
- l'état est reflété dans l'URL et rechargeable (SC-010).
