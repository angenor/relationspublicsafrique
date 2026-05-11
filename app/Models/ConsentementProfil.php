<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ConsentementProfil extends Model
{
    use HasFactory;

    protected $table = 'consentements_profils';

    protected $fillable = [
        'profil_id', 'atteste_par', 'atteste_le',
        'email_notification_envoye_a', 'email_notification_envoye_le',
        'jeton_retrait', 'jeton_expire_le', 'retrait_demande_le',
    ];

    protected $casts = [
        'atteste_le' => 'datetime',
        'email_notification_envoye_le' => 'datetime',
        'jeton_expire_le' => 'datetime',
        'retrait_demande_le' => 'datetime',
    ];

    public function profil(): BelongsTo
    {
        return $this->belongsTo(Profil::class);
    }

    public function attesteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atteste_par');
    }

    public function regenererJetonRetrait(): string
    {
        $token = Str::random(64);
        $this->jeton_retrait = $token;
        $this->jeton_expire_le = Carbon::now()->addDays(
            (int) config('annuaire.consentement_expiry_days', 90)
        );
        $this->save();

        return $token;
    }
}
