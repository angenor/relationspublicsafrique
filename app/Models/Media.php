<?php

declare(strict_types=1);

namespace App\Models;

use App\Helper\Sluggable;
use App\Helper\VideoEmbed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Contenu de la section Média (article, interview, podcast, vidéo, reportage).
 *
 * @property int $id
 * @property string $titre
 * @property string $slug
 * @property string|null $titre_normalise
 * @property string $type
 * @property string|null $media_kind
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $published_at
 */
class Media extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;

    protected $table = 'media';

    protected $guarded = ['id'];

    protected $casts = [
        'featured' => 'boolean',
        'is_pinned' => 'boolean',
        'audio_downloadable' => 'boolean',
        'published_at' => 'datetime',
        'duration' => 'integer',
        'reading_time' => 'integer',
        'view' => 'integer',
        'popularity_score' => 'integer',
        'pinned_position' => 'integer',
        'saison' => 'integer',
        'episode' => 'integer',
        'serie_position' => 'integer',
    ];

    /** Types de contenu autorisés (sélecteur admin / filtres). */
    public static array $types = [
        'article' => 'Article',
        'interview' => 'Interview',
        'podcast' => 'Podcast',
        'video' => 'Vidéo',
        'reportage' => 'Reportage',
    ];

    /** Nature média indépendante du type. */
    public static array $kinds = [
        'audio' => 'Audio (podcast)',
        'youtube' => 'Vidéo YouTube',
        'vimeo' => 'Vidéo Vimeo',
    ];

    /** Statuts du cycle de publication. */
    public static array $statuses = [
        'draft' => 'Brouillon',
        'scheduled' => 'Programmé',
        'published' => 'Publié',
        'archived' => 'Archivé',
    ];

    // ----- Relations -----

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_media')
            ->withPivot('position')
            ->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'media_tag');
    }

    /** Co-auteurs / intervenants (lecture/affichage). */
    public function auteurs(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'media_authors')
            ->withPivot('role', 'position')
            ->withTimestamps();
    }

    /** Édition du pivot (rôle par ligne) via Repeater Filament. */
    public function contributions(): HasMany
    {
        return $this->hasMany(MediaAuthor::class)->orderBy('position');
    }

    public function auteurPrincipal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pays(): BelongsTo
    {
        return $this->belongsTo(Pays::class);
    }

    public function serie(): BelongsTo
    {
        return $this->belongsTo(MediaSerie::class, 'serie_id');
    }

    public function commentaires(): HasMany
    {
        return $this->hasMany(MediaComment::class);
    }

    public function commentairesApprouves(): HasMany
    {
        return $this->hasMany(MediaComment::class)->where('status', 'approved');
    }

    // ----- Scopes -----

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function scopePinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true)->orderBy('pinned_position');
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc('published_at');
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->orderByDesc('popularity_score')->orderByDesc('published_at');
    }

    public function scopeRecommended(Builder $query): Builder
    {
        return $query->orderByDesc('is_pinned')
            ->orderByDesc('featured')
            ->orderByDesc('popularity_score')
            ->orderByDesc('published_at');
    }

    // ----- Accesseurs -----

    public function link(): Attribute
    {
        return Attribute::make(
            get: fn () => route('media.show', ['id' => $this->id, 'slug' => $this->slug]),
        );
    }

    public function img(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cover_image
                ? asset('storage/'.$this->cover_image)
                : asset('logos/logo1.png'),
        );
    }

    public function ogImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->og_image
                ? asset('storage/'.$this->og_image)
                : $this->img,
        );
    }

    public function audioUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->audio_file ? asset('storage/'.$this->audio_file) : null,
        );
    }

    public function subtitlesUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->subtitles_path ? asset('storage/'.$this->subtitles_path) : null,
        );
    }

    public function durationHuman(): Attribute
    {
        return Attribute::make(get: function () {
            $d = (int) $this->duration;
            if ($d <= 0) {
                return null;
            }
            $h = intdiv($d, 3600);
            $m = intdiv($d % 3600, 60);
            $s = $d % 60;

            return $h > 0 ? sprintf('%dh %02dmin', $h, $m) : sprintf('%d:%02d', $m, $s);
        });
    }

    public function readingTimeHuman(): Attribute
    {
        return Attribute::make(
            get: fn () => ((int) $this->reading_time) > 0 ? $this->reading_time.' min de lecture' : null,
        );
    }

    public function embedHtml(): Attribute
    {
        return Attribute::make(
            get: fn () => VideoEmbed::iframe($this->embed_url, (string) $this->titre),
        );
    }

    // ----- Actions (bulk admin) -----

    public function publish(): void
    {
        $this->status = 'published';
        // « Publier maintenant » : rend visible immédiatement (date vide ou future).
        if (empty($this->published_at) || $this->published_at->isFuture()) {
            $this->published_at = now();
        }
        $this->save();
    }

    public function archive(): void
    {
        $this->status = 'archived';
        $this->save();
    }

    public function feature(bool $state = true): void
    {
        $this->featured = $state;
        $this->save();
    }
}
