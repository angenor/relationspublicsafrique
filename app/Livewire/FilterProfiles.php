<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Profil;
use App\Models\Pays;
use Livewire\WithPagination;
use Livewire\Attributes\Url;


class FilterProfiles extends Component
{
    use WithPagination;

    #[Url]
    public $name = '';

    #[Url]
    public $pays;

    public $pageTitle = 'Appartenir';
    public $pagesousTitle = 'Appartenir';



    public function render()
    {



        $profils = Profil::query()
            ->when($this->name, function ($query) {
                $query->where('nom', 'like', '%' . $this->name . '%')
                    ->orWhere('prenom', 'like', '%' . $this->name . '%');
            })
            ->when($this->pays, function ($query) {
                $query->whereHas('pays', function ($q) {
                    $q->where('id', '=',   $this->pays);
                });
            })
            ->where(['online' => 1])
            ->orderBy('name', 'asc')
            ->paginate(12); // Nombre d'éléments par page

        $paysList = Pays::where(['online' => 1])->orderBy('name', 'asc')->get(); // Récupérer la liste des pays pour les options de sélection


        return view('livewire.filter-profiles', compact('profils', 'paysList'));
    }
}
