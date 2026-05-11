<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandeModeration extends Model
{
    use HasFactory;

    protected $table = 'demandes_moderation';

    protected $fillable = [
        'profil_id', 'soumis_par', 'moderateur_id', 'decision', 'motif',
    ];

    protected $casts = [
        'decision' => 'string',
    ];

    public function profil(): BelongsTo
    {
        return $this->belongsTo(Profil::class);
    }

    public function soumetteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'soumis_par');
    }

    public function moderateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderateur_id');
    }
}
