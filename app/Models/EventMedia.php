<?php

declare(strict_types=1);

namespace App\Models;

use App\Helper\VideoEmbed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Média post-événement (research R6) : replay vidéo (embed/natif), photo, document.
 *
 * @property int $id
 * @property int $event_id
 * @property string $type
 * @property string|null $chemin
 * @property string|null $url_embed
 * @property string|null $legende
 * @property int $position
 */
class EventMedia extends Model
{
    use HasFactory;

    protected $table = 'event_medias';

    protected $guarded = ['id'];

    protected $casts = [
        'position' => 'integer',
    ];

    public static array $types = [
        'replay' => 'Replay vidéo',
        'image' => 'Photo',
        'document' => 'Document',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position');
    }

    /** URL publique du fichier téléversé (photo, vidéo native, document), sinon null. */
    public function cheminUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->chemin ? asset('storage/'.$this->chemin) : null,
        );
    }

    /** Iframe d'embed (YouTube/Vimeo) si type=replay et url_embed reconnue, sinon ''. */
    public function embed(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->type === 'replay'
                ? VideoEmbed::iframe($this->url_embed, (string) $this->legende)
                : '',
        );
    }

    public function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => self::$types[$this->type] ?? $this->type,
        );
    }
}
