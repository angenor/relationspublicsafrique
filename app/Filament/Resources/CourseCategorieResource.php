<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseCategorieResource\Pages;
use App\Filament\Resources\CourseCategorieResource\RelationManagers;
use App\Models\Category;
use App\Models\CourseCategorie;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CourseCategorieResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationGroup = 'Formation';
    protected static ?string $label ="Catégorie de cours";

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->minLength(2)
                    ->maxLength(255)
                    ->required()
                    ->live(5000)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
//                                if (($get('slug') ?? '') !== Str::slug($old)) {
//                                    return;
//                                }
                        $set('slug', Str::slug($state));
                    }),



                Forms\Components\TextInput::make('slug') ->required(),

                Forms\Components\Select::make('type')
                    ->placeholder('Sélectionner un type')
                    ->options(['course' => 'Categorie course']),


                Forms\Components\TextInput::make('position')
                    ->numeric()
                    ->required(),
                Forms\Components\FileUpload::make('image') ,

                Forms\Components\Toggle::make('online')
                    ->required(),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('name'),
                //      Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('place'),
                Tables\Columns\IconColumn::make('online')->boolean(),
//                Tables\Columns\TextColumn::make('position'),
//                Tables\Columns\TextColumn::make('parent_id'),
//                Tables\Columns\TextColumn::make('created_at')
//                    ->dateTime(),
//                Tables\Columns\TextColumn::make('updated_at')
//                    ->dateTime(),
            ])
            ->defaultSort('name', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('online')
                        ->action(fn (Category $record) => $record->activate())
                        ->requiresConfirmation()
                        ->color('success'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCourseCategories::route('/'),
            'create' => Pages\CreateCourseCategorie::route('/create'),
            'edit' => Pages\EditCourseCategorie::route('/{record}/edit'),
        ];
    }


}
