<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\ConsentementProfil;
use App\Models\Profil;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AnnuaireConsentements extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Annuaire';
    protected static ?string $title = 'Audit des consentements';
    protected static ?string $slug = 'annuaire-consentements';

    protected static string $view = 'filament.pages.annuaire-consentements';

    public static function canAccess(): bool
    {
        return auth()->check() && \Gate::allows('viewAudit', Profil::class);
    }

    protected function getTableQuery(): Builder
    {
        return ConsentementProfil::query()->with(['profil', 'attesteur']);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('profil.fullname')->label('Profil')->searchable(),
            Tables\Columns\TextColumn::make('attesteur.name')->label('Attesté par')->searchable(),
            Tables\Columns\TextColumn::make('atteste_le')->label('Attesté le')->dateTime()->sortable(),
            Tables\Columns\TextColumn::make('email_notification_envoye_a')->label('Email envoyé à'),
            Tables\Columns\TextColumn::make('email_notification_envoye_le')->label('Envoyé le')->dateTime(),
            Tables\Columns\TextColumn::make('jeton_expire_le')
                ->label('Jeton')
                ->dateTime()
                ->badge()
                ->color(fn ($state) => $state && $state->isPast() ? 'danger' : 'success')
                ->formatStateUsing(fn ($state) => $state ? ($state->isPast() ? 'Expiré le '.$state->format('d/m/Y') : 'Valide jusqu\'au '.$state->format('d/m/Y')) : '—'),
            Tables\Columns\TextColumn::make('retrait_demande_le')->label('Retrait demandé le')->dateTime(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('atteste_par')
                ->label('Admin')
                ->relationship('attesteur', 'name'),
            Tables\Filters\Filter::make('periode')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('debut')->label('Du'),
                    \Filament\Forms\Components\DatePicker::make('fin')->label('Au'),
                ])
                ->query(function (Builder $q, array $data) {
                    return $q
                        ->when($data['debut'] ?? null, fn ($q, $d) => $q->whereDate('atteste_le', '>=', $d))
                        ->when($data['fin'] ?? null, fn ($q, $d) => $q->whereDate('atteste_le', '<=', $d));
                }),
        ];
    }

    protected function getTableDefaultSortColumn(): ?string
    {
        return 'atteste_le';
    }

    protected function getTableDefaultSortDirection(): ?string
    {
        return 'desc';
    }
}
