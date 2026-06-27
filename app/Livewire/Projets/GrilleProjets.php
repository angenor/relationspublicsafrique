<?php

declare(strict_types=1);

namespace App\Livewire\Projets;

use App\Models\Category;
use App\Models\Pays;
use App\Models\Projet;
use App\Services\Projets\ProjetSearchService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Listing réactif /projets : recherche, filtres (thématique/zone/statut), tri,
 * « charger plus ». Calqué sur App\Livewire\Media\GrilleMedias.
 */
class GrilleProjets extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $q = '';

    #[Url(as: 'thematique', except: '')]
    public string $thematique = '';

    #[Url(as: 'zone', except: '')]
    public string $zone = '';

    #[Url(as: 'statut', except: '')]
    public string $statut = '';

    #[Url(as: 'tri', except: '')]
    public string $tri = '';

    #[Url(as: 'per_page', except: 12)]
    public int $perPage = 12;

    public function mount(): void
    {
        if ($this->perPage <= 0) {
            $this->perPage = (int) config('projets.per_page', 12);
        }
    }

    /**
     * Tout changement de filtre/recherche/tri réinitialise la pagination ET ramène
     * perPage au pas initial (contrat livewire-grille-projets.md) — sinon un « charger
     * plus » accumulé resterait gonflé après un changement de filtre.
     */
    public function updating($name, $value): void
    {
        if (in_array($name, ['q', 'thematique', 'zone', 'statut', 'tri'], true)) {
            $this->resetPage();
            $this->perPage = (int) config('projets.per_page', 12);
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['q', 'thematique', 'zone', 'statut', 'tri']);
        $this->resetPage();
    }

    public function chargerPlus(): void
    {
        $this->perPage += (int) config('projets.per_page_step', 12);
    }

    public function render()
    {
        $perPage = $this->perPage > 0 ? $this->perPage : (int) config('projets.per_page', 12);

        $projets = app(ProjetSearchService::class)
            ->recherche([
                'q' => $this->q,
                'thematique' => $this->thematique,
                'zone' => $this->zone,
                'statut' => $this->statut,
                'tri' => $this->tri,
            ])
            ->with(['categories', 'pays'])
            ->paginate($perPage);

        return view('livewire.projets.grille-projets', [
            'projets' => $projets,
            'thematiqueOptions' => Category::query()
                ->where('online', 1)->where('type', 'projet')->orderBy('position')
                ->get(['id', 'name']),
            'zoneOptions' => $this->zoneOptions(),
            'statutOptions' => Projet::$statuts,
            'triOptions' => [
                'recent' => 'Plus récents',
                'alpha' => 'Alphabétique',
            ],
        ]);
    }

    /**
     * Options de zone : pays distincts référencés par des projets publiés,
     * puis les portées non nationales effectivement présentes.
     *
     * @return array<string,string> token => libellé
     */
    private function zoneOptions(): array
    {
        $options = [];

        $paysIds = Projet::query()->published()
            ->where('portee', 'pays')->whereNotNull('pays_id')
            ->distinct()->pluck('pays_id')->all();

        if ($paysIds !== []) {
            foreach (Pays::query()->whereIn('id', $paysIds)->orderBy('name')->get(['id', 'name']) as $pays) {
                $options['pays:'.$pays->id] = $pays->name;
            }
        }

        foreach (['regional' => 'Régional', 'continental' => 'Continental'] as $portee => $label) {
            if (Projet::query()->published()->where('portee', $portee)->exists()) {
                $options['portee:'.$portee] = $label;
            }
        }

        return $options;
    }
}
