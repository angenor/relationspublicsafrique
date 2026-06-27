<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Témoignage optionnel rattaché à un projet (R9).
 *
 * @property int $id
 * @property int $projet_id
 * @property string $auteur
 * @property string|null $fonction
 * @property string|null $organisation
 * @property string $contenu
 * @property string|null $photo
 * @property int $position
 */
class ProjetTemoignage extends Model
{
    use HasFactory;

    protected $table = 'projet_temoignages';

    protected $guarded = ['id'];

    protected $casts = [
        'position' => 'integer',
    ];

    public function projet(): BelongsTo
    {
        return $this->belongsTo(Projet::class);
    }

    public function photoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->photo ? asset('storage/'.$this->photo) : null,
        );
    }
}
