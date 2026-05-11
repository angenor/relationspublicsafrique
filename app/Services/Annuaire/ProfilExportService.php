<?php

declare(strict_types=1);

namespace App\Services\Annuaire;

use App\Exports\ProfilsExport;
use Maatwebsite\Excel\Excel as ExcelType;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfilExportService
{
    /**
     * @param array<string,mixed> $filtres
     * @param 'csv'|'xlsx' $format
     */
    public function export(array $filtres = [], string $format = 'csv'): BinaryFileResponse
    {
        $extension = $format === 'xlsx' ? 'xlsx' : 'csv';
        $writer = $format === 'xlsx' ? ExcelType::XLSX : ExcelType::CSV;
        $filename = 'profils-'.now()->format('Ymd-His').'.'.$extension;

        return Excel::download(new ProfilsExport($filtres), $filename, $writer);
    }

    /**
     * Version "raw" qui retourne le contenu (utile pour tester le BOM en mémoire).
     */
    public function exportRaw(array $filtres = [], string $format = 'csv'): string
    {
        $writer = $format === 'xlsx' ? ExcelType::XLSX : ExcelType::CSV;

        return Excel::raw(new ProfilsExport($filtres), $writer);
    }
}
