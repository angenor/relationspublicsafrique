<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\MediaCommentResource\Pages;
use App\Models\MediaComment;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MediaCommentResource extends Resource
{
    protected static ?string $model = MediaComment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Média';

    protected static ?string $label = 'Commentaire';

    protected static ?string $pluralLabel = 'Modération commentaires';

    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false;
    }

    /** Badge de navigation = nombre de commentaires en attente. */
    public static function getNavigationBadge(): ?string
    {
        $pending = MediaComment::query()->where('status', 'pending')->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('media.titre')->label('Contenu')->limit(30)->searchable(),
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMediaComments::route('/'),
        ];
    }
}
