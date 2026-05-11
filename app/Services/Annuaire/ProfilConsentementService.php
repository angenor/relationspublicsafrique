<?php

declare(strict_types=1);

namespace App\Services\Annuaire;

use App\Models\ConsentementProfil;
use App\Models\Profil;
use App\Models\User;
use App\Notifications\ProfilPubliePersonneNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

class ProfilConsentementService
{
    public function __construct(private readonly ProfilHistoriqueService $historique)
    {
    }

    /**
     * Un administrateur atteste qu'il a recueilli le consentement RGPD
     * écrit/oral de la personne référencée par le profil.
     */
    public function attester(Profil $profil, User $admin, ?string $emailNotification = null): ConsentementProfil
    {
        $consentement = ConsentementProfil::firstOrNew(['profil_id' => $profil->id]);
        $consentement->atteste_par = $admin->id;
        $consentement->atteste_le = Carbon::now();
        if ($emailNotification !== null) {
            $consentement->email_notification_envoye_a = $emailNotification;
        }
        $consentement->save();

        $consentement->regenererJetonRetrait();

        $this->historique->log($profil, 'consentement_atteste', [
            'consentement' => ['avant' => null, 'apres' => 'atteste'],
        ]);

        return $consentement;
    }

    public function regenererJeton(Profil $profil): string
    {
        $consentement = $profil->consentement;
        if (! $consentement) {
            throw new \LogicException('Le profil n\'a pas de consentement à régénérer.');
        }

        return $consentement->regenererJetonRetrait();
    }

    /**
     * Notifie la personne référencée que son profil est publié et lui transmet
     * le lien signé pour demander un retrait/modification (FR-027, SC-010).
     */
    public function envoyerNotificationPublication(Profil $profil): void
    {
        $consentement = $profil->consentement()->firstOrCreate(
            ['profil_id' => $profil->id],
            ['atteste_par' => $profil->user_id ?? 0, 'atteste_le' => Carbon::now()]
        );

        if (! $consentement->jeton_retrait) {
            $consentement->regenererJetonRetrait();
        }

        $destinataire = $consentement->email_notification_envoye_a ?? $profil->email;
        if (! $destinataire) {
            return;
        }

        Notification::route('mail', $destinataire)
            ->notify(new ProfilPubliePersonneNotification($profil, $consentement->jeton_retrait));

        $consentement->email_notification_envoye_a = $destinataire;
        $consentement->email_notification_envoye_le = Carbon::now();
        $consentement->save();
    }

    /**
     * Marque la demande de retrait reçue ; renvoie le profil cible si jeton valide.
     */
    public function traiterDemandeRetrait(string $jeton): ?Profil
    {
        $consentement = ConsentementProfil::where('jeton_retrait', $jeton)->first();

        if (! $consentement) {
            return null;
        }

        if ($consentement->jeton_expire_le && $consentement->jeton_expire_le->isPast()) {
            return null;
        }

        $consentement->retrait_demande_le = Carbon::now();
        $consentement->save();

        $profil = $consentement->profil;
        if ($profil) {
            $profil->etat_publication = 'archive';
            $profil->save();
            $this->historique->log($profil, 'retrait_demande', [
                'etat_publication' => ['avant' => 'publie', 'apres' => 'archive'],
            ], 'Demande de retrait via lien signé');
        }

        return $profil;
    }
}
