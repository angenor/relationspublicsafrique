<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfilPublicResource;
use App\Models\Pays;
use App\Models\Profil;
use App\Services\Annuaire\ProfilSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnuaireController extends Controller
{
    public function __construct(private readonly ProfilSearchService $searchService) {}

    /**
     * Page publique de l'annuaire (Livewire).
     */
    public function index(): View
    {
        return view('annuaire.index');
    }

    /**
     * API JSON publique : liste paginée.
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $criteres = $this->extractCriteres($request);
        $perPage = $this->resolvePerPage($request);

        $paginator = $this->searchService->recherche($criteres)
            ->with(['pays', 'domainesExpertise', 'tags', 'liensExternes'])
            ->orderBy('nom')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => ProfilPublicResource::collection($paginator->getCollection())->resolve($request),
            'meta' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    /**
     * API JSON publique : fiche détail.
     */
    public function apiShow(string $slug, Request $request): JsonResponse
    {
        $profil = Profil::query()
            ->publies()
            ->where('slug', $slug)
            ->with(['pays', 'domainesExpertise', 'tags', 'liensExternes'])
            ->firstOrFail();

        $resource = (new ProfilPublicResource($profil))->avecDetail();

        return response()->json([
            'success' => true,
            'data' => $resource->toArray($request),
        ]);
    }

    /**
     * @return array<string,mixed>
     */
    private function extractCriteres(Request $request): array
    {
        return [
            'q' => trim((string) $request->query('q', '')),
            'pays' => $request->query('pays'),
            'type' => $request->query('type'),
            'domaine' => (array) $request->query('domaine', []),
            'tag' => (array) $request->query('tag', []),
            'tri' => $request->query('tri'),
        ];
    }

    private function resolvePerPage(Request $request): int
    {
        $allowed = [12, 24, 48, 96];
        $perPage = (int) $request->query('per_page', (int) config('annuaire.per_page', 24));

        return in_array($perPage, $allowed, true) ? $perPage : 24;
    }

    // ----- Pages legacy conservées (rejoindre / suggerer / appartenir) -----

    public function rejoindre(): View
    {
        return view('annuaire.rejoindre');
    }

    public function suggerer(): View
    {
        return view('annuaire.suggerer');
    }

    public function appartenir(Request $request): View
    {
        $pageTitle = 'Appartenir';
        $pagesousTitle = "Rejoindre la communauté des communicants d'Afrique";

        $name = $request->get('name', '');
        $pays = $request->get('pays', '');

        $profils = Profil::query()
            ->when($name, function ($query) use ($name) {
                $query->where('nom', 'like', '%'.$name.'%')
                    ->orWhere('prenom', 'like', '%'.$name.'%');
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
