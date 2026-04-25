<?php

namespace App\Filament\Resources\InstructorsResource\Pages;

use App\Filament\Resources\InstructorsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInstructors extends EditRecord
{
    protected static string $resource = InstructorsResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
