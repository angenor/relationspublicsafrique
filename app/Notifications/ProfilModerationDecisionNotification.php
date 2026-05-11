<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Profil;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfilModerationDecisionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Profil $profil,
        public readonly string $decision,
        public readonly ?string $motif = null,
    ) {
    }

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $libelle = trim(($this->profil->prenom ?? '').' '.($this->profil->nom ?? ''));
        $mail = (new MailMessage())->subject('Décision de modération sur le profil "'.$libelle.'"');

        if ($this->decision === 'approuve') {
            $mail->line('Bonne nouvelle, le profil "'.$libelle.'" a été approuvé et est désormais publié sur l\'annuaire.');
        } elseif ($this->decision === 'rejete') {
            $mail->line('Le profil "'.$libelle.'" a été rejeté.');
            if ($this->motif) {
                $mail->line('Motif fourni par le modérateur :')->line($this->motif);
            }
            $mail->line('Vous pouvez corriger le profil puis le soumettre à nouveau.');
        } else {
            $mail->line('Le profil "'.$libelle.'" a été archivé.');
        }

        return $mail;
    }
}
