<?php

declare(strict_types=1);

namespace App\Filament\Resources\EventResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Intervenants d'un événement (research R5) — CRUD réordonnable par position.
 */
class SpeakersRelationManager extends RelationManager
{
    protected static string $relationship = 'speakers';

    protected static ?string $title = 'Intervenants';

    protected static ?string $modelLabel = 'intervenant';

    protected static ?string $pluralModelLabel = 'intervenants';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nom')
                ->label('Nom')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('role')
                ->label('Rôle / qualité')
                ->maxLength(255),
            Forms\Components\TextInput::make('organisation')
                ->label('Organisation')
                ->maxLength(255),
            Forms\Components\FileUpload::make('photo')
                ->label('Photo')
                ->image()
                ->imageEditor()
                ->disk('public')
                ->directory('events/speakers')
                ->visibility('public'),
            Forms\Components\Textarea::make('bio')
                ->label('Biographie courte')
                ->rows(3)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('position')
                ->label('Ordre')
                ->numeric()
                ->default(0),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nom')
            ->reorderable('position')
            ->defaultSort('position')
            ->columns([
                Tables\Columns\ImageColumn::make('photo')->label('Photo')->disk('public')->circular()->size(45),
                Tables\Columns\TextColumn::make('nom')->label('Nom')->searchable(),
                Tables\Columns\TextColumn::make('role')->label('Rôle')->limit(30),
                Tables\Columns\TextColumn::make('organisation')->label('Organisation')->limit(30)->toggleable(),
                Tables\Columns\TextColumn::make('position')->label('Ordre'),
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
            ->emptyStateHeading('Aucun intervenant');
    }
}
