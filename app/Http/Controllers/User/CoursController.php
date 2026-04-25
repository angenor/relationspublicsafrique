<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CoursController extends Controller
{

    public function index()
    {
        // Pour l'instant, on retourne une collection vide car la table achat_cours n'existe pas encore
        // TODO: Créer la migration et le modèle pour achat_cours
        $cours = collect([]);

        return view('user.cours.index', compact('cours'));
    }
}
