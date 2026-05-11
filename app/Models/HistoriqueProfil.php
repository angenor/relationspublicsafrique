<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriqueProfil extends Model
{
    use HasFactory;

    protected $table = 'historique_profils';

    public $timestamps = false;

    protected $fillable = [
        'profil_id', 'user_id', 'action', 'diff', 'motif', 'ip', 'user_agent', 'created_at',
    ];

    protected $casts = [
        'diff' => 'array',
        'created_at' => 'datetime',
    ];

    public function profil(): BelongsTo
    {
        return $this->belongsTo(Profil::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Les entrées d'historique sont conçues comme immuables côté code :
     * on bloque les updates pour éviter toute réécriture du journal.
     */
    public function update(array $attributes = [], array $options = []): bool
    {
        if ($this->exists) {
            return false;
        }

        return parent::update($attributes, $options);
    }
}
