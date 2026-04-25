<?php

namespace App\Filament\Resources\WidgetTextResource\Pages;

use App\Filament\Resources\WidgetTextResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWidgetTexts extends ListRecords
{
    protected static string $resource = WidgetTextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
