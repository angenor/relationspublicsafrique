<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LomeTourAcceptanceNotification extends Notification
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
            ->subject('Félicitations ! Votre inscription au Lomé COM\' TOUR a été acceptée')
            ->greeting('Félicitations ' . $this->registration->full_name . ' !')
            ->line('Nous avons le plaisir de vous informer que votre inscription au Lomé COM\' TOUR a été acceptée.')
            ->line('Vos informations :')
            ->line('• Nom complet : ' . $this->registration->full_name)
            ->line('• Statut : ' . $this->registration->statut_label)
            ->line('• Fonction : ' . $this->registration->fonction)
            ->line('• Téléphone WhatsApp : ' . $this->registration->telephone_whatsapp)
            ->line('Vous recevrez prochainement toutes les informations pratiques concernant l\'événement.')
            ->line('Nous vous remercions de votre intérêt et nous réjouissons de vous accueillir !')
            ->action('Visiter notre site', url('/'))
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
            'status' => 'accepted',
        ];
    }
}
