<div class="media-grille"
     wire:loading.class="is-loading"
     wire:loading.attr="aria-busy"
     role="region"
     aria-label="Explorateur de contenus média">

    <div class="media-grille__toolbar" role="search">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-lg-4">
                <label for="media-q" class="form-label small mb-1">Recherche</label>
                <input id="media-q" type="search" class="form-control"
                       wire:model.live.debounce.400ms="q"
                       placeholder="Titre, mot-clé, auteur, thématique…"
                       aria-label="Rechercher un contenu média">
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label for="media-type" class="form-label small mb-1">Format</label>
                <select id="media-type" class="form-select" wire:model.live="type">
                    <option value="">Tous</option>
                    @foreach ($typeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label for="media-cat" class="form-label small mb-1">Thématique</label>
                <select id="media-cat" class="form-select" wire:model.live="categorie">
                    <option value="">Toutes</option>
                    @foreach ($categorieOptions as $cat)
                        <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label for="media-pays" class="form-label small mb-1">Pays</label>
                <select id="media-pays" class="form-select" wire:model.live="pays">
                    <option value="">Tous</option>
                    @foreach ($paysOptions as $p)
                        <option value="{{ $p->slug }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label for="media-auteur" class="form-label small mb-1">Auteur</label>
                <select id="media-auteur" class="form-select" wire:model.live="auteur">
                    <option value="">Tous</option>
                    @foreach ($auteurOptions as $a)
                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-2">
            <div class="d-flex align-items-center gap-2">
                <label for="media-tri" class="form-label small mb-0">Trier :</label>
                <select id="media-tri" class="form-select form-select-sm w-auto" wire:model.live="tri">
                    <option value="">Par défaut (récents)</option>
                    @foreach ($triOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-sm btn-link" wire:click="resetFilters">Réinitialiser</button>
            </div>

            <div class="btn-group" role="group" aria-label="Mode d'affichage">
                <button type="button" class="btn btn-sm btn-outline-secondary {{ $mode === 'grille' ? 'active' : '' }}"
                        wire:click="setMode('grille')" aria-pressed="{{ $mode === 'grille' ? 'true' : 'false' }}">
                    <i class="fas fa-th"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary {{ $mode === 'liste' ? 'active' : '' }}"
                        wire:click="setMode('liste')" aria-pressed="{{ $mode === 'liste' ? 'true' : 'false' }}">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>
    </div>

    <div aria-live="polite" aria-atomic="false" class="mt-3">
        @if ($medias->total() === 0)
            <div class="alert alert-info" role="status">
                Aucun contenu ne correspond à votre recherche.
                <button type="button" class="btn btn-sm btn-link" wire:click="resetFilters">Réinitialiser les filtres</button>
            </div>
        @else
            <p class="visually-hidden" id="media-resultats-count">
                {{ $medias->total() }} contenu(s) trouvé(s).
            </p>

            @if ($mode === 'grille')
                <div class="row g-4" role="list" aria-describedby="media-resultats-count">
                    @foreach ($medias as $media)
                        <div class="col-12 col-sm-6 col-lg-4 col-xxl-3" role="listitem" wire:key="media-{{ $media->id }}">
                            @include('components.media-card', ['media' => $media])
                        </div>
                    @endforeach
                </div>
            @else
                <ul class="media-list" aria-describedby="media-resultats-count">
                    @foreach ($medias as $media)
                        <div wire:key="media-{{ $media->id }}">
                            @include('components.media-ligne', ['media' => $media])
                        </div>
                    @endforeach
                </ul>
            @endif

            @if ($medias->hasMorePages())
                <div class="text-center my-4">
                    <button type="button" class="btn btn-theme" wire:click="loadMore"
                            wire:loading.attr="disabled" data-media-loadmore>
                        <span wire:loading.remove wire:target="loadMore">Charger plus</span>
                        <span wire:loading wire:target="loadMore">Chargement…</span>
                    </button>
                </div>
            @endif

            <nav class="mt-3" aria-label="Pagination des contenus média">
                {{ $medias->onEachSide(1)->links() }}
            </nav>
        @endif
    </div>
</div>
