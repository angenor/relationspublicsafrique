<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Affiche la liste des événements
     */
    public function index(Request $request): View
    {
        $query = Event::query()
            ->with(['category', 'user', 'pays'])
            ->online()
            ->published()
            ->orderBy('start_date', 'asc');

        // Filtrage par catégorie
        if ($request->has('category') && $request->category) {
            $query->byCategory($request->category);
        }

        // Filtrage par statut temporel
        if ($request->has('status')) {
            switch ($request->status) {
                case 'upcoming':
                    $query->upcoming();
                    break;
                case 'ongoing':
                    $query->where('start_date', '<=', now())
                        ->where('end_date', '>=', now());
                    break;
                case 'past':
                    $query->where('end_date', '<', now());
                    break;
            }
        }

        $featuredEvents = Event::featured()->online()->published()->take(3)->get();

        // Créer une nouvelle requête pour les événements normaux (non mis en avant)
        $normalEventsQuery = Event::query()
            ->with(['category', 'user', 'pays'])
            ->online()
            ->published()
            ->where('is_featured', false)
            ->orderBy('start_date', 'asc');

        // Appliquer les mêmes filtres que la requête principale
        if ($request->has('category') && $request->category) {
            $normalEventsQuery->byCategory($request->category);
        }

        if ($request->has('status')) {
            switch ($request->status) {
                case 'upcoming':
                    $normalEventsQuery->upcoming();
                    break;
                case 'ongoing':
                    $normalEventsQuery->where('start_date', '<=', now())
                        ->where('end_date', '>=', now());
                    break;
                case 'past':
                    $normalEventsQuery->where('end_date', '<', now());
                    break;
            }
        }

        $events = $normalEventsQuery->paginate(6);
        $categories = Category::all();

        // Compter les événements par statut pour la sidebar
        $upcomingCount = Event::online()->published()->upcoming()->count();
        $ongoingCount = Event::online()->published()->ongoing()->count();
        $pastCount = Event::online()->published()->past()->count();

        return view('events.index', compact('events', 'categories', 'featuredEvents', 'upcomingCount', 'ongoingCount', 'pastCount'));
    }

    /**
     * Affiche un événement spécifique
     */
    public function show(string $slug, int $id): View
    {
        $event = Event::with(['category', 'user', 'pays'])
            ->where('id', $id)
            ->where('slug', $slug)
            ->online()
            ->published()
            ->firstOrFail();

        // Incrémenter le nombre de vues
        $event->increment('view');

        // Événements similaires
        $similarEvents = Event::with(['category', 'user'])
            ->where('id', '!=', $event->id)
            ->where('category_id', $event->category_id)
            ->online()
            ->published()
            ->take(3)
            ->get();

        return view('events.show', compact('event', 'similarEvents'));
    }

    /**
     * Affiche les événements par catégorie
     */
    public function category(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $events = Event::with(['category', 'user', 'pays'])
            ->where('category_id', $category->id)
            ->online()
            ->published()
            ->orderBy('start_date', 'asc')
            ->paginate(6);

        return view('events.category', compact('events', 'category'));
    }

    /**
     * Affiche le calendrier des événements
     */
    public function calendar(): View
    {
        $events = Event::with(['category', 'user', 'pays'])
            ->online()
            ->published()
            ->orderBy('start_date', 'asc')
            ->get();

        return view('events.calendar', compact('events'));
    }

    /**
     * Recherche d'événements
     */
    public function search(Request $request): View
    {
        $query = $request->get('q');

        $events = Event::with(['category', 'user', 'pays'])
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('location', 'like', "%{$query}%");
            })
            ->online()
            ->published()
            ->orderBy('start_date', 'asc')
            ->paginate(6);

        return view('events.search', compact('events', 'query'));
    }
}
