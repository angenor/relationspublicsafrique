<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LomeTourAdminNotification extends Notification
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
            ->subject('Nouvelle inscription - Lomé COM’ TOUR')
            ->greeting('Nouvelle inscription reçue')
            ->line('Nom: '.$this->registration->full_name)
            ->line('Statut: '.$this->registration->statut_label)
            ->line('Fonction: '.$this->registration->fonction)
            ->line('WhatsApp: '.$this->registration->telephone_whatsapp)
            ->line('Email: '.($this->registration->email ?: 'N/A'))
            ->line('Statut validation: '.$this->registration->status_label)
            ->action('Gérer dans le panel', url('/admin'));
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
