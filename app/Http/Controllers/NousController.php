<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NousController extends Controller
{
    /**
     * Affiche la page Mission
     */
    public function mission(): View
    {
        $mission = Post::where('type', 'mission')
            ->where('online', true)
            ->first();

        return view('nous.mission', compact('mission'));
    }

    /**
     * Affiche la page Vision
     */
    public function vision(): View
    {
        $vision = Post::where('type', 'vision')
            ->where('online', true)
            ->first();

        return view('nous.vision', compact('vision'));
    }

    /**
     * Affiche la page Historique
     */
    public function historique(): View
    {
        $historique = Post::where('type', 'historique')
            ->where('online', true)
            ->first();

        return view('nous.historique', compact('historique'));
    }
}
