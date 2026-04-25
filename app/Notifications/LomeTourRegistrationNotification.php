<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LomeTourRegistrationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public \App\Models\LomeTourRegistration $registration) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Confirmation d’inscription - Lomé COM’ TOUR')
            ->greeting('Bonjour '.$this->registration->full_name)
            ->line('Votre inscription au Lomé COM’ TOUR a bien été enregistrée.')
            ->line('Statut: '.$this->registration->status_label)
            ->line('Statut (profil): '.$this->registration->statut_label)
            ->line('Téléphone WhatsApp: '.$this->registration->telephone_whatsapp)
            ->action('Voir le site', url('/'))
            ->line('Merci et à très bientôt !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
