<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class EventsWidget extends Component
{
    public $events;
    public $featuredEvents;

    public function mount()
    {
        $this->loadEvents();
    }

    public function loadEvents()
    {
        // Charger les événements à venir (limités à 6)
        $this->events = Event::with(['category', 'user'])
            ->online()
            ->published()
            ->upcoming()
            ->orderBy('start_date', 'asc')
            ->take(6)
            ->get();

        // Charger les événements mis en avant (limités à 3)
        $this->featuredEvents = Event::with(['category', 'user'])
            ->featured()
            ->online()
            ->published()
            ->orderBy('start_date', 'asc')
            ->take(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.events-widget');
    }
}
