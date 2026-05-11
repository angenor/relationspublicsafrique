<?php

declare(strict_types=1);

namespace App\Services\Annuaire;

use App\Models\DemandeModeration;
use App\Models\Profil;
use App\Models\User;
use App\Notifications\ProfilModerationDecisionNotification;
use Illuminate\Support\Carbon;

class ProfilModerationService
{
    public function __construct(
        private readonly ProfilHistoriqueService $historique,
        private readonly ProfilConsentementService $consentement,
    ) {
    }

    /**
     * Un éditeur soumet son profil à validation.
     */
    public function submit(Profil $profil, User $soumetteur): DemandeModeration
    {
        $profil->etat_publication = 'en_attente';
        $profil->save();

        $demande = DemandeModeration::create([
            'profil_id' => $profil->id,
            'soumis_par' => $soumetteur->id,
            'decision' => 'soumis',
        ]);

        $this->historique->log($profil, 'soumis_moderation', [
            'etat_publication' => ['avant' => $profil->getOriginal('etat_publication'), 'apres' => 'en_attente'],
        ]);

        return $demande;
    }

    public function approve(Profil $profil, User $admin): DemandeModeration
    {
        $avant = $profil->etat_publication;
        $profil->etat_publication = 'publie';
        $profil->published_at = $profil->published_at ?? Carbon::now();
        $profil->save();

        $demande = DemandeModeration::create([
            'profil_id' => $profil->id,
            'soumis_par' => $profil->user_id,
            'moderateur_id' => $admin->id,
            'decision' => 'approuve',
        ]);

        $this->historique->log($profil, 'approuve', [
            'etat_publication' => ['avant' => $avant, 'apres' => 'publie'],
        ]);

        // Notifier la personne référencée (FR-027) si consentement.
        if ($profil->consentement) {
            $this->consentement->envoyerNotificationPublication($profil->fresh());
        }

        // Notifier l'éditeur de la décision.
        if ($soumetteur = $profil->user) {
            $soumetteur->notify(new ProfilModerationDecisionNotification($profil, 'approuve', null));
        }

        return $demande;
    }

    public function reject(Profil $profil, User $admin, string $motif): DemandeModeration
    {
        $demande = DemandeModeration::create([
            'profil_id' => $profil->id,
            'soumis_par' => $profil->user_id,
            'moderateur_id' => $admin->id,
            'decision' => 'rejete',
            'motif' => $motif,
        ]);

        $this->historique->log($profil, 'rejete', [], $motif);

        if ($soumetteur = $profil->user) {
            $soumetteur->notify(new ProfilModerationDecisionNotification($profil, 'rejete', $motif));
        }

        return $demande;
    }

    public function archive(Profil $profil, User $admin): DemandeModeration
    {
        $avant = $profil->etat_publication;
        $profil->etat_publication = 'archive';
        $profil->save();

        $demande = DemandeModeration::create([
            'profil_id' => $profil->id,
            'soumis_par' => $profil->user_id,
            'moderateur_id' => $admin->id,
            'decision' => 'archive',
        ]);

        $this->historique->log($profil, 'archive', [
            'etat_publication' => ['avant' => $avant, 'apres' => 'archive'],
        ]);

        return $demande;
    }
}
