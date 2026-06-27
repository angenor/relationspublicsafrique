<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Intervenant d'un événement (research R5) : nom, rôle/qualité, photo, bio.
 *
 * @property int $id
 * @property int $event_id
 * @property string $nom
 * @property string|null $role
 * @property string|null $organisation
 * @property string|null $photo
 * @property string|null $bio
 * @property int $position
 */
class EventSpeaker extends Model
{
    use HasFactory;

    protected $table = 'event_speakers';

    protected $guarded = ['id'];

    protected $casts = [
        'position' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position');
    }

    /** URL publique de la photo téléversée, sinon null (la vue gère le fallback). */
    public function photoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->photo ? asset('storage/'.$this->photo) : null,
        );
    }
}
