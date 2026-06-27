<?php

declare(strict_types=1);

namespace App\Livewire\Events;

use App\Models\Category;
use App\Models\Event;
use App\Models\Pays;
use App\Services\Events\EventSearchService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Listing réactif /evenements : recherche, filtres (statut temporel / type / pays),
 * tri, « charger plus ». Calqué sur App\Livewire\Projets\GrilleProjets.
 */
class GrilleEvents extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $q = '';

    #[Url(as: 'statut', except: '')]
    public string $statut = '';

    #[Url(as: 'type', except: '')]
    public string $type = '';

    #[Url(as: 'pays', except: '')]
    public string $pays = '';

    #[Url(as: 'tri', except: '')]
    public string $tri = '';

    #[Url(as: 'per_page', except: 12)]
    public int $perPage = 12;

    public function mount(): void
    {
        if ($this->perPage <= 0) {
            $this->perPage = (int) config('events.per_page', 12);
        }
    }

    /**
     * Tout changement de filtre/recherche/tri réinitialise la pagination ET ramène
     * perPage au pas initial (contrat livewire-grille-events.md) — sinon un « charger
     * plus » accumulé resterait gonflé après un changement de filtre.
     */
    public function updating($name, $value): void
    {
        if (in_array($name, ['q', 'statut', 'type', 'pays', 'tri'], true)) {
            $this->resetPage();
            $this->perPage = (int) config('events.per_page', 12);
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['q', 'statut', 'type', 'pays', 'tri']);
        $this->resetPage();
    }

    public function chargerPlus(): void
    {
        $this->perPage += (int) config('events.per_page_step', 12);
    }

    public function render()
    {
        $perPage = $this->perPage > 0 ? $this->perPage : (int) config('events.per_page', 12);
        $service = app(EventSearchService::class);

        $events = $service
            ->recherche([
                'q' => $this->q,
                'statut' => $this->statut,
                'type' => $this->type,
                'pays' => $this->pays,
                'tri' => $this->tri,
            ])
            ->with(['category', 'pays', 'medias'])
            ->paginate($perPage);

        // Comptes globaux par statut temporel (mêmes filtres SAUF statut), pour les
        // en-têtes de sections du listing groupé.
        $base = $service->recherche([
            'q' => $this->q,
            'type' => $this->type,
            'pays' => $this->pays,
        ]);
        $counts = [
            'ongoing' => (clone $base)->ongoing()->count(),
            'upcoming' => (clone $base)->upcoming()->count(),
            'past' => (clone $base)->past()->count(),
        ];

        return view('livewire.events.grille-events', [
            'events' => $events,
            'counts' => $counts,
            'statutOptions' => Event::$temporalStatuses,
            'typeOptions' => Category::query()
                ->where('online', 1)->where('type', 'event')->orderBy('position')
                ->get(['id', 'name']),
            'paysOptions' => $this->paysOptions(),
            'triOptions' => [
                'proche' => 'Date la plus proche',
                'recent' => 'Plus récents',
            ],
        ]);
    }

    /**
     * Pays distincts référencés par des événements publiés.
     *
     * @return array<int,string>
     */
    private function paysOptions(): array
    {
        $paysIds = Event::query()->published()
            ->whereNotNull('pays_id')
            ->distinct()->pluck('pays_id')->all();

        if ($paysIds === []) {
            return [];
        }

        return Pays::query()->whereIn('id', $paysIds)->orderBy('name')
            ->pluck('name', 'id')->all();
    }
}
