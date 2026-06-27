<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProjetResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TemoignagesRelationManager extends RelationManager
{
    protected static string $relationship = 'temoignages';

    protected static ?string $title = 'Témoignages';

    protected static ?string $modelLabel = 'témoignage';

    protected static ?string $pluralModelLabel = 'témoignages';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('auteur')->label('Auteur')->required()->maxLength(255),
            Forms\Components\TextInput::make('fonction')->label('Fonction')->maxLength(255),
            Forms\Components\TextInput::make('organisation')->label('Organisation')->maxLength(255),
            Forms\Components\Textarea::make('contenu')->label('Témoignage')->required()->rows(4)->columnSpanFull(),
            Forms\Components\FileUpload::make('photo')
                ->label('Photo')
                ->image()
                ->disk('public')
                ->directory('projets/temoignages')
                ->visibility('public'),
            Forms\Components\TextInput::make('position')->label('Ordre')->numeric()->default(0),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('auteur')
            ->reorderable('position')
            ->defaultSort('position')
            ->columns([
                Tables\Columns\TextColumn::make('auteur')->label('Auteur'),
                Tables\Columns\TextColumn::make('organisation')->label('Organisation'),
                Tables\Columns\TextColumn::make('contenu')->label('Témoignage')->limit(50)->wrap(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Aucun témoignage');
    }
}
