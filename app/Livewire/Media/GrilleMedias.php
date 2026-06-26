<?php

declare(strict_types=1);

namespace App\Livewire\Media;

use App\Models\Category;
use App\Models\Media;
use App\Models\Pays;
use App\Models\User;
use App\Services\Media\MediaSearchService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class GrilleMedias extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $q = '';

    #[Url(as: 'type', except: '')]
    public string $type = '';

    #[Url(as: 'cat', except: '')]
    public string $categorie = '';

    #[Url(as: 'pays', except: '')]
    public string $pays = '';

    #[Url(as: 'auteur', except: '')]
    public string $auteur = '';

    #[Url(as: 'tri', except: '')]
    public string $tri = '';

    #[Url(as: 'mode', except: 'grille')]
    public string $mode = 'grille';

    #[Url(as: 'per_page', except: 12)]
    public int $perPage = 12;

    public function mount(): void
    {
        if ($this->perPage <= 0) {
            $this->perPage = (int) config('media.per_page', 12);
        }
    }

    /** Réinitialise la pagination à tout changement de filtre — SAUF perPage (« charger plus »). */
    public function updating($name, $value): void
    {
        if (in_array($name, ['q', 'type', 'categorie', 'pays', 'auteur', 'tri', 'mode'], true)) {
            $this->resetPage();
        }
    }

    public function setMode(string $mode): void
    {
        $this->mode = in_array($mode, ['grille', 'liste'], true) ? $mode : 'grille';
    }

    public function resetFilters(): void
    {
        $this->reset(['q', 'type', 'categorie', 'pays', 'auteur', 'tri']);
        $this->resetPage();
    }

    public function loadMore(): void
    {
        $this->perPage += (int) config('media.per_page_step', 12);
    }

    public function render()
    {
        $perPage = $this->perPage > 0 ? $this->perPage : (int) config('media.per_page', 12);

        $medias = app(MediaSearchService::class)
            ->recherche([
                'q' => $this->q,
                'type' => $this->type,
                'categorie' => $this->categorie,
                'pays' => $this->pays,
                'auteur' => $this->auteur,
                'tri' => $this->tri,
            ])
            ->with(['categories', 'tags', 'auteurPrincipal', 'pays', 'serie'])
            ->paginate($perPage);

        return view('livewire.media.grille-medias', [
            'medias' => $medias,
            'typeOptions' => Media::$types,
            'categorieOptions' => Category::query()
                ->where('online', 1)->where('type', 'media')->orderBy('position')
                ->get(['slug', 'name']),
            'paysOptions' => Pays::query()->where('online', 1)->orderBy('name')->get(['slug', 'name']),
            'auteurOptions' => User::query()
                ->whereIn('id', Media::query()->published()->whereNotNull('user_id')->distinct()->pluck('user_id'))
                ->orderBy('name')
                ->get(['id', 'name']),
            'triOptions' => [
                'recent' => 'Plus récents',
                'populaire' => 'Plus populaires',
                'recommande' => 'Recommandés',
            ],
        ]);
    }
}
