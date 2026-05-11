<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProfilResource\Pages;

use App\Filament\Resources\ProfilResource;
use App\Models\Profil;
use App\Services\Annuaire\ProfilConsentementService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateProfil extends CreateRecord
{
    protected static string $resource = ProfilResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug(trim(($data['prenom'] ?? '').' '.($data['nom'] ?? '')));
        }
        $data['user_id'] = $user?->id;

        // FR-026 : un profil sans consentement attesté ne peut pas être publié.
        if (empty($data['consentement_atteste']) && ($data['etat_publication'] ?? null) === 'publie') {
            $data['etat_publication'] = 'en_attente';
        }

        // T071 : un éditeur (non-admin) ne peut pas auto-publier — toujours en_attente.
        if ($user && ($user->type ?? null) !== 'admin') {
            $data['etat_publication'] = 'en_attente';
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var Profil $profil */
        $profil = $this->record;
        $user = auth()->user();
        $consentementAtteste = (bool) ($this->data['consentement_atteste'] ?? false);

        if ($consentementAtteste && $user) {
            app(ProfilConsentementService::class)->attester($profil, $user);

            if ($profil->etat_publication === 'publie') {
                app(ProfilConsentementService::class)->envoyerNotificationPublication($profil);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
