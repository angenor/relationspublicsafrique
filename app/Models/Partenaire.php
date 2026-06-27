<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Partenaire institutionnel réutilisable entre projets (R7).
 *
 * @property int $id
 * @property string $nom
 * @property string|null $slug
 * @property string|null $logo
 * @property string|null $url
 */
class Partenaire extends Model
{
    use HasFactory;

    protected $table = 'partenaires';

    protected $guarded = ['id'];

    public function projets(): BelongsToMany
    {
        return $this->belongsToMany(Projet::class, 'partenaire_projet')
            ->withPivot('position')
            ->withTimestamps();
    }

    /** URL publique du logo, sinon placeholder. */
    public function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->logo
                ? asset('storage/'.$this->logo)
                : asset('logos/logo1.png'),
        );
    }
}
