<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProfilResource\Pages;

use App\Filament\Resources\ProfilResource;
use App\Models\Profil;
use App\Services\Annuaire\ProfilConsentementService;
use App\Services\Annuaire\ProfilModerationService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Gate;

class EditProfil extends EditRecord
{
    protected static string $resource = ProfilResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Profil $profil */
        $profil = $this->record;
        $actions = [
            Actions\ViewAction::make(),
        ];

        if (Gate::allows('approve', $profil) && $profil->etat_publication === 'en_attente') {
            $actions[] = Actions\Action::make('approuver')
                ->label('Approuver')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () use ($profil) {
                    app(ProfilModerationService::class)->approve($profil, auth()->user());
                    Notification::make()->title('Profil approuvé et publié')->success()->send();
                });
        }

        if (Gate::allows('reject', $profil) && $profil->etat_publication === 'en_attente') {
            $actions[] = Actions\Action::make('rejeter')
                ->label('Rejeter')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    Forms\Components\Textarea::make('motif')
                        ->label('Motif du rejet')
                        ->required()
                        ->maxLength(1000),
                ])
                ->action(function (array $data) use ($profil) {
                    app(ProfilModerationService::class)->reject($profil, auth()->user(), $data['motif']);
                    Notification::make()->title('Profil rejeté')->success()->send();
                });
        }

        if (Gate::allows('archive', $profil) && in_array($profil->etat_publication, ['publie', 'en_attente'], true)) {
            $actions[] = Actions\Action::make('archiver')
                ->label('Archiver')
                ->icon('heroicon-o-archive-box')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () use ($profil) {
                    app(ProfilModerationService::class)->archive($profil, auth()->user());
                    Notification::make()->title('Profil archivé')->success()->send();
                });
        }

        if (Gate::allows('approve', $profil) && $profil->etat_publication === 'archive') {
            $actions[] = Actions\Action::make('republier')
                ->label('Republier')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () use ($profil) {
                    app(ProfilModerationService::class)->approve($profil, auth()->user());
                    Notification::make()->title('Profil republié')->success()->send();
                });
        }

        if (Gate::allows('delete', $profil)) {
            $actions[] = Actions\DeleteAction::make();
        }

        return $actions;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = auth()->user();

        // FR-026 : sans consentement attesté, on ne peut pas publier.
        $profil = $this->record;
        if (! $profil->consentement && ($data['etat_publication'] ?? null) === 'publie') {
            if (empty($data['consentement_atteste'])) {
                $data['etat_publication'] = 'en_attente';
            }
        }

        // T071 : éditeur ne peut pas auto-publier ses propres profils.
        if ($user && ($user->type ?? null) !== 'admin') {
            unset($data['etat_publication']);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        /** @var Profil $profil */
        $profil = $this->record;
        $user = auth()->user();
        $consentementAtteste = (bool) ($this->data['consentement_atteste'] ?? false);

        if ($consentementAtteste && $user && ! $profil->consentement) {
            app(ProfilConsentementService::class)->attester($profil, $user);
        }

        if ($profil->wasChanged('etat_publication') && $profil->etat_publication === 'publie' && $profil->consentement) {
            app(ProfilConsentementService::class)->envoyerNotificationPublication($profil);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
