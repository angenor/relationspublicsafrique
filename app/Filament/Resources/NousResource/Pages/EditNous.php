<?php

namespace App\Filament\Resources\NousResource\Pages;

use App\Filament\Resources\NousResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNous extends EditRecord
{
    protected static string $resource = NousResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
