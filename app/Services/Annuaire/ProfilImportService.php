<?php

declare(strict_types=1);

namespace App\Services\Annuaire;

use App\Imports\ProfilsImport;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;

class ProfilImportService
{
    /**
     * @param UploadedFile|string $source UploadedFile ou chemin absolu (utilisé pour les imports différés Filament).
     */
    public function import(UploadedFile|string $source, string $strategieDoublon = 'mettre_a_jour'): RapportImport
    {
        $import = new ProfilsImport($strategieDoublon);

        if ($source instanceof UploadedFile) {
            Excel::import($import, $source);
        } else {
            Excel::import($import, $source);
        }

        // Capture des échecs de validation (WithValidation) dans le rapport.
        foreach ($import->failures() as $failure) {
            foreach ($failure->errors() as $message) {
                $import->rapport->ajouterErreur(
                    (int) $failure->row(),
                    $failure->attribute(),
                    $message,
                );
            }
        }

        return $import->rapport;
    }
}
