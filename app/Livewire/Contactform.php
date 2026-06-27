<?php

namespace App\Livewire;

use App\Models\Projet;
use Livewire\Component;

class Contactform extends Component
{
    public $name = '';

    public $email = '';

    public $message = '';

    public $number = '';

    /** Référence du projet à l'origine de la prise de contact (R10/FR-015). */
    public $projet = '';

    /**
     * Pré-remplit (de façon non destructive) la prise de contact lorsqu'elle est
     * initiée depuis la page détail d'un projet (/contact?projet={slug}).
     * Le contact sans paramètre reste strictement inchangé (non-régression).
     */
    public function mount(): void
    {
        $slug = request()->query('projet');
        if (empty($slug)) {
            return;
        }

        $this->projet = (string) $slug;

        if (trim((string) $this->message) === '') {
            $titre = Projet::published()->where('slug', $this->projet)->value('titre') ?? $this->projet;
            $this->message = 'Bonjour, je vous contacte au sujet du projet « '.$titre.' ».';
        }
    }

    public function submit()
    {
        $validated = $this->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
            'number' => 'required',
        ]);

        dd($validated);
    }

    public function render()
    {
        return view('livewire.contactform');
    }
}
