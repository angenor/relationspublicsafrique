<?php

namespace App\Filament\Resources\LomeTourRegistrationResource\Pages;

use App\Filament\Resources\LomeTourRegistrationResource;
use App\Notifications\LomeTourAcceptanceNotification;
use App\Notifications\LomeTourRejectionNotification;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditLomeTourRegistration extends EditRecord
{
    protected static string $resource = LomeTourRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $originalStatus = $this->record->getOriginal('status');
        $newStatus = $this->record->status;

        // Vérifier si le statut a changé
        if ($originalStatus !== $newStatus) {
            // Envoyer l'email approprié selon le nouveau statut
            if ($newStatus === 'confirmed' && $this->record->email) {
                $this->record->notify(new LomeTourAcceptanceNotification($this->record));

                Notification::make()
                    ->title('Email d\'acceptation envoyé')
                    ->body('Un email de confirmation a été envoyé à ' . $this->record->email)
                    ->success()
                    ->send();
            } elseif ($newStatus === 'rejected' && $this->record->email) {
                $this->record->notify(new LomeTourRejectionNotification($this->record));

                Notification::make()
                    ->title('Email de rejet envoyé')
                    ->body('Un email d\'information a été envoyé à ' . $this->record->email)
                    ->warning()
                    ->send();
            } elseif (!$this->record->email) {
                Notification::make()
                    ->title('Aucun email envoyé')
                    ->body('Aucun email n\'a été envoyé car l\'inscription n\'a pas d\'adresse email')
                    ->warning()
                    ->send();
            }
        }
    }
}
