<?php

namespace App\Filament\Resources\WidgetTextResource\Pages;

use App\Filament\Resources\WidgetTextResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWidgetText extends EditRecord
{
    protected static string $resource = WidgetTextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
