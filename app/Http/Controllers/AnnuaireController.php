<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Profil;
use App\Models\Pays;

class AnnuaireController extends Controller
{
    /**
     * Afficher la page "Rejoindre l'annuaire"
     */
    public function rejoindre(): View
    {
        return view('annuaire.rejoindre');
    }

    /**
     * Afficher la page "Suggérer une adhésion"
     */
    public function suggerer(): View
    {
        return view('annuaire.suggerer');
    }

    /**
     * Afficher la page "Consulter l'annuaire" avec les profils
     */
    public function consulter(Request $request): View
    {
        $pageTitle = 'Annuaire';
        $pagesousTitle = 'Consulter l\'annuaire';

        // Récupérer les paramètres de filtrage
        $name = $request->get('name', '');
        $pays = $request->get('pays', '');

        // Construire la requête
        $profils = Profil::query()
            ->when($name, function ($query) use ($name) {
                $query->where('nom', 'like', '%' . $name . '%')
                    ->orWhere('prenom', 'like', '%' . $name . '%');
            })
            ->when($pays, function ($query) use ($pays) {
                $query->whereHas('pays', function ($q) use ($pays) {
                    $q->where('id', '=', $pays);
                });
            })
            ->where(['online' => 1])
            ->orderBy('nom', 'asc')
            ->paginate(12);

        $paysList = Pays::where(['online' => 1])->orderBy('name', 'asc')->get();

        return view('annuaire.consulter', compact('profils', 'paysList', 'pageTitle', 'pagesousTitle', 'name', 'pays'));
    }

    /**
     * Afficher la page "Appartenir" - intégrée dans l'annuaire
     */
    public function appartenir(Request $request): View
    {
        $pageTitle = 'Appartenir';
        $pagesousTitle = 'Rejoindre la communauté des communicants d\'Afrique';

        // Récupérer les paramètres de filtrage
        $name = $request->get('name', '');
        $pays = $request->get('pays', '');

        // Construire la requête
        $profils = Profil::query()
            ->when($name, function ($query) use ($name) {
                $query->where('nom', 'like', '%' . $name . '%')
                    ->orWhere('prenom', 'like', '%' . $name . '%');
            })
            ->when($pays, function ($query) use ($pays) {
                $query->whereHas('pays', function ($q) use ($pays) {
                    $q->where('id', '=', $pays);
                });
            })
            ->where(['online' => 1])
            ->orderBy('nom', 'asc')
            ->paginate(12);

        $paysList = Pays::where(['online' => 1])->orderBy('name', 'asc')->get();

        return view('annuaire.appartenir', compact('profils', 'paysList', 'pageTitle', 'pagesousTitle', 'name', 'pays'));
    }
}
