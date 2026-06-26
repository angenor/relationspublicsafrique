# Contract — Routes & contrôleur public

**Surface** : site public. Conventions reprises de `routes/web.php` (`$slugPatern = '[a-z0-9\-]+'`, URLs `{slug}-{id}` avec `->where(['slug'=>$slugPatern,'id'=>'[0-9]+'])`).

> **Non-régression (FR-041)** : aucune route existante n'est modifiée. `/` (`accueil`), `/actualites` (`blog`) et `/actualites/{slug}-{id}` (`blog.show`) restent identiques.

## Routes (à ajouter dans `routes/web.php`)

| Méthode | URI | Nom | Action | Vue |
|---|---|---|---|---|
| GET | `/media` | `media.home` | `MediaController@home` | `media.home` |
| GET | `/media/explorer` | `media.index` | `MediaController@index` | `media.index` (héberge `<livewire:media.grille-medias>`) |
| GET | `/media/series/{slug}` | `media.serie` | `MediaController@serie` | `media.serie` |
| GET | `/media/{slug}-{id}` | `media.show` | `MediaController@show` | `media.show` |
| POST | `/media/{id}/commentaires` | `media.comments.store` | `MediaController@storeComment` | redirect back |
| POST | `/media/newsletter` | `media.newsletter.subscribe` | `NewsletterController@subscribe` | redirect back |
| GET | `/media/newsletter/confirmer/{token}` | `media.newsletter.confirm` | `NewsletterController@confirm` (URL signée) | vue confirmation |
| GET | `/media/newsletter/desabonnement/{token}` | `media.newsletter.unsubscribe` | `NewsletterController@unsubscribe` (URL signée) | vue désabonnement |

Contrainte de la route `media.show` :
```php
Route::get('/media/{slug}-{id}', [MediaController::class, 'show'])
    ->where(['slug' => $slugPatern, 'id' => '[0-9]+'])
    ->name('media.show');
```
> `media.show` est déclarée **après** `media.series/...` et `media.explorer` pour éviter toute collision de segment.

## `MediaController`

### `home(): View`
- **Données** :
  - `aLaUne` : `Media::published()->featured()->recommended()->take(3)->get()`
  - `dernieres` : `Media::published()->recent()->take(N)->get()`
  - `tendances` : `Media::published()->popular()->take(N)->get()`
  - `selection` : médias `is_pinned` (sélection éditoriale, ex. « Focus communicants africains »)
  - `categoriesMedia` : `Category::where('online',1)->where('type','media')->orderBy('position')->get()` (sidebar)
- **Eager-load** : `with(['categories','auteurPrincipal','serie'])`.

### `index(): View`
- Rend la page hébergeant `<livewire:media.grille-medias />` (cf. [livewire-grille-medias.md](./livewire-grille-medias.md)).

### `show(string $slug, int $id): View`
- `Media::with(['categories','tags','auteurs','auteurPrincipal','pays','serie','commentairesApprouves'])->where('id',$id)->where('slug',$slug)->published()->firstOrFail();` (validation stricte slug+id, pattern `EventController::show`).
- `increment('view')`.
- `similaires` : même `type`/catégorie/tags, `->where('id','!=',$id)->published()->take(4)`.
- `precedent`/`suivant` : par `serie` (saison/épisode) sinon par `published_at`.
- `btnShare = \Share::page($media->link, $media->titre)->facebook()->twitter()->linkedin()->whatsapp()->getRawLinks();` + lien `mailto:` construit en vue.
- Vue rend `@section('meta')` à partir de `meta_title`/`meta_description`/`og_image` (repli `titre`/`chapo`/`cover_image`).

### `serie(string $slug): View`
- `MediaSerie::where('slug',$slug)->where('online',true)->firstOrFail()` + ses `medias()` publiés (ordonnés saison/épisode), avec lecteur de playlist.

### `storeComment(Request $request, int $id): RedirectResponse`
- Valide `author_name`, `author_email`, `body` (+ honeypot vide) ; throttling `RateLimiter` par IP.
- Crée `MediaComment` `status='pending'` ; message flash « commentaire en attente de modération ».

## Réponses / statuts
- 404 si média non publié ou introuvable (`firstOrFail`).
- 200 pour toutes les pages publiques.
- 302 (redirect back + flash) pour `storeComment`, `subscribe`.
- 403 si signature invalide/expirée sur `confirm`/`unsubscribe` (middleware `signed`).

## Tests de contrat (Feature)
- `/media` renvoie 200 et contient les zones À la une / Dernières / Tendances.
- `/media/{slug}-{id}` d'un média publié → 200 ; d'un média `draft/scheduled/archived` → 404.
- `increment('view')` effectif après visite détail.
- `/` et `/actualites` inchangés (mêmes vues/contenus qu'avant).
