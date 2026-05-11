<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ConsentementProfil;
use App\Notifications\ProfilPubliePersonneNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

class AnnuaireEnvoyerRappelsConsentement extends Command
{
    protected $signature = 'annuaire:envoyer-rappels-consentement {--days=7 : Fenêtre (jours avant expiration)}';

    protected $description = 'Renvoie l\'email de consentement aux profils dont le jeton expire bientôt (FR-027).';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $now = Carbon::now();
        $limite = $now->copy()->addDays($days);

        $consentements = ConsentementProfil::query()
            ->whereNotNull('jeton_expire_le')
            ->whereBetween('jeton_expire_le', [$now, $limite])
            ->with('profil')
            ->get();

        $envoyes = 0;
        foreach ($consentements as $consentement) {
            $profil = $consentement->profil;
            $email = $consentement->email_notification_envoye_a ?? $profil?->email;
            if (! $profil || ! $email) {
                continue;
            }

            $token = $consentement->regenererJetonRetrait();
            Notification::route('mail', $email)
                ->notify(new ProfilPubliePersonneNotification($profil, $token));

            $consentement->email_notification_envoye_a = $email;
            $consentement->email_notification_envoye_le = $now;
            $consentement->save();
            $envoyes++;
        }

        $this->info("Rappels envoyés : {$envoyes}");

        return self::SUCCESS;
    }
}
