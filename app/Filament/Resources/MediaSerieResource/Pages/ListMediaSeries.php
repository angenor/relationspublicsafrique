<?php

declare(strict_types=1);

namespace App\Filament\Resources\MediaSerieResource\Pages;

use App\Filament\Resources\MediaSerieResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMediaSeries extends ListRecords
{
    protected static string $resource = MediaSerieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
