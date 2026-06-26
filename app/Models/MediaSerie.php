<?php

declare(strict_types=1);

namespace App\Models;

use App\Helper\Sluggable;
use App\Services\Annuaire\TextNormalizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Série / playlist de contenus média (podcast ou vidéo), ordonnée saison/épisode.
 */
class MediaSerie extends Model
{
    use HasFactory;
    use Sluggable;

    protected $table = 'media_series';

    protected $guarded = ['id'];

    protected $casts = [
        'online' => 'boolean',
        'position' => 'integer',
    ];

    /** Natures de série. */
    public static array $types = [
        'podcast' => 'Podcast',
        'video' => 'Vidéo',
        'mixte' => 'Mixte',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $serie): void {
            if (empty($serie->slug)) {
                $serie->slug = Str::slug((string) $serie->titre);
            }
        });

        static::saving(function (self $serie): void {
            $serie->titre_normalise = TextNormalizer::normalize($serie->titre);
        });
    }

    public function medias(): HasMany
    {
        return $this->hasMany(Media::class, 'serie_id')
            ->orderBy('saison')
            ->orderBy('episode')
            ->orderBy('serie_position');
    }

    public function scopeOnline(Builder $query): Builder
    {
        return $query->where('online', true);
    }

    public function link(): Attribute
    {
        return Attribute::make(
            get: fn () => route('media.serie', ['slug' => $this->slug]),
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
}
