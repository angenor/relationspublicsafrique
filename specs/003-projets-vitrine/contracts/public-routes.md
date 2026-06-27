# Contrat — Routes publiques (`projets.*`)

Routes additives dans `routes/web.php` (aucune route existante modifiée). Layout : `layouts/front.blade.php`.

## Routes

| Méthode | URI | Nom | Contrôleur | Description |
|---|---|---|---|---|
| GET | `/projets` | `projets.index` | `ProjetController@index` | Listing en cards + filtres (Livewire `GrilleProjets`). |
| GET | `/projets/{projet:slug}` | `projets.show` | `ProjetController@show` | Page détaillée d'un projet publié. |

> Le bouton « Nous contacter » de la page détail pointe vers la route existante `contact` : `route('contact', ['projet' => $projet->slug])` → `/contact?projet={slug}` (R10). Aucune nouvelle route de contact.

## `GET /projets` (index)

- **Entrée (query, gérée par Livewire)** : `thematique` (id catégorie, optionnel), `zone` (`pays_id` ou code portée, optionnel), `statut` (`actif|realise|en_developpement`, optionnel), `tri` (`recent|alpha`, défaut `recent`), `q` (recherche texte, optionnel), `page`/charger-plus.
- **Logique** : `Projet::published()->ordered()` filtré par `ProjetSearchService`, eager-load `categories`, `pays`. Pagination `config('projets.per_page')`.
- **Sortie** : HTML listing. Chaque card (FR-002) : `visuel_card_url`, `titre`, `resume`, thématique(s), zone (pays ou `zone_libelle`), badge `statut_label`, bouton « Découvrir » → `projets.show`.
- **État vide** (FR-007) : message explicite si aucun projet / aucun résultat de filtre.
- **Exclusion** (FR-006) : les projets non publiés n'apparaissent jamais.

## `GET /projets/{slug}` (show)

- **Entrée** : `slug` du projet.
- **Logique** : résolution par route-model-binding sur `slug` **restreinte aux publiés** (`firstWhere` via scope `published()`), sinon **404** (FR-016). Eager-load `categories`, `pays`, `resultats`, `medias`, `partenaires`, `temoignages`.
- **Sortie** : HTML détail dans l'ordre — titre + visuel principal → contexte → objectifs → description détaillée → activités réalisées → chiffres clés (`resultats`) → partenaires → galerie (`medias`) → témoignages (optionnel) → bouton « Nous contacter ».
- **Sections optionnelles** (FR-012) : tout bloc dont la donnée/relation est vide est **masqué** (pas de bloc vide).
- **Galerie** (FR-014) : images (`<img>` + lightbox), vidéos (`VideoEmbed` pour `url_embed`, sinon `<video>`), tolérante aux médias indisponibles.

## SEO

- `<title>`/meta depuis `meta_titre`/`meta_description` (fallback `titre`/`resume`).
- URL canonique stable basée sur `slug` (FR-009).

## Tests de contrat (Feature)

- Un projet publié est listé ; un brouillon ne l'est pas (SC-002).
- `show` d'un brouillon / slug inconnu → 404.
- Filtre `thematique`/`zone`/`statut` ne renvoie que les projets correspondants (SC-004).
- Sections optionnelles absentes → blocs masqués (SC-006).
- Bouton « Nous contacter » → URL `/contact?projet={slug}` (SC-007).
- **Non-régression** : `/`, `/actualites`, `/media` répondent 200 inchangés.
