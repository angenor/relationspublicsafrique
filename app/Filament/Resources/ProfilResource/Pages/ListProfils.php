<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProfilResource\Pages;

use App\Filament\Resources\ProfilResource;
use App\Imports\ProfilsImport;
use App\Models\Profil;
use App\Services\Annuaire\ProfilExportService;
use App\Services\Annuaire\ProfilImportService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Gate;

class ListProfils extends ListRecords
{
    protected static string $resource = ProfilResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\CreateAction::make(),
        ];

        if (Gate::allows('export', Profil::class)) {
            $actions[] = Actions\Action::make('exporter_csv')
                ->label('Exporter CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => app(ProfilExportService::class)->export([], 'csv'));

            $actions[] = Actions\Action::make('exporter_xlsx')
                ->label('Exporter XLSX')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => app(ProfilExportService::class)->export([], 'xlsx'));
        }

        if (Gate::allows('import', Profil::class)) {
            $actions[] = Actions\Action::make('importer')
                ->label('Importer')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Forms\Components\FileUpload::make('fichier')
                        ->label('Fichier CSV ou XLSX')
                        ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->required()
                        ->disk('local')
                        ->directory('imports/annuaire'),
                    Forms\Components\Radio::make('strategie_doublon')
                        ->label('Doublons (email connu)')
                        ->options([
                            'mettre_a_jour' => 'Mettre à jour',
                            'ignorer' => 'Ignorer',
                            'creer' => 'Créer quand même',
                        ])
                        ->default('mettre_a_jour')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $path = storage_path('app/'.$data['fichier']);
                    $rapport = app(ProfilImportService::class)->import($path, $data['strategie_doublon']);

                    Notification::make()
                        ->title('Import terminé')
                        ->body("Lues : {$rapport->lues} · Créées : {$rapport->creees} · MAJ : {$rapport->misesAJour} · Ignorées : {$rapport->ignorees} · Erreurs : ".count($rapport->erreurs))
                        ->success()
                        ->send();
                });
        }

        return $actions;
    }
}
