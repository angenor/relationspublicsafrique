# Data Model — Section Média (002-media-newsroom)

**Date** : 2026-06-25 · **Spec** : [spec.md](./spec.md) · **Recherche** : [research.md](./research.md)

Toutes les migrations sont **additives** (aucune table existante modifiée, hormis l'ajout d'index si nécessaire). Conventions du projet respectées : `foreignId(...)->constrained()->cascadeOnDelete()` (cf. `profil_tag`), `foreignIdFor(...)` (cf. `category_post`), colonnes `*_normalise` indexées (cf. `pays.name_normalise`), trait `App\Helper\Sluggable`, `App\Services\Annuaire\TextNormalizer`.

---

## Vue d'ensemble des entités

```
media ──< category_media >── categories (existant)
  │  ├──< media_tag >── tags (existant)
  │  ├──< media_authors >── users (existant)   (rôle: auteur/interviewer/invite)
  │  ├──< media_comments
  │  ├──> media_series (saison/épisode/position)
  │  ├──> pays (existant, nullable)
  │  └──> users (auteur principal, nullable)
newsletter_subscribers (indépendant)
```

---

## Table `media`

Modèle : `App\Models\Media` (traits `Sluggable`, `HasFactory`, `SoftDeletes`).

| Colonne | Type (migration) | Notes |
|---|---|---|
| `id` | `id()` | |
| `titre` | `string` | titre éditorial |
| `slug` | `string` unique | via `Sluggable` (source `titre`, hook `creating`) |
| `titre_normalise` | `string(200)` nullable, **index** `idx_media_titre_norm` | recherche sans accents (`TextNormalizer`) |
| `type` | `string(20)`, **index** | `article` \| `interview` \| `podcast` \| `video` \| `reportage` |
| `chapo` | `text` nullable | chapô éditorial **stocké** (≠ `Post::resume`) |
| `content` | `longText` nullable | corps riche / transcription |
| `cover_image` | `string` nullable | couverture (disque `public`, `media/covers`) |
| `media_kind` | `string(16)` nullable | `audio` \| `youtube` \| `vimeo` — **pilote les contrôles média indépendamment du `type`** (une interview / un reportage peut porter audio ou vidéo) |
| `embed_url` | `string` nullable | URL YouTube/Vimeo (type=video) |
| `audio_file` | `string` nullable | MP3 (type=podcast, `media/audio`) |
| `subtitles_path` | `string` nullable | `.vtt` (`media/subtitles`) |
| `audio_downloadable` | `boolean` default `false` | autorise le téléchargement audio |
| `duration` | `unsignedInteger` nullable | secondes (audio/vidéo) |
| `reading_time` | `unsignedInteger` nullable | minutes (calculé via observer) |
| `featured` | `boolean` default `false`, **index** | « À la une » |
| `is_pinned` | `boolean` default `false` | épinglage |
| `pinned_position` | `unsignedInteger` nullable | ordre d'épinglage |
| `meta_title` | `string` nullable | SEO |
| `meta_description` | `string(500)` nullable | SEO |
| `og_image` | `string` nullable | aperçu social (`media/og`) |
| `status` | `string(16)` default `draft`, **index** | `draft` \| `scheduled` \| `published` \| `archived` |
| `published_at` | `timestamp` nullable, **index** | date de publication (programmable) |
| `view` | `unsignedBigInteger` default `0` | compteur de consultations |
| `popularity_score` | `unsignedBigInteger` default `0`, **index** | tri tendances (recalc. commande) |
| `serie_id` | `foreignId` nullable → `media_series` `nullOnDelete` | série/playlist |
| `saison` | `unsignedInteger` nullable | saison dans la série |
| `episode` | `unsignedInteger` nullable | n° d'épisode |
| `serie_position` | `unsignedInteger` nullable | ordre dans la série |
| `pays_id` | `foreignId` nullable → `pays` `nullOnDelete` | filtre géographique |
| `user_id` | `foreignId` nullable → `users` `nullOnDelete` | auteur principal |
| `timestamps` | | |
| `softDeletes` | `deleted_at` | |

**Index composé** : `index(['status','published_at'])` (`idx_media_status_pub`) pour le scope `published()`.

### Casts
`featured`, `is_pinned`, `audio_downloadable` → `boolean` ; `published_at` → `datetime` ; `duration`, `reading_time`, `view`, `popularity_score`, `pinned_position`, `saison`, `episode`, `serie_position` → `integer`.

### Relations (Eloquent)
- `categories()` : `belongsToMany(Category, 'category_media')->withPivot('position')->withTimestamps()`
- `tags()` : `belongsToMany(Tag, 'media_tag')`
- `auteurs()` : `belongsToMany(User, 'media_authors')->withPivot('role','position')->withTimestamps()` (lecture/affichage)
- `contributions()` : `hasMany(MediaAuthor)->orderBy('position')` (édition admin du pivot avec rôle, via `Repeater`)
- `auteurPrincipal()` : `belongsTo(User, 'user_id')`
- `pays()` : `belongsTo(Pays)`
- `serie()` : `belongsTo(MediaSerie, 'serie_id')`
- `commentaires()` : `hasMany(MediaComment)`
- `commentairesApprouves()` : `hasMany(MediaComment)->where('status','approved')`

### Scopes
- `scopePublished($q)` : `where('status','published')->where('published_at','<=', now())`
- `scopeFeatured($q)` : `where('featured', true)`
- `scopePinned($q)` : `where('is_pinned', true)->orderBy('pinned_position')`
- `scopeOfType($q, string $type)`
- `scopeRecent($q)` : `orderByDesc('published_at')`
- `scopePopular($q)` : `orderByDesc('popularity_score')`
- `scopeRecommended($q)` : `orderByDesc('is_pinned')->orderByDesc('featured')->orderByDesc('popularity_score')->orderByDesc('published_at')`

### Accessors
- `link` : `route('media.show', ['id'=>$this->id,'slug'=>$this->slug])` (pattern `Post::link`)
- `coverUrl` / `img` : `asset('storage/'.$this->cover_image)` avec repli sur une image par défaut
- `audioUrl` : `asset('storage/'.$this->audio_file)`
- `durationHuman` : `mm:ss` / `h min`
- `readingTimeHuman` : `"{n} min de lecture"`
- `embedHtml` : via `App\Helper\VideoEmbed::iframe($this->embed_url)` (YouTube/Vimeo)

### Hooks (MediaObserver)
- `creating` : si `slug` vide → `Str::slug($titre)`.
- `saving` : `titre_normalise = TextNormalizer::normalize($titre)` ; `reading_time = max(1, round(str_word_count(strip_tags($content)) / 200))` quand `content` présent.
- `saved` (transition vers `published`) : déclenche `NewMediaPublishedNotification` aux abonnés confirmés (idempotent : ne notifie qu'au passage `!=published → =published`).

### Règles de validation (Form Request public minimal + Filament)
- `titre` requis ; `type` ∈ enum ; `slug` unique ; `status` ∈ enum.
- `media_kind` ∈ {null, audio, youtube, vimeo} — **disponible pour tout type** (article/interview/podcast/vidéo/reportage) ; `type=podcast` ⇒ défaut `audio` ; `type=video` ⇒ défaut `youtube`/`vimeo`.
- Si `media_kind ∈ {youtube, vimeo}` : `embed_url` requis et hôte ∈ {youtube.com, youtu.be, vimeo.com}.
- Si `media_kind = audio` : `audio_file` requis (mime audio).
- `published_at` requis si `status ∈ {scheduled, published}`.
- `subtitles_path` : extension `.vtt` (lorsque `media_kind` est défini).

### Transitions d'état (`status`)
```
draft ──(publier maintenant)──> published         (published_at = now() si vide)
draft ──(programmer)──────────> scheduled          (published_at futur requis)
scheduled ──(échéance, cmd)───> published          (déclenche newsletter)
published ──(archiver)────────> archived
archived ──(restaurer)────────> draft | published
```
Visibilité publique ⇔ `status=published AND published_at<=now()`.

---

## Table `media_series`

Modèle : `App\Models\MediaSerie` (traits `Sluggable`, `HasFactory`).

| Colonne | Type | Notes |
|---|---|---|
| `id` | `id()` | |
| `titre` | `string` | |
| `slug` | `string` unique | `Sluggable` |
| `titre_normalise` | `string(200)` nullable index | recherche |
| `description` | `text` nullable | |
| `cover_image` | `string` nullable | `media/series` |
| `type` | `string(16)` nullable | `podcast` \| `video` \| `mixte` |
| `online` | `boolean` default `true` | publication de la série |
| `position` | `unsignedInteger` default `0` | ordre d'affichage |
| `timestamps` | | |

Relations : `medias()` : `hasMany(Media, 'serie_id')->orderBy('saison')->orderBy('episode')->orderBy('serie_position')`.

---

## Table pivot `category_media`

Patron : `category_post` (cf. `2023_08_26_165106_create_category_posts_table.php`), enrichi d'une `position`.

| Colonne | Type |
|---|---|
| `id` | `id()` |
| `category_id` | `foreignId`→`categories` `cascadeOnDelete` |
| `media_id` | `foreignId`→`media` `cascadeOnDelete` |
| `position` | `unsignedInteger` default `0` |
| `timestamps` | |

Contrainte : `unique(['category_id','media_id'])`.

> Les **catégories** réutilisent la table existante `categories` (`name, slug, type, parent_id, online, position`). Une thématique média peut être marquée `type='media'` pour la lister dans la sidebar (non destructif).

---

## Table pivot `media_tag`

Patron exact : `profil_tag` (cf. `2026_05_10_000005_create_profil_tag_pivot.php`).

| Colonne | Type |
|---|---|
| `media_id` | `foreignId`→`media` `cascadeOnDelete` |
| `tag_id` | `foreignId`→`tags` `cascadeOnDelete` |

Clé primaire composite : `primary(['media_id','tag_id'])`. **Réutilise la table `tags` existante** (`libelle`, `slug` unique).

---

## Table pivot `media_authors`

| Colonne | Type | Notes |
|---|---|---|
| `id` | `id()` | |
| `media_id` | `foreignId`→`media` `cascadeOnDelete` | |
| `user_id` | `foreignId`→`users` `cascadeOnDelete` | |
| `role` | `string(16)` default `auteur` | `auteur` \| `interviewer` \| `invite` |
| `position` | `unsignedInteger` default `0` | ordre d'affichage |
| `timestamps` | | |

Contrainte : `unique(['media_id','user_id','role'])`. L'auteur principal reste `media.user_id` ; `media_authors` porte les co-auteurs/intervenants. Un modèle pivot léger `App\Models\MediaAuthor` expose ce pivot en `hasMany` (`Media::contributions()`) pour permettre l'édition du `role` via un `Repeater` Filament.

---

## Table `media_comments`

Modèle : `App\Models\MediaComment` (trait `HasFactory`).

| Colonne | Type | Notes |
|---|---|---|
| `id` | `id()` | |
| `media_id` | `foreignId`→`media` `cascadeOnDelete` | |
| `parent_id` | `foreignId` nullable → `media_comments` `nullOnDelete` | réponses (v-future) |
| `author_name` | `string(120)` | |
| `author_email` | `string` | non affiché publiquement |
| `body` | `text` | |
| `status` | `string(16)` default `pending`, **index** | `pending` \| `approved` \| `rejected` |
| `ip_address` | `string(45)` nullable | anti-abus |
| `timestamps` | | |

Index : `index(['media_id','status'])`. Affichage public : `status='approved'`. Modération via Filament.

**Validation soumission** : `author_name`, `author_email` (email), `body` requis ; champ **honeypot** vide ; throttling `RateLimiter` (clé IP). État initial `pending`.

---

## Table `newsletter_subscribers`

Modèle : `App\Models\NewsletterSubscriber` (trait `HasFactory`).

| Colonne | Type | Notes |
|---|---|---|
| `id` | `id()` | |
| `email` | `string` **unique** | anti-doublon (FR-035) |
| `token` | `string(64)` unique | confirmation / désabonnement |
| `status` | `string(16)` default `pending`, index | `pending` \| `confirmed` \| `unsubscribed` |
| `confirmed_at` | `timestamp` nullable | |
| `timestamps` | | |

Transitions : `pending ──(URL signée confirmée)──> confirmed ──(URL désabo.)──> unsubscribed`. Seuls les `confirmed` reçoivent `NewMediaPublishedNotification`.

---

## Référentiels réutilisés (non modifiés)

| Table | Réutilisation |
|---|---|
| `categories` | thématiques (M2M via `category_media`) ; `parent_id` hiérarchique |
| `tags` (`libelle`,`slug`) | mots-clés (M2M via `media_tag`) |
| `pays` (`name`,`name_normalise`) | filtre géographique (`media.pays_id`) ; recherche normalisée |
| `users` (`name`,`email`) | auteur principal (`media.user_id`) + co-auteurs (`media_authors`) |

---

## Indices & performance

- `idx_media_status_pub (status, published_at)` — listing public/scope `published()`.
- `idx_media_titre_norm`, `idx_media_type`, `idx_media_featured`, `idx_media_popularity` — filtres & tris.
- Eager-loading systématique dans le service/contrôleur : `with(['categories','tags','auteurs','auteurPrincipal','pays','serie'])` (évite N+1, pattern `ProfilGrid::render`).

## Seeders & factories

- `MediaFactory`, `MediaSerieFactory`, `MediaCommentFactory`, `NewsletterSubscriberFactory`.
- `MediaSeeder` idempotent : crée catégories `type='media'` (thématiques : communication politique/corporate/digitale…), quelques séries, et ≥ 1 contenu par type avec statuts variés (dont 1 `scheduled` et 1 `archived`) + ≥ 1 « featured » et 1 « pinned » pour démontrer les rubriques d'accueil.
