<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use App\Models\EventRegistration;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $title = 'Inscriptions';

    protected static ?string $modelLabel = 'inscription';

    protected static ?string $pluralModelLabel = 'inscriptions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Utilisateur')
                    ->options(User::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Statut')
                    ->options(EventRegistration::$statuses)
                    ->required()
                    ->default('registered'),

                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'registered',
                        'warning' => 'confirmed',
                        'primary' => 'attended',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn($state) => EventRegistration::$statuses[$state] ?? $state),

                Tables\Columns\TextColumn::make('registered_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options(EventRegistration::$statuses),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Ajouter une inscription')
                    ->modalHeading('Ajouter une inscription')
                    ->successNotificationTitle('Inscription ajoutée avec succès'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Modifier l\'inscription')
                    ->successNotificationTitle('Inscription modifiée avec succès'),

                Tables\Actions\Action::make('confirm')
                    ->label('Confirmer')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn(EventRegistration $record) => $record->update(['status' => 'confirmed']))
                    ->visible(fn(EventRegistration $record): bool => $record->status === 'registered')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmer l\'inscription')
                    ->modalDescription('Êtes-vous sûr de vouloir confirmer cette inscription ?'),

                Tables\Actions\Action::make('mark_attended')
                    ->label('Marquer présent')
                    ->icon('heroicon-o-check-circle')
                    ->color('primary')
                    ->action(fn(EventRegistration $record) => $record->update(['status' => 'attended']))
                    ->visible(fn(EventRegistration $record): bool => in_array($record->status, ['registered', 'confirmed']))
                    ->requiresConfirmation()
                    ->modalHeading('Marquer comme présent')
                    ->modalDescription('Êtes-vous sûr de vouloir marquer cette personne comme présente ?'),

                Tables\Actions\DeleteAction::make()
                    ->label('Supprimer')
                    ->successNotificationTitle('Inscription supprimée avec succès'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('confirm_selected')
                        ->label('Confirmer les sélectionnés')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn($records) => $records->each(fn($record) => $record->update(['status' => 'confirmed'])))
                        ->requiresConfirmation()
                        ->modalHeading('Confirmer les inscriptions sélectionnées')
                        ->modalDescription('Êtes-vous sûr de vouloir confirmer toutes les inscriptions sélectionnées ?'),

                    Tables\Actions\BulkAction::make('mark_attended_selected')
                        ->label('Marquer présents')
                        ->icon('heroicon-o-check-circle')
                        ->color('primary')
                        ->action(fn($records) => $records->each(fn($record) => $record->update(['status' => 'attended'])))
                        ->requiresConfirmation()
                        ->modalHeading('Marquer comme présents')
                        ->modalDescription('Êtes-vous sûr de vouloir marquer toutes les personnes sélectionnées comme présentes ?'),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Supprimer les sélectionnés'),
                ]),
            ])
            ->defaultSort('registered_at', 'desc')
            ->emptyStateHeading('Aucune inscription')
            ->emptyStateDescription('Aucun utilisateur ne s\'est encore inscrit à cet événement.')
            ->emptyStateIcon('heroicon-o-users');
    }
}
