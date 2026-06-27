# Contrat — Routes publiques (`events.*`)

Routes **existantes conservées** dans `routes/web.php` (signatures **inchangées** — non-régression). Layout : `layouts/front.blade.php`. La feature **enrichit le comportement** des contrôleurs sans modifier les URIs ni les noms de routes.

## Routes (existantes — conservées)

| Méthode | URI | Nom | Contrôleur | Évolution |
|---|---|---|---|---|
| GET | `/evenements` | `events.index` | `EventController@index` | Listing → vue Livewire `GrilleEvents` ; gate `->online()` **remplacé** par `->published()` (R1) |
| GET | `/evenements/{slug}-{id}` | `events.show` | `EventController@show` | Détail **enrichi** (objectifs/programme/intervenants/public cible + CTA dynamique) ; gate `->published()` |
| GET | `/evenements/categorie/{slug}` | `events.category` | `EventController@category` | Inchangé (gate `->published()`) |
| GET | `/evenements/calendrier` | `events.calendar` | `EventController@calendar` | Inchangé |
| GET | `/evenements/recherche` | `events.search` | `EventController@search` | Inchangé (gate `->published()`, R1) |
| POST | `/evenements/{event}/inscription` | `events.register` | `EventRegistrationController@register` | **À CRÉER** — le contrôleur existe déjà mais **aucune route ne le déclare** ; cette feature ajoute la route (parcours interne) |

> ⚠️ **Non-régression** : aucune URI/nom de route **existant** n'est modifié. La route d'inscription `events.register` est **nouvelle** (le `EventRegistrationController@register` existant n'était relié à aucune route). La section « Lomé COM'TOUR » du menu pointe sur `events.index` et reste fonctionnelle.
> ⚠️ **R1 (gate de visibilité)** : `index`, `show`, **`category`, `calendar`, `search`** doivent **tous** passer de `->online()` à `->published()` — sinon, `online` recentré sur le format masquerait les événements présentiels publiés sur ces routes.

## `GET /evenements` (index)

- **Entrée (query, gérée par Livewire `GrilleEvents`)** : `statut` (`upcoming|ongoing|past`, optionnel), `type` (id catégorie `type='event'`, optionnel), `pays` (`pays_id`, optionnel), `tri` (`proche|recent`, défaut `proche`), `q` (recherche texte, optionnel), `page`/charger-plus.
- **Logique** : `Event::published()` (R1, **plus** de `->online()`) filtré par `EventSearchService`, eager-load `category`, `pays`, (compteurs `speakers`/`medias` au besoin). Pagination `config('events.per_page')`. Tri : `proche` = `start_date asc` ; `recent` = `start_date desc`. Affichage par groupes/sections **En cours / À venir / Clos** (ou via filtre `statut`). Le contrôleur fournit les **comptes** `upcomingCount`/`ongoingCount`/`pastCount` (déjà calculés aujourd'hui — gate à mettre à jour vers `published()`).
- **Sortie** : HTML listing. Chaque card (FR-003) : `img`, `title`, `resume_text`, date(s) formatées, lieu (`format_label` : ville ou « En ligne »), **badge statut temporel** (`temporal_status_label`, ou « Annulé » si `status='cancelled'`), **CTA contextuel** (cf. matrice CTA), **compte à rebours** si `temporal_status='upcoming'` (FR-017).
- **État vide** (FR-007) : message explicite si aucun événement / aucun résultat de filtre (par groupe).
- **Exclusion** (FR-006) : les événements non publiés (`status != 'published'`) n'apparaissent jamais.

## `GET /evenements/{slug}-{id}` (show)

- **Entrée** : `slug` + `id` (signature existante conservée).
- **Logique** : `Event::published()->where('id',$id)->where('slug',$slug)->firstOrFail()` (404 si non publié/inconnu, FR-012). `increment('view')`. Eager-load `category`, `pays`, `user`, `speakers`, `medias`. Événements similaires : même `category_id`, publiés.
- **Sortie** : HTML détail dans l'ordre — bannière (titre + accroche) + visuel → date & heure + lieu/format → description complète → **objectifs** → **programme/agenda** → **intervenants** (`speakers`) → **public cible** → **CTA dynamique** → (si clos) **médias post-événement** (replay/photos/compte rendu) → événements similaires.
- **Sections optionnelles** (FR-010) : tout bloc dont la donnée/relation est vide est **masqué**.
- **Compte à rebours** (FR-017) : affiché pour un événement `upcoming` (données via `data-start` ; logique `events.js`).

## Matrice CTA (FR-004 / FR-010 / FR-011)

| État de l'événement | CTA card | CTA détail |
|---|---|---|
| `cancelled` | « Voir détails » (badge « Annulé ») | mention « Événement annulé » |
| `upcoming`/`ongoing` + `can_register` + `internal` | « S'inscrire » | « S'inscrire » → POST `events.register` |
| `upcoming`/`ongoing` + `external` (publié, non clos) | « S'inscrire » | « S'inscrire » → `registration_url` (nouvel onglet) |
| `upcoming`/`ongoing` + `internal` mais complet/deadline dépassée | « Voir détails » | mention « Complet » / « Inscriptions closes » |
| `past` + `has_post_event_media` | « Voir le replay » | « Voir replay / photos / compte rendu » |
| `past` sans média | « Voir détails » | « Voir détails » (pas de CTA replay) |

## Inscription interne (`POST /evenements/{event}/inscription`)

- **Inchangée** : `EventRegistrationController@register` (auth requise, contrôle `can_register`, anti-doublon `isUserRegistered`, `increment('current_participants')`). La **seule** évolution est la correction de `can_register` (R1/R9 : ne dépend plus de `online`).

## SEO

- `<title>`/meta depuis `title`/`resume_text` ; URL canonique stable basée sur `{slug}-{id}` (signature existante).

## Tests de contrat (Feature)

- Un événement **publié** est listé (présentiel **comme** en ligne, R1) ; un `draft`/`cancelled` (selon règle d'affichage) respecte FR-006 ; un brouillon n'est jamais listé (SC-002).
- `show` d'un brouillon / slug-id inconnu → **404** (FR-012).
- Statut temporel affiché = exact selon les dates à tout instant (SC-002/SC-004) : à venir / en cours / clos.
- Filtre `statut`/`type`/`pays` ne renvoie que les événements correspondants (SC-007).
- **CTA dynamique** conforme à la matrice : S'inscrire (interne/externe), Complet/closes, Voir replay (SC-006), Voir détails.
- Sections optionnelles absentes (objectifs/programme/intervenants/médias) → blocs masqués.
- **Inscription interne** : un utilisateur connecté éligible s'inscrit ; doublon refusé ; capacité/deadline respectées.
- **Non-régression** : `events.index/show/category/calendar/search` répondent **200** ; la page Lomé COM'TOUR (lien `events.index`) répond 200 ; le parcours d'inscription interne fonctionne comme avant.
