<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProjetResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ResultatsRelationManager extends RelationManager
{
    protected static string $relationship = 'resultats';

    protected static ?string $title = 'Chiffres clés';

    protected static ?string $modelLabel = 'chiffre clé';

    protected static ?string $pluralModelLabel = 'chiffres clés';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('libelle')->label('Libellé')->required()->maxLength(255),
            Forms\Components\TextInput::make('valeur')->label('Valeur')->required()->maxLength(255)
                ->helperText('Texte libre : « 1 200 », « +35 % », etc.'),
            Forms\Components\TextInput::make('unite')->label('Unité')->maxLength(255),
            Forms\Components\TextInput::make('icone')->label('Icône (classe CSS)')->maxLength(255),
            Forms\Components\TextInput::make('position')->label('Ordre')->numeric()->default(0),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('libelle')
            ->reorderable('position')
            ->defaultSort('position')
            ->columns([
                Tables\Columns\TextColumn::make('libelle')->label('Libellé'),
                Tables\Columns\TextColumn::make('valeur')->label('Valeur'),
                Tables\Columns\TextColumn::make('unite')->label('Unité'),
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
            ->emptyStateHeading('Aucun chiffre clé');
    }
}
