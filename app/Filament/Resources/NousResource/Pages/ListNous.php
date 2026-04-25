<?php

namespace App\Filament\Resources\NousResource\Pages;

use App\Filament\Resources\NousResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNous extends ListRecords
{
    protected static string $resource = NousResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
