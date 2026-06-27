<?php

declare(strict_types=1);

namespace App\Models;

use App\Helper\VideoEmbed;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Média de galerie d'un projet (R6) : image téléversée ou vidéo (embed/natif).
 *
 * @property int $id
 * @property int $projet_id
 * @property string $type
 * @property string|null $chemin
 * @property string|null $url_embed
 * @property string|null $legende
 * @property int $position
 */
class ProjetMedia extends Model
{
    use HasFactory;

    protected $table = 'projet_medias';

    protected $guarded = ['id'];

    protected $casts = [
        'position' => 'integer',
    ];

    public function projet(): BelongsTo
    {
        return $this->belongsTo(Projet::class);
    }

    /** URL publique du fichier téléversé (image ou vidéo native), sinon null. */
    public function cheminUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->chemin ? asset('storage/'.$this->chemin) : null,
        );
    }

    /** Iframe d'embed (YouTube/Vimeo) si type=video et url_embed reconnue, sinon ''. */
    public function embedHtml(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->type === 'video'
                ? VideoEmbed::iframe($this->url_embed, (string) $this->legende)
                : '',
        );
    }
}
