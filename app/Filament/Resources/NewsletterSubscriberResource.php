<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterSubscriberResource\Pages;
use App\Models\NewsletterSubscriber;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Média';

    protected static ?string $label = 'Abonné newsletter';

    protected static ?string $pluralLabel = 'Newsletter';

    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool
    {
        return false; // opt-in public uniquement.
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->sortable()->copyable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->formatStateUsing(fn (?string $state): string => NewsletterSubscriber::$statuses[$state] ?? (string) $state)
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'gray' => 'unsubscribed',
                    ]),
                Tables\Columns\TextColumn::make('confirmed_at')->label('Confirmé le')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Inscrit le')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(NewsletterSubscriber::$statuses),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Exporter (CSV)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (): StreamedResponse => response()->streamDownload(function (): void {
                        $out = fopen('php://output', 'w');
                        fputcsv($out, ['email', 'statut', 'confirme_le', 'inscrit_le']);
                        NewsletterSubscriber::query()->orderBy('id')->cursor()->each(function (NewsletterSubscriber $s) use ($out): void {
                            fputcsv($out, [
                                $s->email,
                                $s->status,
                                optional($s->confirmed_at)->toDateTimeString(),
                                optional($s->created_at)->toDateTimeString(),
                            ]);
                        });
                        fclose($out);
                    }, 'newsletter-abonnes.csv')),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletterSubscribers::route('/'),
        ];
    }
}
