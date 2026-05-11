<?php

namespace App\Livewire\Annuaire;

use App\Models\DomaineExpertise;
use App\Models\Pays;
use App\Models\Tag;
use App\Services\Annuaire\ProfilSearchService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProfilGrid extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $q = '';

    #[Url(as: 'pays', except: '')]
    public string $pays = '';

    #[Url(as: 'type', except: '')]
    public string $type = '';

    /** @var array<int,string> */
    #[Url(as: 'domaine', except: [])]
    public array $domaines = [];

    /** @var array<int,string> */
    #[Url(as: 'tag', except: [])]
    public array $tags = [];

    #[Url(as: 'tri', except: '')]
    public string $tri = '';

    #[Url(as: 'mode', except: 'grille')]
    public string $mode = 'grille';

    #[Url(as: 'per_page', except: 24)]
    public int $perPage = 24;

    public function updating($name, $value): void
    {
        if (in_array($name, ['q', 'pays', 'type', 'domaines', 'tags', 'tri', 'mode', 'perPage'], true)) {
            $this->resetPage();
        }
    }

    public function setMode(string $mode): void
    {
        $this->mode = in_array($mode, ['grille', 'liste'], true) ? $mode : 'grille';
    }

    public function resetFilters(): void
    {
        $this->reset(['q', 'pays', 'type', 'domaines', 'tags', 'tri']);
        $this->resetPage();
    }

    public function render()
    {
        $perPage = $this->perPage > 0 ? $this->perPage : (int) config('annuaire.per_page', 24);

        $profils = app(ProfilSearchService::class)
            ->recherche([
                'q' => $this->q,
                'pays' => $this->pays,
                'type' => $this->type,
                'domaine' => $this->domaines,
                'tag' => $this->tags,
                'tri' => $this->tri,
            ])
            ->with(['pays', 'domainesExpertise', 'tags', 'liensExternes'])
            ->paginate($perPage);

        return view('livewire.annuaire.profil-grid', [
            'profils' => $profils,
            'paysOptions' => Pays::query()->where('online', 1)->orderBy('name')->get(['id', 'slug', 'name']),
            'domaineOptions' => DomaineExpertise::query()->orderBy('libelle')->get(['slug', 'libelle']),
            'tagOptions' => Tag::query()->orderBy('libelle')->get(['slug', 'libelle']),
            'typeOptions' => [
                'expert' => 'Expert',
                'etudiant' => 'Étudiant',
                'alumni' => 'Alumni',
                'partenaire' => 'Partenaire',
                'autre' => 'Autre',
            ],
            'triOptions' => [
                'alpha' => 'Ordre alphabétique',
                'recent' => 'Plus récents',
                'pertinence' => 'Pertinence',
            ],
        ]);
    }
}
