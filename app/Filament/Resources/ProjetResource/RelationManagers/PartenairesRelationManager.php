<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProjetResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PartenairesRelationManager extends RelationManager
{
    protected static string $relationship = 'partenaires';

    protected static ?string $title = 'Partenaires';

    protected static ?string $modelLabel = 'partenaire';

    protected static ?string $pluralModelLabel = 'partenaires';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nom')
            ->defaultSort('partenaire_projet.position')
            ->columns([
                Tables\Columns\ImageColumn::make('logo')->label('Logo')->disk('public')->size(50),
                Tables\Columns\TextColumn::make('nom')->label('Nom')->searchable(),
                Tables\Columns\TextColumn::make('url')->label('Site')->limit(30)->toggleable(),
                Tables\Columns\TextColumn::make('pivot.position')->label('Ordre'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Partenaire')
                            ->searchable(),
                        Forms\Components\TextInput::make('position')->label('Ordre')->numeric()->default(0),
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ])
            ->emptyStateHeading('Aucun partenaire associé');
    }
}
