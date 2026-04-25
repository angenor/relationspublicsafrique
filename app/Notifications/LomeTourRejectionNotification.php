<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LomeTourRejectionNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public \App\Models\LomeTourRegistration $registration)
    {
        //
    }

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
            ->subject('Information concernant votre inscription au Lomé COM\' TOUR')
            ->greeting('Bonjour ' . $this->registration->full_name)
            ->line('Nous vous remercions de votre intérêt pour le Lomé COM\' TOUR.')
            ->line('Après examen de votre candidature, nous regrettons de vous informer que votre inscription n\'a pas pu être acceptée pour cette édition.')
            ->line('Cela peut être dû à plusieurs raisons :')
            ->line('• Capacité d\'accueil limitée')
            ->line('• Critères de sélection spécifiques')
            ->line('• Délai d\'inscription dépassé')
            ->line('Nous vous encourageons à suivre nos actualités pour les prochaines éditions.')
            ->action('Découvrir nos autres événements', url('/events'))
            ->line('Nous vous remercions de votre compréhension.')
            ->line('Cordialement,')
            ->line('L\'équipe Relations Publiques Afrique');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'registration_id' => $this->registration->id,
            'status' => 'rejected',
        ];
    }
}
