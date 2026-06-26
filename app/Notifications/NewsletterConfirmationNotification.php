<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

/**
 * Email de confirmation d'abonnement (double opt-in) via URL signée.
 * Le notifiable est le NewsletterSubscriber (porte le token).
 */
class NewsletterConfirmationNotification extends Notification
{
    use Queueable;

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'media.newsletter.confirm',
            now()->addDays(7),
            ['token' => $notifiable->token],
        );

        return (new MailMessage)
            ->subject('Confirmez votre abonnement à la newsletter RP Afrique')
            ->greeting('Bonjour,')
            ->line('Vous avez demandé à recevoir les nouveautés de la section Média de Relations Publics Afrique.')
            ->line('Merci de confirmer votre abonnement en cliquant sur le bouton ci-dessous.')
            ->action('Confirmer mon abonnement', $url)
            ->line('Si vous n\'êtes pas à l\'origine de cette demande, ignorez simplement cet email.')
            ->salutation('L\'équipe RP Afrique');
    }
}
