<?php

namespace App\Filament\Resources\ProfilResource\Pages;

use App\Filament\Resources\ProfilResource;
use App\Models\Profil;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateProfil extends CreateRecord
{
    protected static string $resource = ProfilResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {


        $data['slug'] = Str::slug( $data['prenom'] );
        $data['user_id'] = auth()->id();

        return $data;
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


}
