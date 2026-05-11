<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Profil;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ProfilPubliePersonneNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Profil $profil,
        public readonly string $jetonRetrait,
    ) {
    }

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'annuaire.retrait.form',
            now()->addDays((int) config('annuaire.consentement_expiry_days', 90)),
            ['token' => $this->jetonRetrait]
        );

        $nomComplet = trim(($this->profil->prenom ?? '').' '.($this->profil->nom ?? ''));

        return (new MailMessage())
            ->subject('Votre profil a été publié sur l\'annuaire RP Afrique')
            ->greeting('Bonjour '.$nomComplet)
            ->line('Votre profil professionnel a été publié sur l\'annuaire de Relations Publiques Afrique.')
            ->line('Conformément au RGPD, vous disposez d\'un droit d\'accès, de rectification et de retrait.')
            ->action('Gérer mon profil ou demander un retrait', $url)
            ->line('Si vous n\'êtes pas à l\'origine de cette inscription, utilisez le lien ci-dessus pour la signaler.')
            ->salutation('L\'équipe RP Afrique');
    }
}
