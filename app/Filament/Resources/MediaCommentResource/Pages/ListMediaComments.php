<?php

declare(strict_types=1);

namespace App\Filament\Resources\MediaCommentResource\Pages;

use App\Filament\Resources\MediaCommentResource;
use Filament\Resources\Pages\ListRecords;

class ListMediaComments extends ListRecords
{
    protected static string $resource = MediaCommentResource::class;
}
