<?php

namespace App\Filament\Resources\CourseCategorieResource\Pages;

use App\Filament\Resources\CourseCategorieResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCourseCategorie extends EditRecord
{
    protected static string $resource = CourseCategorieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
