<?php

namespace App\Models;

use App\Services\Annuaire\TextNormalizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string|null $nom
 * @property string|null $prenom
 * @property string|null $nationalite
 * @property string|null $bio_courte
 * @property string|null $bio_longue
 * @property string|null $ville
 * @property string|null $organisation
 * @property string $type_profil
 * @property string $etat_publication
 * @property bool $masquer_email
 * @property bool $masquer_tel
 * @property string $nom_normalise
 * @property string $prenom_normalise
 * @property string $organisation_normalisee
 * @property string $ville_normalisee
 * @property bool $legacy_sans_consentement
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Profil extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $guarded = ['id'];

    protected $casts = [
        'masquer_email' => 'boolean',
        'masquer_tel' => 'boolean',
        'legacy_sans_consentement' => 'boolean',
        'published_at' => 'datetime',
        'type_profil' => 'string',
        'etat_publication' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pays(): BelongsTo
    {
        return $this->belongsTo(Pays::class);
    }

    public function domainesExpertise(): BelongsToMany
    {
        return $this->belongsToMany(
            DomaineExpertise::class,
            'profil_domaine_expertise',
            'profil_id',
            'domaine_expertise_id'
        );
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'profil_tag');
    }

    public function liensExternes(): HasMany
    {
        return $this->hasMany(LienExterne::class);
    }

    public function historiques(): HasMany
    {
        return $this->hasMany(HistoriqueProfil::class);
    }

    public function demandesModeration(): HasMany
    {
        return $this->hasMany(DemandeModeration::class);
    }

    public function consentement(): HasOne
    {
        return $this->hasOne(ConsentementProfil::class);
    }

    // ----- Mutateurs : maintien des colonnes normalisées -----

    public function nom(): Attribute
    {
        return Attribute::make(
            set: function (?string $value): array {
                return [
                    'nom' => $value,
                    'nom_normalise' => TextNormalizer::normalize($value),
                ];
            }
        );
    }

    public function prenom(): Attribute
    {
        return Attribute::make(
            set: function (?string $value): array {
                return [
                    'prenom' => $value,
                    'prenom_normalise' => TextNormalizer::normalize($value),
                ];
            }
        );
    }

    public function organisation(): Attribute
    {
        return Attribute::make(
            set: function (?string $value): array {
                return [
                    'organisation' => $value,
                    'organisation_normalisee' => TextNormalizer::normalize($value),
                ];
            }
        );
    }

    public function ville(): Attribute
    {
        return Attribute::make(
            set: function (?string $value): array {
                return [
                    'ville' => $value,
                    'ville_normalisee' => TextNormalizer::normalize($value),
                ];
            }
        );
    }

    // ----- Scopes -----

    public function scopePublies(Builder $query): Builder
    {
        return $query->where('etat_publication', 'publie');
    }

    public function scopeEnAttente(Builder $query): Builder
    {
        return $query->where('etat_publication', 'en_attente');
    }

    public function scopeArchives(Builder $query): Builder
    {
        return $query->where('etat_publication', 'archive');
    }

    public function scopeParType(Builder $query, string $type): Builder
    {
        return $query->where('type_profil', $type);
    }

    // ----- Accesseurs legacy -----

    public function getImgAttribute()
    {
        return $this->image ? '/storage/' . $this->image : asset('img/img1.jpeg');
    }

    /**
     * URL publique de la photo du profil ; retourne l'avatar par défaut si vide.
     */
    public function getPhotoUrlAttribute(): string
    {
        $image = $this->image;

        if (empty($image) || in_array($image, ['images/user.png'], true)) {
            return asset('img/profils/default-avatar.svg');
        }

        if (preg_match('#^https?://#i', $image)) {
            return $image;
        }

        return asset('storage/' . ltrim($image, '/'));
    }

    public function fullname(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => trim(($this->nom ?? '') . ' ' . ($this->prenom ?? ''))
        );
    }

    public function link(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => route('profil.show', [$this->slug, $this->id])
        );
    }

    public function resumeBio(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => \Str::limit($this->bio, 80)
        );
    }
}
