<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Media;
use App\Models\MediaSerie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaController extends Controller
{
    /** Liste les contenus eager-loadés pour les rubriques d'accueil. */
    private const WITH_LISTE = ['categories', 'auteurPrincipal', 'serie'];

    /**
     * Page d'accueil de la section : À la une, Dernières, Tendances, sélection éditoriale.
     */
    public function home(): View
    {
        $aLaUne = Media::with(self::WITH_LISTE)
            ->published()->featured()->recommended()
            ->take((int) config('media.a_la_une', 3))
            ->get();

        $dernieres = Media::with(self::WITH_LISTE)
            ->published()->recent()
            ->take(8)
            ->get();

        $tendances = Media::with(self::WITH_LISTE)
            ->published()->popular()
            ->take(5)
            ->get();

        $selection = Media::with(self::WITH_LISTE)
            ->published()->pinned()
            ->take(5)
            ->get();

        $categoriesMedia = Category::query()
            ->where('online', 1)->where('type', 'media')
            ->orderBy('position')
            ->get();

        return view('media.home', compact('aLaUne', 'dernieres', 'tendances', 'selection', 'categoriesMedia'));
    }

    /**
     * Explorateur réactif (recherche / filtres / tri / charger plus) hébergeant le composant Livewire.
     */
    public function index(): View
    {
        return view('media.index');
    }

    /**
     * Page d'une série / playlist : épisodes publiés ordonnés (saison/épisode).
     */
    public function serie(string $slug): View
    {
        $serie = MediaSerie::query()->where('slug', $slug)->online()->firstOrFail();

        $episodes = $serie->medias()
            ->with(['auteurPrincipal'])
            ->published()
            ->get();

        return view('media.serie', compact('serie', 'episodes'));
    }

    /**
     * Soumission d'un commentaire (masqué jusqu'à modération). Honeypot + throttle (route).
     */
    public function storeComment(Request $request, int $id): RedirectResponse
    {
        $media = Media::query()->published()->findOrFail($id);

        // Honeypot : un bot remplit le champ caché → on ignore silencieusement.
        if (filled($request->input('website'))) {
            return back()->with('media_comment_status', 'Merci, votre commentaire a été pris en compte.')
                ->withFragment('commentaires');
        }

        $data = $request->validate([
            'author_name' => ['required', 'string', 'max:120'],
            'author_email' => ['required', 'email', 'max:255'],
            'body' => ['required', 'string', 'max:3000'],
        ]);

        $media->commentaires()->create([
            'author_name' => $data['author_name'],
            'author_email' => $data['author_email'],
            'body' => $data['body'],
            'status' => 'pending',
            'ip_address' => $request->ip(),
        ]);

        return back()
            ->with('media_comment_status', 'Merci ! Votre commentaire sera publié après modération.')
            ->withFragment('commentaires');
    }

    /**
     * Page de détail (validation stricte slug+id, incrément des vues, similaires, préc/suiv, partage).
     */
    public function show(string $slug, int $id): View
    {
        $media = Media::with(['categories', 'tags', 'auteurs', 'auteurPrincipal', 'pays', 'serie', 'commentairesApprouves'])
            ->where('id', $id)
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $media->increment('view');

        $categorieIds = $media->categories->pluck('id')->all();

        $similaires = Media::with(['auteurPrincipal'])
            ->where('id', '!=', $media->id)
            ->where(function ($q) use ($media, $categorieIds): void {
                $q->where('type', $media->type);
                if ($categorieIds !== []) {
                    $q->orWhereHas('categories', fn ($c) => $c->whereIn('categories.id', $categorieIds));
                }
            })
            ->published()->recent()
            ->take(4)
            ->get();

        // Navigation : prioritairement par série (saison/épisode), sinon chronologique.
        $serieEpisodes = collect();
        $precedent = null;
        $suivant = null;

        if ($media->serie_id && $media->serie) {
            $serieEpisodes = $media->serie->medias()->published()->get();
            $position = $serieEpisodes->search(fn (Media $e) => $e->id === $media->id);
            if ($position !== false) {
                $precedent = $position > 0 ? $serieEpisodes[$position - 1] : null;
                $suivant = $position < $serieEpisodes->count() - 1 ? $serieEpisodes[$position + 1] : null;
            }
        }

        if ($precedent === null && $suivant === null) {
            $precedent = Media::published()
                ->where('published_at', '<', $media->published_at)
                ->orderByDesc('published_at')
                ->first();

            $suivant = Media::published()
                ->where('published_at', '>', $media->published_at)
                ->orderBy('published_at')
                ->first();
        }

        $btnShare = \Share::page($media->link, $media->titre)
            ->facebook()
            ->twitter()
            ->linkedin()
            ->whatsapp()
            ->getRawLinks();

        return view('media.show', compact('media', 'similaires', 'precedent', 'suivant', 'serieEpisodes', 'btnShare'));
    }
}
