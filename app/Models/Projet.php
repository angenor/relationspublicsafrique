<?php

declare(strict_types=1);

namespace App\Models;

use App\Helper\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Projet de la vitrine institutionnelle (feature 003-projets-vitrine).
 *
 * @property int $id
 * @property string $titre
 * @property string $slug
 * @property string|null $titre_normalise
 * @property string|null $resume
 * @property string $statut
 * @property bool $is_published
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property int|null $pays_id
 * @property string $portee
 * @property string|null $zone_libelle
 * @property bool $featured
 * @property int $position
 */
class Projet extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;

    protected $table = 'projets';

    protected $guarded = ['id'];

    protected $casts = [
        'is_published' => 'boolean',
        'featured' => 'boolean',
        'published_at' => 'datetime',
        'position' => 'integer',
        'pays_id' => 'integer',
    ];

    /** Statut métier (badge éditorial, FR-003). */
    public static array $statuts = [
        'actif' => 'Actif',
        'realise' => 'Réalisé',
        'en_developpement' => 'En développement',
    ];

    /** Nature de la portée géographique (R3). */
    public static array $portees = [
        'pays' => 'National',
        'regional' => 'Régional',
        'continental' => 'Continental',
    ];

    // La résolution publique « publiés uniquement » (FR-016) est faite côté
    // ProjetController::show pour ne pas affecter la liaison d'enregistrement
    // de Filament (qui réutilise resolveRouteBinding et la clé primaire).

    // ----- Relations -----

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_projet')
            ->withPivot('position')
            ->withTimestamps()
            ->orderBy('category_projet.position');
    }

    public function pays(): BelongsTo
    {
        return $this->belongsTo(Pays::class);
    }

    public function resultats(): HasMany
    {
        return $this->hasMany(ProjetResultat::class)->orderBy('position');
    }

    public function medias(): HasMany
    {
        return $this->hasMany(ProjetMedia::class)->orderBy('position');
    }

    public function temoignages(): HasMany
    {
        return $this->hasMany(ProjetTemoignage::class)->orderBy('position');
    }

    public function partenaires(): BelongsToMany
    {
        return $this->belongsToMany(Partenaire::class, 'partenaire_projet')
            ->withPivot('position')
            ->withTimestamps()
            ->orderBy('partenaire_projet.position');
    }

    // ----- Scopes -----

    /** Projet visible publiquement : publié et dont la date de mise en ligne est passée. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where('published_at', '<=', now());
    }

    public function scopeByStatut(Builder $query, string $statut): Builder
    {
        return $query->where('statut', $statut);
    }

    public function scopeByThematique(Builder $query, int $categoryId): Builder
    {
        return $query->whereHas('categories', fn (Builder $sub) => $sub->where('category_id', $categoryId));
    }

    /** Filtre par zone : token « pays:{id} » ou « portee:{regional|continental} ». */
    public function scopeByZone(Builder $query, string $zone): Builder
    {
        if (str_starts_with($zone, 'pays:')) {
            return $query->where('pays_id', (int) substr($zone, 5));
        }
        if (str_starts_with($zone, 'portee:')) {
            return $query->where('portee', substr($zone, 7));
        }

        return $query;
    }

    /** Ordre d'affichage du listing : mis en avant, puis position manuelle, puis récence. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('featured')
            ->orderBy('position')
            ->orderByDesc('published_at');
    }

    // ----- Accesseurs -----

    public function visuelCardUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->visuel_card
                ? asset('storage/'.$this->visuel_card)
                : asset('logos/logo1.png'),
        );
    }

    public function visuelPrincipalUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->visuel_principal
                ? asset('storage/'.$this->visuel_principal)
                : $this->visuel_card_url,
        );
    }

    public function statutLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => self::$statuts[$this->statut] ?? $this->statut,
        );
    }

    /** Libellé de zone : nom du pays (portée nationale) sinon libellé libre. */
    public function zoneLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->portee === 'pays' ? $this->pays?->name : $this->zone_libelle,
        );
    }

    // ----- Actions (bulk admin) -----

    public function publish(): void
    {
        $this->is_published = true;
        if (empty($this->published_at) || $this->published_at->isFuture()) {
            $this->published_at = now();
        }
        $this->save();
    }

    public function unpublish(): void
    {
        $this->is_published = false;
        $this->save();
    }

    public function feature(bool $state = true): void
    {
        $this->featured = $state;
        $this->save();
    }
}
