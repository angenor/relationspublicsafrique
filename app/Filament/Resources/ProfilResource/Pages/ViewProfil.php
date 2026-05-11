<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProfilResource\Pages;

use App\Filament\Resources\ProfilResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProfil extends ViewRecord
{
    protected static string $resource = ProfilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
