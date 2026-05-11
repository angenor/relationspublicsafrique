<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LienExterne extends Model
{
    use HasFactory;

    protected $table = 'liens_externes';

    protected $fillable = ['profil_id', 'type', 'url', 'libelle'];

    protected $casts = [
        'type' => 'string',
    ];

    public function profil(): BelongsTo
    {
        return $this->belongsTo(Profil::class);
    }
}
