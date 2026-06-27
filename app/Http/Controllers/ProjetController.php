<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Projet;

/**
 * Vitrine publique des projets (feature 003-projets-vitrine).
 * - index : listing en cards filtrables (composant Livewire GrilleProjets).
 * - show  : page détaillée d'un projet publié (résolution restreinte aux publiés).
 */
class ProjetController extends Controller
{
    /** Listing /projets — le filtrage réactif est porté par <livewire:projets.grille-projets />. */
    public function index()
    {
        return view('projets.index');
    }

    /**
     * Détail /projets/{slug}. Résolution restreinte aux projets publiés (FR-016) :
     * un brouillon, un projet programmé ou un slug inconnu donne 404.
     */
    public function show(string $slug)
    {
        $projet = Projet::published()
            ->where('slug', $slug)
            ->with(['categories', 'pays', 'resultats', 'medias', 'partenaires', 'temoignages'])
            ->firstOrFail();

        return view('projets.show', compact('projet'));
    }
}
