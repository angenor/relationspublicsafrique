<?php

namespace App\Filament\Resources\LomeTourRegistrationResource\Pages;

use App\Filament\Resources\LomeTourRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLomeTourRegistrations extends ListRecords
{
    protected static string $resource = LomeTourRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
