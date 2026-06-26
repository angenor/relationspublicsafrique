# Contract — Back-office Filament 3

Conventions du projet (cf. `FormationResource`, `EventResource`, `ProfilResource`, `AdminPanelProvider`). Auto-discovery déjà active (`discoverResources(in: app_path('Filament/Resources'))`). Disque par défaut `public`.

## `MediaResource` (`app/Filament/Resources/MediaResource.php`)
- `navigationGroup = 'Média'`, `navigationIcon = 'heroicon-o-newspaper'`, `recordTitleAttribute = 'titre'`.
- Pages : `Pages/{ListMedia, CreateMedia, EditMedia}` ; `getRelations()` → `[CommentairesRelationManager::class]`.

### Form (Sections)
1. **Contenu** : `TextInput titre` (`live(onBlur)` → `afterStateUpdated` set `slug` via `Str::slug`), `TextInput slug`, `Select type` (5 valeurs, `live()`), `Textarea chapo`, `RichEditor content`.
2. **Média** (conditionnelle via `->visible(fn (Forms\Get $get) => ...)`, **pilotée par `media_kind`, pas par `type`** — permet une interview/un reportage multimédia) :
   - `Select media_kind` (options `null|audio|youtube|vimeo` ; `live()` ; défaut `audio` si `type='podcast'`, `youtube` si `type='video'` ; proposé pour tous les types sauf usage purement texte).
   - `TextInput embed_url` (`url()`, visible si `media_kind ∈ {youtube,vimeo}`) — validé hôte YouTube/Vimeo.
   - `FileUpload audio_file` `->acceptedFileTypes(['audio/mpeg','audio/mp3','audio/wav','audio/x-m4a','audio/ogg'])->directory('media/audio')->disk('public')` (visible si `media_kind='audio'`).
   - `FileUpload subtitles_path` `->acceptedFileTypes(['text/vtt'])->directory('media/subtitles')` (visible si `media_kind` non nul).
   - `Toggle audio_downloadable` (visible si `media_kind='audio'`).
   - `TextInput duration` (secondes, visible si `media_kind` non nul).
3. **Couverture & SEO** : `FileUpload cover_image ->image()->imageEditor()->directory('media/covers')` (pattern `ProfilResource`), `TextInput meta_title`, `Textarea meta_description`, `FileUpload og_image ->image()->directory('media/og')`.
4. **Classement** : `Select categories ->relationship('categories','name')->multiple()->preload()`, `Select tags ->relationship('tags','libelle')->multiple()->preload()->createOptionForm([...])`, `Select pays_id ->relationship('pays','name')->searchable()`, `Select user_id` (auteur principal). **Co-auteurs / intervenants** : `Repeater::make('contributions')->relationship()` avec schéma `[Select user_id ->relationship('user','name')->searchable()->required(), Select role (options auteur|interviewer|invite)->required(), TextInput position]` — édite le pivot `media_authors` (rôle par ligne) via le modèle `MediaAuthor`.
5. **Série** : `Select serie_id ->relationship('serie','titre')`, `TextInput saison`, `TextInput episode`, `TextInput serie_position` (visibles si type ∈ {podcast,video}).
6. **Publication & mise en avant** : `Select status` (draft/scheduled/published/archived), `DateTimePicker published_at`, `Toggle featured`, `Toggle is_pinned`, `TextInput pinned_position`.

### Table
- `ImageColumn cover_image`, `TextColumn titre (searchable, limit)`, `BadgeColumn type`, `BadgeColumn status` (couleurs : `gray=>draft, warning=>scheduled, success=>published, danger=>archived` — pattern `EventResource`), `IconColumn featured`, `IconColumn is_pinned`, `TextColumn view`, `TextColumn published_at`.
- Filtres : `SelectFilter type`, `SelectFilter status`, `SelectFilter serie`, `SelectFilter categories (relationship)`, `TernaryFilter featured`.
- Actions ligne : `EditAction`, `DeleteAction`.
- Bulk : `DeleteBulkAction` + `BulkAction publish/archive/feature` (pattern `EventResource` `->each->...` + `requiresConfirmation()`).

## `MediaSerieResource`
- Form : `titre` (→slug), `description`, `Select type`, `FileUpload cover_image`, `Toggle online`, `TextInput position`.
- `getRelations()` → `[EpisodesRelationManager::class]`.
- `EpisodesRelationManager` (`$relationship='medias'`, pattern `RegistrationsRelationManager`) : table ordonnée par `saison`,`episode`,`serie_position` ; colonnes titre/type/status ; `headerActions([CreateAction])`, actions edit/detach, réordonnable.

## Modération des commentaires
- `CommentairesRelationManager` sous `MediaResource` : colonnes `author_name`, `body (limit)`, `BadgeColumn status`, actions `approve`/`reject` conditionnelles (`->visible(fn (MediaComment $r)=>$r->status==='pending')`, pattern `RegistrationsRelationManager::confirm`).
- `MediaCommentResource` (file globale) : `navigationGroup='Média'`, filtre par `status`, actions de modération + `DeleteAction`, badge de navigation = nombre de `pending`.

## `NewsletterSubscriberResource`
- `navigationGroup='Média'`, lecture seule majoritairement : colonnes `email`, `BadgeColumn status`, `confirmed_at` ; filtre `status` ; action export CSV ; pas de création manuelle (opt-in public).

## Tests de contrat
- création d'un média de chaque type via le form (champs conditionnels corrects).
- bulk `publish` passe `status=published` et rend visible publiquement.
- `approve` d'un commentaire le rend visible sur la page détail.
