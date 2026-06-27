<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Vitrine publique des événements (feature 004-evenements).
 *
 * Gate de visibilité : `published()` UNIQUEMENT (research R1). L'ancien
 * `->online()` est retiré de toutes les routes publiques — il masquait à tort
 * les événements présentiels publiés (`online` = format, pas visibilité).
 */
class EventController extends Controller
{
    /**
     * Listing /evenements — le filtrage réactif (statut temporel, type, pays,
     * tri, recherche, charger-plus) est porté par <livewire:events.grille-events />.
     */
    public function index(Request $request): View
    {
        $featuredEvents = Event::query()
            ->with(['category', 'pays'])
            ->featured()
            ->published()
            ->orderBy('start_date', 'asc')
            ->take((int) config('events.a_la_une', 3))
            ->get();

        $upcomingCount = Event::published()->upcoming()->count();
        $ongoingCount = Event::published()->ongoing()->count();
        $pastCount = Event::published()->past()->count();

        return view('events.index', compact('featuredEvents', 'upcomingCount', 'ongoingCount', 'pastCount'));
    }

    /**
     * Détail /evenements/{slug}-{id}. Résolution restreinte aux événements
     * publiés (FR-012) : un brouillon, un statut non publié ou un slug/id
     * inconnu donne 404.
     */
    public function show(string $slug, int $id): View
    {
        $event = Event::query()
            ->with(['category', 'pays', 'user', 'speakers', 'medias'])
            ->where('id', $id)
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $event->increment('view');

        $similarEvents = Event::query()
            ->with(['category', 'pays', 'medias'])
            ->where('id', '!=', $event->id)
            ->where('category_id', $event->category_id)
            ->published()
            ->orderBy('start_date', 'desc')
            ->take(3)
            ->get();

        return view('events.show', compact('event', 'similarEvents'));
    }

    /**
     * Affiche les événements publiés d'une catégorie (type d'événement).
     */
    public function category(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $events = Event::query()
            ->with(['category', 'pays', 'medias'])
            ->where('category_id', $category->id)
            ->published()
            ->orderBy('start_date', 'asc')
            ->paginate((int) config('events.per_page', 12));

        return view('events.category', compact('events', 'category'));
    }

    /**
     * Calendrier des événements publiés.
     */
    public function calendar(): View
    {
        $events = Event::query()
            ->with(['category', 'pays'])
            ->published()
            ->orderBy('start_date', 'asc')
            ->get();

        return view('events.calendar', compact('events'));
    }

    /**
     * Recherche d'événements publiés.
     */
    public function search(Request $request): View
    {
        $query = (string) $request->get('q', '');

        $events = Event::query()
            ->with(['category', 'pays', 'medias'])
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhere('location', 'like', "%{$query}%");
                });
            })
            ->published()
            ->orderBy('start_date', 'asc')
            ->paginate((int) config('events.per_page', 12));

        return view('events.search', compact('events', 'query'));
    }
}
