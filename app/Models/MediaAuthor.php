<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle pivot léger pour media_authors (co-auteurs / intervenants).
 * Expose le pivot en hasMany (Media::contributions()) afin d'éditer le rôle
 * via un Repeater Filament.
 */
class MediaAuthor extends Model
{
    use HasFactory;

    protected $table = 'media_authors';

    protected $guarded = ['id'];

    protected $casts = [
        'position' => 'integer',
    ];

    /** Rôles possibles d'une contribution. */
    public static array $roles = [
        'auteur' => 'Auteur',
        'interviewer' => 'Interviewer',
        'invite' => 'Invité',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
