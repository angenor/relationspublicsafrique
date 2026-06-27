<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Chiffre clé / résultat d'impact d'un projet (R8).
 *
 * @property int $id
 * @property int $projet_id
 * @property string $libelle
 * @property string $valeur
 * @property string|null $unite
 * @property string|null $icone
 * @property int $position
 */
class ProjetResultat extends Model
{
    use HasFactory;

    protected $table = 'projet_resultats';

    protected $guarded = ['id'];

    protected $casts = [
        'position' => 'integer',
    ];

    public function projet(): BelongsTo
    {
        return $this->belongsTo(Projet::class);
    }
}
