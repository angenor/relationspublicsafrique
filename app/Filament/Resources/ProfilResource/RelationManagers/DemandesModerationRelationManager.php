<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProfilResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DemandesModerationRelationManager extends RelationManager
{
    protected static string $relationship = 'demandesModeration';

    protected static ?string $title = 'Historique de modération';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('decision')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Date')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('decision')->badge()->colors([
                    'warning' => 'soumis',
                    'success' => 'approuve',
                    'danger' => 'rejete',
                    'gray' => 'archive',
                ]),
                Tables\Columns\TextColumn::make('moderateur.name')->label('Modérateur'),
                Tables\Columns\TextColumn::make('soumetteur.name')->label('Soumis par'),
                Tables\Columns\TextColumn::make('motif')->limit(80)->wrap(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
