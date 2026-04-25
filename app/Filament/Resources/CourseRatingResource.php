<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseRatingResource\Pages;
use App\Filament\Resources\CourseRatingResource\RelationManagers;
use App\Models\CourseRating;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseRatingResource extends Resource
{
    protected static ?string $model = CourseRating::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Formation';
    protected static ?string $label ="Note";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourseRatings::route('/'),
            'create' => Pages\CreateCourseRating::route('/create'),
            'edit' => Pages\EditCourseRating::route('/{record}/edit'),
        ];
    }
}
