# Research — Section Média (002-media-newsroom)

**Date** : 2026-06-25 · **Feature** : `002-media-newsroom` · **Spec** : [spec.md](./spec.md)

Ce document consigne les décisions techniques (Phase 0). Chaque entrée suit le format **Décision / Rationale / Alternatives écartées**, ancrée dans le code réel du dépôt.

---

## R1 — Modèle de données : `Media` autonome (ne pas surcharger `Post`)

- **Décision** : créer un modèle/table `media` autonome plutôt que d'étendre `Post`.
- **Rationale** : `app/Models/Post.php` est verrouillant pour un média éditorial — `type` est un enum **fermé** en propriété statique (`sliders/bienvenue/evenements/faq/blog`) consommé par 3 surfaces (`PostController::index`, `PostController::blog`, `BlogResource`) ; `Post::resume()` **écrase** toute valeur stockée par `Str::limit($this->content,200)` (incompatible avec un chapô maîtrisé, cf. `app/Http/Controllers/PostController.php`) ; `Post::link()` est figé sur `route('blog.show')` ; aucun champ audio, durée, SEO, featured, scheduling. La décision a été validée avec le demandeur.
- **Alternatives écartées** : (a) étendre `Post` → pollue les surfaces existantes et risque de régression sur `/` et `/actualites` (interdit par FR-041) ; (b) `Post` polymorphe → sur-ingénierie pour un besoin distinct.

## R2 — Génération du slug : réutiliser le trait `App\Helper\Sluggable`

- **Décision** : `Media` et `MediaSerie` utilisent le trait existant `App\Helper\Sluggable` ; la colonne titre est **`titre`**. Comme le trait dérive le slug de `$this->name` (mutateur `setSlugAttribute`), `Media` ajoute un hook `creating` : si `slug` vide → `slug = Str::slug($this->titre)`. Côté Filament, le slug s'auto-remplit depuis `titre` via `afterStateUpdated` (pattern `PostResource`).
- **Rationale** : `app/Helper/Sluggable.php` n'agit que lorsqu'on **assigne** `slug` et retombe sur `$this->name`. Conserver le nommage métier `titre` tout en garantissant un slug en création programmatique (seeders/tests) impose le hook `creating`. URLs canoniques `{slug}-{id}` cohérentes avec le reste du site.
- **Alternatives écartées** : nommer la colonne `name` pour réutiliser le trait verbatim → casse la lisibilité métier ; package spatie/sluggable → dépendance superflue, incohérente avec l'existant.

## R3 — Recherche & normalisation : réutiliser `TextNormalizer` + colonne `titre_normalise`

- **Décision** : créer `App\Services\Media\MediaSearchService` calqué sur `app/Services/Annuaire/ProfilSearchService.php`. Ajouter une colonne `media.titre_normalise` (indexée) renseignée via `App\Services\Annuaire\TextNormalizer::normalize()` (à la création/mise à jour, + backfill de migration comme `2026_05_11_000001_add_name_normalise_to_pays...`). La recherche fait `LIKE '%mot1%mot2%'` sur `titre_normalise` et les relations (`pays.name_normalise`, `tags.libelle/slug`, `categories.name`, auteurs `users.name`).
- **Rationale** : pattern éprouvé en 001 (`ProfilSearchService::applyTermeRecherche`), recherche insensible aux accents sans dépendance externe. `pays.name_normalise` existe déjà.
- **Alternatives écartées** : Laravel Scout/Meilisearch → infra supplémentaire non justifiée à ce volume ; FULLTEXT MySQL → non portable SQLite (tests in-memory).

## R4 — Grille & pagination réactive : cloner le patron Livewire `ProfilGrid`

- **Décision** : créer `App\Livewire\Media\GrilleMedias` (trait `WithPagination`), propriétés publiques `#[Url]` : `q`, `type`, `categorie`, `pays`, `auteur`, `tri` (`recent|populaire|recommande`), `mode` (grille/liste), `perPage`. `render()` appelle `MediaSearchService` puis `paginate($perPage)`. Pour « Charger plus » : méthode `loadMore()` qui incrémente `perPage` (sans `resetPage()`), bouton + option `IntersectionObserver` (polish). La pagination classique `->withQueryString()->links()` reste le repli.
- **Rationale** : `app/Livewire/Annuaire/ProfilGrid.php` fournit un patron complet (filtres `#[Url]`, `wire:model.live.debounce.400ms`, `updating()` → `resetPage()`, toggle mode). L'annuaire n'a **pas** de « charger plus » (gap connu) ; on l'ajoute proprement par incrément de `perPage`.
- **Alternatives écartées** : pagination cursor → inutile à ce volume ; scroll infini pur sans repli paginé → moins accessible/SEO.

## R5 — Lecteur audio podcast : **Plyr** via une entrée Vite dédiée

- **Décision** : ajouter `plyr` (npm). Créer une entrée Vite dédiée `resources/js/media.js` (+ `resources/css/media.css`) ajoutée à `vite.config.js`, chargée **uniquement** par le layout média via `@vite([...])`. Plyr pilote `<audio>` (podcasts) et les `<iframe>` YouTube/Vimeo (vidéos), avec pistes de sous-titres.
- **Rationale** : `package.json` ne contient ni Plyr ni Video.js/HLS.js. Plyr est léger, accessible (clavier), gère audio + YouTube/Vimeo + captions, et s'intègre à Vite. Une entrée dédiée évite de charger Plyr sur tout le site (le front charge `css2/app.css` + `js2/app.js`).
- **Alternatives écartées** : `<audio>`/`<video>` natifs sans habillage → UI incohérente, captions moins maîtrisées ; Video.js + hls.js → lourd et inutile (pas de HLS, vidéos en embed).

## R6 — Vidéos : embed YouTube/Vimeo (pas d'hébergement interne)

- **Décision** : champ `embed_url` (+ `media_kind` ∈ `youtube|vimeo`). Un helper `App\Helper\VideoEmbed` extrait l'ID et produit l'URL d'`<iframe>` ; rendu responsive (ratio 16:9). Aucun upload vidéo, aucun FFmpeg, aucun disque `videos`.
- **Rationale** : décision produit validée ; le pipeline FFmpeg existant (`app/Jobs/ConvertVideoForStreaming`) est vidéo-cours, mono-bitrate, et le disque `videos` n'est même pas déclaré dans `config/filesystems.php`. L'embed est l'approche de brut.media.
- **Alternatives écartées** : upload + HLS interne → hors périmètre, gaps techniques ; oEmbed dynamique → dépendance réseau au rendu, surdimensionné.

## R7 — Sous-titres : fichier `.vtt` + `<track>` / captions Plyr

- **Décision** : champ `subtitles_path` (upload `.vtt`), exposé via `<track kind="captions" srclang="fr" src=...>` et l'option `captions` de Plyr. Pour les vidéos embarquées, les sous-titres restent gérés côté plateforme (YouTube/Vimeo) ; `subtitles_path` s'applique surtout aux podcasts et vidéos non-embed.
- **Rationale** : WebVTT est le standard HTML5 ; Plyr le supporte nativement.
- **Alternatives écartées** : SRT (non natif navigateur) → conversion superflue.

## R8 — Publication programmée : scope requête + commande planifiée

- **Décision** : la **visibilité** publique repose sur un scope `scopePublished()` = `status = 'published' AND published_at <= now()` (robuste même sans cron). En complément, une commande `media:publish-scheduled` (planifiée `everyMinute()`) bascule `status` `scheduled → published` à l'échéance, met à jour l'état affiché en admin et **déclenche les alertes newsletter**. Une commande `media:recompute-popularity` (planifiée `hourly`/`daily`) recalcule `popularity_score`.
- **Rationale** : `app/Console/Kernel.php::schedule()` est **actif** (déjà `annuaire:envoyer-rappels-consentement`->daily()), donc ajouter des commandes est cohérent (le cron `schedule:run` doit tourner en prod — exigence ops). Le double dispositif (scope + commande) garantit l'exactitude visuelle ET la robustesse si le cron est en retard.
- **Alternatives écartées** : flip uniquement par commande (sans scope) → contenu visible en retard si cron en panne ; visibilité uniquement par scope (sans commande) → pas de déclencheur fiable pour la newsletter.

## R9 — Newsletter : table d'abonnés + opt-in par URL signée + notification à la publication

- **Décision** : table `newsletter_subscribers` (email unique, `token`, `status` pending/confirmed/unsubscribed). Confirmation et désabonnement via **URL signée** (`URL::temporarySignedRoute`, pattern `ProfilPubliePersonneNotification`). À la publication d'un média, un `MediaObserver`/évènement envoie `NewMediaPublishedNotification` (canal `mail`, `Queueable`) à `Notification::route('mail', $email)` pour chaque abonné confirmé. Protection anti-doublon via `email` unique ; honeypot sur le formulaire d'abonnement.
- **Rationale** : réutilise le pattern Notifications existant (`app/Notifications/*`, `via()=>['mail']`, `Queueable`) et MailHog en local (`docker-compose.yml`). Pas d'infra newsletter existante → table minimale.
- **Alternatives écartées** : Mailchimp/Sendinblue → dépendance externe et RGPD plus lourd ; envoi synchrone in-request → risque de timeout sur gros volumes.

## R10 — Commentaires : table dédiée `media_comments` + modération + honeypot

- **Décision** : table `media_comments` (FK `media_id`, `parent_id` nullable pour réponses futures, `author_name`, `author_email`, `body`, `status` pending/approved/rejected, `ip_address`). Affichage public filtré sur `status='approved'`. Modération via Filament (RelationManager sous `MediaResource` + ressource dédiée pour la file globale). Anti-spam : champ **honeypot** + throttling (`RateLimiter`).
- **Rationale** : aucune infra de commentaires ni anti-spam n'existe (`app/Livewire/Contactform.php` ne fait que `dd()`). Table dédiée (non polymorphe) suffisante et simple à modérer.
- **Alternatives écartées** : commentaires polymorphes multi-entités → YAGNI ; Disqus/externe → perte de contrôle/RGPD ; captcha tiers → dépendance ; honeypot suffit en v1.

## R11 — Popularité & tendances

- **Décision** : `media.view` incrémenté sur la page détail (`increment('view')`, pattern `EventController::show`). `media.popularity_score` = vues pondérées par la fraîcheur, recalculé par `media:recompute-popularity`. Tri « populaire/tendances » = `ORDER BY popularity_score DESC` ; « recommandé » = pondération `featured`/`is_pinned` + `popularity_score` ; égalité départagée par `published_at DESC` (déterministe).
- **Rationale** : simple, sans dépendance ; aligné sur l'incrément de vues déjà pratiqué.
- **Alternatives écartées** : analytics tiers → surdimensionné ; calcul temps réel à chaque requête → coûteux.

## R12 — Partage social : `\Share` + lien email

- **Décision** : réutiliser `\Share::page($media->link, $media->titre)->facebook()->twitter()->linkedin()->whatsapp()->getRawLinks()` (pattern `PostController::shoqBlog`). Le **partage par email** (demandé) est ajouté via un simple lien `mailto:` (le package `jorenvanhocht/laravel-share` ne fournit pas de service email).
- **Rationale** : `config/laravel-share.php` active facebook/twitter/linkedin/whatsapp ; X = bouton twitter. Lien `mailto:` natif, sans dépendance.
- **Alternatives écartées** : étendre le package pour l'email → effort disproportionné.

## R13 — Layout « menu à gauche » minimaliste : nouveau `layouts/media.blade.php`

- **Décision** : créer un **nouveau** layout `resources/views/layouts/media.blade.php` (flex 2 colonnes : `<aside>` sidebar gauche fixe + colonne principale scrollable), composant `resources/views/components/media-sidebar.blade.php` (logo, navigation par type, thématiques). Réutilise `@livewireStyles/@livewireScripts`, `theme-variables.css` (couleurs `#713d0b`), Bootstrap 5 (grille `col-*`, comme l'annuaire) + l'entrée Vite média dédiée. Sur mobile : sidebar repliable (burger). Une entrée « Média » → `route('media.home')` est ajoutée à la nav existante (`layouts/front.blade.php`).
- **Rationale** : `layouts/default.blade.php` et `front.blade.php` sont **identiques** et chargés de scripts globaux (jQuery, slick, wow, magnific) inadaptés à un design épuré ; partir d'un layout neuf évite la dette et le double CSRF/charset constatés. L'annuaire prouve que Bootstrap + theme-variables suffisent.
- **Alternatives écartées** : réutiliser `default.blade.php` → impose la nav horizontale lourde, contraire au brief « menu à gauche minimaliste » ; tout-Tailwind → `tailwind.config.js` a `theme.extend` vide et le front est Bootstrap, incohérent.

## R14 — SEO / Open Graph par champs dédiés

- **Décision** : champs `meta_title`, `meta_description`, `og_image` sur `media`, rendus dans `@section('meta')` de la page détail (pattern `posts/show.blade.php`), avec repli sur `titre`/`chapo`/`cover_image`. URL canonique `{slug}-{id}`.
- **Rationale** : `posts/show.blade.php` dérive l'OG du `content` (`Str::limit(...,50)`) — médiocre ; des champs dédiés donnent un contrôle éditorial (FR-013/SC-007).
- **Alternatives écartées** : package SEO (artesaos/seotools) → dépendance non nécessaire.

## R15 — Tests : PHPUnit, miroir de `tests/*/Annuaire`

- **Décision** : `tests/Feature/Media/*` (parcours public, recherche/filtres, visibilité par statut, commentaires/modération, newsletter, non-régression `/` et `/actualites`) et `tests/Unit/Media/*` (MediaSearchService, slug, popularité, VideoEmbed, reading time). SQLite in-memory.
- **Rationale** : la feature 001 a `tests/Feature/Annuaire` et `tests/Unit/Annuaire` → même organisation. `phpunit.xml` déjà configuré.
- **Alternatives écartées** : Pest → non utilisé dans le projet.

## R16 — Stockage des fichiers : disque `public` existant

- **Décision** : disque `public` (déjà défaut Filament). Répertoires : `media/covers`, `media/audio`, `media/subtitles`, `media/og`, `media/series`. `intervention/image` (présent) pour optimiser les couvertures si besoin (optionnel v1).
- **Rationale** : pattern `FormationResource`/`ProfilResource` (`->disk('public')->directory(...)`). Pas de vidéo interne → pas de disque `videos`.
- **Alternatives écartées** : S3 → non configuré, hors périmètre.

## R17 — Back-office Filament 3

- **Décision** : `MediaResource` (sections de formulaire **conditionnelles** selon `type`/`media_kind` via `->visible(fn (Forms\Get $get) => ...)`), `MediaSerieResource` + `RelationManagers/EpisodesRelationManager` (épisodes ordonnés, pattern `RegistrationsRelationManager`), modération des commentaires (RelationManager + ressource globale), ressource `NewsletterSubscriberResource` (lecture/export). Uploads via `FileUpload` (cover image ; audio `acceptedFileTypes(['audio/mpeg','audio/mp3','audio/wav','audio/x-m4a','audio/ogg'])->directory('media/audio')` ; `.vtt`). Status en `BadgeColumn` (pattern `EventResource`), `Toggle` featured/pinned, bulk actions publish/archive/feature.
- **Rationale** : patrons concrets disponibles (`FormationResource` upload+tags+sections, `EventResource` badges+bulk+RelationManager, `ProfilResource` upload image). La visibilité conditionnelle `Get` est standard Filament 3 (non encore utilisée dans le repo mais supportée).
- **Alternatives écartées** : controllers `Admin/*` legacy → la convention du projet privilégie Filament (CLAUDE.md).

## R18 — Durée & temps de lecture

- **Décision** : `duration` (secondes) saisie par l'éditeur pour audio/vidéo (v1). `reading_time` (minutes) calculé automatiquement à l'enregistrement à partir du nombre de mots de `content` (~200 mots/min) via le `MediaObserver`. Affichage humanisé par accessors (`durationHuman`, `readingTimeHuman`).
- **Rationale** : pas de pipeline d'analyse média (vidéos en embed) ; calcul du temps de lecture trivial et fiable.
- **Alternatives écartées** : extraction automatique de durée audio (getID3/FFProbe) → ajout d'outil ; reportable en polish.

---

## Synthèse des dépendances nouvelles

| Élément | Nature | Justification |
|---|---|---|
| `plyr` (npm) | Front | Lecteur audio/vidéo accessible (R5) |
| Entrée Vite `resources/js/media.js` + `resources/css/media.css` | Build | Charge Plyr/JS média de façon scopée (R5/R13) |
| `config/media.php` | Config | `per_page`, pondérations popularité, throttle (miroir `config/annuaire.php`) |
| Commandes `media:publish-scheduled`, `media:recompute-popularity` | Console | Scheduling & tendances (R8/R11) |
| (Prod) driver queue `database`/`redis` + table `jobs` | Ops | Notifications newsletter asynchrones (R9) — `sync` en local OK |
| (Ops) cron `schedule:run` | Ops | Requis pour R8/R11 (déjà nécessaire pour l'annuaire) |

Aucun `[NEEDS CLARIFICATION]` ne subsiste : les choix produits ont été tranchés avec le demandeur et consignés ci-dessus et dans `spec.md` (Assumptions).
