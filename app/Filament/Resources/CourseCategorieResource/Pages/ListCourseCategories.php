<?php

namespace App\Filament\Resources\CourseCategorieResource\Pages;

use App\Filament\Resources\CourseCategorieResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCourseCategories extends ListRecords
{
    protected static string $resource = CourseCategorieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
