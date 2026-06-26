<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Alerte aux abonnés confirmés à la publication d'un nouveau contenu média.
 * Le notifiable est le NewsletterSubscriber (porte le token de désabonnement).
 */
class NewMediaPublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Media $media) {}

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $unsubscribe = URL::signedRoute(
            'media.newsletter.unsubscribe',
            ['token' => $notifiable->token],
        );

        $extrait = $this->media->chapo
            ?: Str::limit(strip_tags((string) $this->media->content), 160);

        return (new MailMessage)
            ->subject('Nouveau sur RP Afrique : '.$this->media->titre)
            ->greeting('Bonjour,')
            ->line('Un nouveau contenu vient d\'être publié dans la newsroom de Relations Publics Afrique :')
            ->line('**'.$this->media->titre.'**')
            ->when($extrait !== '', fn (MailMessage $m) => $m->line($extrait))
            ->action('Lire le contenu', $this->media->link)
            ->line('Pour ne plus recevoir ces alertes, [désabonnez-vous ici]('.$unsubscribe.').')
            ->salutation('L\'équipe RP Afrique');
    }
}
