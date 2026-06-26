<?php

declare(strict_types=1);

namespace App\Filament\Resources\MediaResource\RelationManagers;

use App\Models\MediaComment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CommentairesRelationManager extends RelationManager
{
    protected static string $relationship = 'commentaires';

    protected static ?string $title = 'Commentaires';

    protected static ?string $modelLabel = 'commentaire';

    protected static ?string $pluralModelLabel = 'commentaires';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('author_name')->label('Nom')->required()->maxLength(120),
            Forms\Components\TextInput::make('author_email')->label('Email')->email()->required(),
            Forms\Components\Textarea::make('body')->label('Commentaire')->required()->rows(4)->columnSpanFull(),
            Forms\Components\Select::make('status')->label('Statut')->options(MediaComment::$statuses)->default('pending'),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('author_name')
            ->columns([
                Tables\Columns\TextColumn::make('author_name')->label('Auteur')->searchable(),
                Tables\Columns\TextColumn::make('body')->label('Commentaire')->limit(60)->wrap(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->formatStateUsing(fn (?string $state): string => MediaComment::$statuses[$state] ?? (string) $state)
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->label('Reçu le')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(MediaComment::$statuses),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approuver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (MediaComment $record): bool => $record->status !== 'approved')
                    ->requiresConfirmation()
                    ->action(fn (MediaComment $record) => $record->approve()),
                Tables\Actions\Action::make('reject')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (MediaComment $record): bool => $record->status !== 'rejected')
                    ->requiresConfirmation()
                    ->action(fn (MediaComment $record) => $record->reject()),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Approuver la sélection')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each(fn (MediaComment $r) => $r->approve())),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Aucun commentaire');
    }
}
