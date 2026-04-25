<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EventRegistrationModal extends Component
{
    public Event $event;
    public bool $showModal = false;
    public string $notes = '';

    protected $rules = [
        'notes' => 'nullable|string|max:500',
    ];

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function openModal()
    {
        if (!Auth::check()) {
            // Rediriger vers la page de connexion
            return redirect()->route('login');
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->notes = '';
        $this->resetErrorBag();
    }

    public function register()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate();

        // Vérifier si l'utilisateur est déjà inscrit
        if ($this->event->isUserRegistered(Auth::id())) {
            session()->flash('error', 'Vous êtes déjà inscrit à cet événement.');
            return;
        }

        // Vérifier si les inscriptions sont ouvertes
        if (!$this->event->can_register) {
            session()->flash('error', 'Les inscriptions ne sont pas ouvertes pour cet événement.');
            return;
        }

        // Vérifier la date limite d'inscription
        if ($this->event->registration_deadline && $this->event->registration_deadline->isPast()) {
            session()->flash('error', 'La date limite d\'inscription est dépassée.');
            return;
        }

        // Vérifier le nombre maximum de participants
        if ($this->event->max_participants && $this->event->current_participants >= $this->event->max_participants) {
            session()->flash('error', 'Cet événement est complet.');
            return;
        }

        try {
            EventRegistration::create([
                'event_id' => $this->event->id,
                'user_id' => Auth::id(),
                'status' => 'registered',
                'notes' => $this->notes,
                'registered_at' => now(),
            ]);

            session()->flash('success', 'Votre inscription a été enregistrée avec succès !');
            $this->closeModal();

            // Rafraîchir la page pour mettre à jour les informations
            return redirect()->route('events.show', ['id' => $this->event->id, 'slug' => $this->event->slug]);
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue lors de votre inscription. Veuillez réessayer.');
        }
    }

    public function cancelRegistration()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $registration = $this->event->registrations()
            ->where('user_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($registration) {
            $registration->update(['status' => 'cancelled']);
            session()->flash('success', 'Votre inscription a été annulée.');
            return redirect()->route('events.show', ['id' => $this->event->id, 'slug' => $this->event->slug]);
        }
    }

    public function render()
    {
        $isRegistered = Auth::check() ? $this->event->isUserRegistered(Auth::id()) : false;
        $canRegister = $this->event->can_register &&
            (!$this->event->registration_deadline || $this->event->registration_deadline->isFuture()) &&
            (!$this->event->max_participants || $this->event->current_participants < $this->event->max_participants);

        return view('livewire.event-registration-modal', [
            'isRegistered' => $isRegistered,
            'canRegister' => $canRegister,
        ]);
    }
}
