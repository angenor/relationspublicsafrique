<?php

namespace App\Filament\Resources\PaysResource\Pages;

use App\Filament\Resources\PaysResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePays extends ManageRecords
{
    protected static string $resource = PaysResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
