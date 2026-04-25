<?php

namespace App\Filament\Resources\CourseRatingResource\Pages;

use App\Filament\Resources\CourseRatingResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListCourseRatings extends ListRecords
{
    protected static string $resource = CourseRatingResource::class;

    protected function getHeaderActions(): array
    {

        return [
            Actions\CreateAction::make(),

        ];
    }
}
