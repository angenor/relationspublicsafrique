<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProfilResource\RelationManagers;

use App\Models\HistoriqueProfil;
use App\Services\Annuaire\ProfilHistoriqueService;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class HistoriqueRelationManager extends RelationManager
{
    protected static string $relationship = 'historiques';

    protected static ?string $title = 'Historique des modifications';

    protected static ?string $icon = 'heroicon-o-clock';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('action')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Auteur')
                    ->default('Système')
                    ->searchable(),
                Tables\Columns\TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->colors([
                        'success' => 'cree',
                        'info' => 'modifie',
                        'warning' => 'statut_change',
                        'danger' => 'supprime',
                        'gray' => 'restaure',
                        'primary' => 'approuve',
                    ]),
                Tables\Columns\TextColumn::make('diff')
                    ->label('Champs modifiés')
                    ->formatStateUsing(function ($state): string {
                        if (! is_array($state) || empty($state)) {
                            return '—';
                        }
                        $keys = array_keys($state);
                        $shown = array_slice($keys, 0, 5);
                        $rest = max(count($keys) - count($shown), 0);

                        return implode(', ', $shown).($rest > 0 ? " (+{$rest})" : '');
                    })
                    ->wrap()
                    ->tooltip(fn ($record) => is_array($record->diff) ? implode(', ', array_keys($record->diff)) : null),
                Tables\Columns\TextColumn::make('ip')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('voir')
                    ->label('Voir détail')
                    ->icon('heroicon-m-eye')
                    ->modalHeading(fn (HistoriqueProfil $record): string => "Détail historique #{$record->id}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fermer')
                    ->modalContent(fn (HistoriqueProfil $record) => new HtmlString(
                        view('filament.annuaire.historique-detail', [
                            'entree' => $record,
                        ])->render()
                    )),
                Action::make('restaurer')
                    ->label('Restaurer')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Restaurer cette version ?')
                    ->modalDescription('Les valeurs « avant » consignées seront réappliquées au profil. Cette action est elle-même journalisée.')
                    ->modalSubmitActionLabel('Restaurer')
                    ->visible(fn (HistoriqueProfil $record): bool => $this->canRestaurer($record))
                    ->action(function (HistoriqueProfil $record): void {
                        /** @var \App\Models\User $admin */
                        $admin = Auth::user();
                        app(ProfilHistoriqueService::class)->restaurer($record, $admin);

                        \Filament\Notifications\Notification::make()
                            ->title('Profil restauré')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    private function canRestaurer(HistoriqueProfil $record): bool
    {
        $user = Auth::user();
        if (! $user || ($user->type ?? null) !== 'admin') {
            return false;
        }

        // On ne propose la restauration que sur les entrées contenant un diff réel.
        return is_array($record->diff) && ! empty($record->diff)
            && in_array($record->action, ['modifie', 'statut_change'], true);
    }
}
