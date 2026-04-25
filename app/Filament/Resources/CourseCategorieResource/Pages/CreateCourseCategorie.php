<?php

namespace App\Filament\Resources\CourseCategorieResource\Pages;

use App\Filament\Resources\CourseCategorieResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCourseCategorie extends CreateRecord
{
    protected static string $resource = CourseCategorieResource::class;




    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'course';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


}
