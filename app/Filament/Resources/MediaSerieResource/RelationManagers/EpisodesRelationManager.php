<?php

declare(strict_types=1);

namespace App\Filament\Resources\MediaSerieResource\RelationManagers;

use App\Models\Media;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EpisodesRelationManager extends RelationManager
{
    protected static string $relationship = 'medias';

    protected static ?string $title = 'Épisodes';

    protected static ?string $modelLabel = 'épisode';

    protected static ?string $pluralModelLabel = 'épisodes';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('titre')
                ->label('Titre')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\Select::make('type')
                ->label('Type')
                ->options(Media::$types)
                ->default('podcast')
                ->required(),
            Forms\Components\Select::make('status')
                ->label('Statut')
                ->options(Media::$statuses)
                ->default('draft')
                ->required(),
            Forms\Components\TextInput::make('saison')->label('Saison')->numeric(),
            Forms\Components\TextInput::make('episode')->label('Épisode')->numeric(),
            Forms\Components\TextInput::make('serie_position')->label('Ordre')->numeric()->default(0),
            Forms\Components\DateTimePicker::make('published_at')->label('Date de publication'),
        ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titre')
            ->columns([
                Tables\Columns\TextColumn::make('saison')->label('S')->sortable(),
                Tables\Columns\TextColumn::make('episode')->label('Ép.')->sortable(),
                Tables\Columns\TextColumn::make('titre')->label('Titre')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('type')->label('Type')->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Media::$statuses[$state] ?? (string) $state),
            ])
            ->defaultSort('saison')
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Ajouter un épisode'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Aucun épisode')
            ->emptyStateIcon('heroicon-o-queue-list');
    }
}
