<?php

namespace App\Filament\Resources\ChapitreResource\Pages;

use App\Filament\Resources\ChapitreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChapitre extends EditRecord
{
    protected static string $resource = ChapitreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
