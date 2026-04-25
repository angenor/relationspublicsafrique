<?php

namespace App\Filament\Resources\CourseRatingResource\Pages;

use App\Filament\Resources\CourseRatingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCourseRating extends EditRecord
{
    protected static string $resource = CourseRatingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
