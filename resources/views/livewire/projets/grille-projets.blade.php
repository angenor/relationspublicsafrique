<div class="projets-grille"
     wire:loading.class="is-loading"
     wire:loading.attr="aria-busy"
     role="region"
     aria-label="Explorateur de projets">

    <div class="projets-grille__toolbar" role="search">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-lg-4">
                <label for="projets-q" class="form-label small mb-1">Recherche</label>
                <input id="projets-q" type="search" class="form-control"
                       wire:model.live.debounce.400ms="q"
                       placeholder="Titre, thématique, pays…"
                       aria-label="Rechercher un projet">
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <label for="projets-thematique" class="form-label small mb-1">Thématique</label>
                <select id="projets-thematique" class="form-select" wire:model.live="thematique">
                    <option value="">Toutes</option>
                    @foreach ($thematiqueOptions as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <label for="projets-zone" class="form-label small mb-1">Zone</label>
                <select id="projets-zone" class="form-select" wire:model.live="zone">
                    <option value="">Toutes</option>
                    @foreach ($zoneOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <label for="projets-statut" class="form-label small mb-1">Statut</label>
                <select id="projets-statut" class="form-select" wire:model.live="statut">
                    <option value="">Tous</option>
                    @foreach ($statutOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-2">
            <div class="d-flex align-items-center gap-2">
                <label for="projets-tri" class="form-label small mb-0">Trier :</label>
                <select id="projets-tri" class="form-select form-select-sm w-auto" wire:model.live="tri">
                    <option value="">Par défaut</option>
                    @foreach ($triOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-sm btn-link" wire:click="resetFilters">Réinitialiser</button>
            </div>
        </div>
    </div>

    <div aria-live="polite" aria-atomic="false" class="mt-3">
        @if ($projets->total() === 0)
            <div class="alert alert-info projets-grille__empty" role="status">
                Aucun projet ne correspond à votre recherche.
                <button type="button" class="btn btn-sm btn-link" wire:click="resetFilters">Réinitialiser les filtres</button>
            </div>
        @else
            <p class="visually-hidden" id="projets-resultats-count">
                {{ $projets->total() }} projet(s) trouvé(s).
            </p>

            <div class="row g-4" role="list" aria-describedby="projets-resultats-count">
                @foreach ($projets as $projet)
                    <div class="col-12 col-sm-6 col-lg-4" role="listitem" wire:key="projet-{{ $projet->id }}">
                        @include('components.projet-card', ['projet' => $projet])
                    </div>
                @endforeach
            </div>

            @if ($projets->hasMorePages())
                <div class="text-center my-4">
                    <button type="button" class="btn btn-theme" wire:click="chargerPlus"
                            wire:loading.attr="disabled" data-projets-loadmore>
                        <span wire:loading.remove wire:target="chargerPlus">Charger plus</span>
                        <span wire:loading wire:target="chargerPlus">Chargement…</span>
                    </button>
                </div>
            @endif

            <nav class="mt-3" aria-label="Pagination des projets">
                {{ $projets->onEachSide(1)->links() }}
            </nav>
        @endif
    </div>
</div>
