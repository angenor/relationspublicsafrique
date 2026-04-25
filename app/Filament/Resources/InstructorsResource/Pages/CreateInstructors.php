<?php

namespace App\Filament\Resources\InstructorsResource\Pages;

use App\Filament\Resources\InstructorsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInstructors extends CreateRecord
{
    protected static string $resource = InstructorsResource::class;


    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
