<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Profil;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemandeRetraitConfirmeNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Profil $profil)
    {
    }

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $nomComplet = trim(($this->profil->prenom ?? '').' '.($this->profil->nom ?? ''));

        return (new MailMessage())
            ->subject('Votre retrait de l\'annuaire RP Afrique est confirmé')
            ->greeting('Bonjour '.$nomComplet)
            ->line('Nous confirmons que votre profil a été retiré de l\'annuaire public.')
            ->line('Si vous changez d\'avis, vous pouvez contacter notre équipe pour une nouvelle inscription.')
            ->salutation('L\'équipe RP Afrique');
    }
}
