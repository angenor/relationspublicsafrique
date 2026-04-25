<?php

namespace App\Filament\Resources\ChapitreResource\Pages;

use App\Filament\Resources\ChapitreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChapitres extends ListRecords
{
    protected static string $resource = ChapitreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
