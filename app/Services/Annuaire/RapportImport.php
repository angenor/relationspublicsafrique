<?php

declare(strict_types=1);

namespace App\Services\Annuaire;

class RapportImport
{
    public int $lues = 0;
    public int $creees = 0;
    public int $misesAJour = 0;
    public int $ignorees = 0;
    /** @var array<int, array{ligne:int, colonne:?string, message:string}> */
    public array $erreurs = [];

    public function ajouterErreur(int $ligne, ?string $colonne, string $message): void
    {
        $this->erreurs[] = [
            'ligne' => $ligne,
            'colonne' => $colonne,
            'message' => $message,
        ];
    }

    public function toArray(): array
    {
        return [
            'lues' => $this->lues,
            'creees' => $this->creees,
            'misesAJour' => $this->misesAJour,
            'ignorees' => $this->ignorees,
            'erreurs' => $this->erreurs,
        ];
    }
}
