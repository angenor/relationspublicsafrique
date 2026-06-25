<div class="annuaire-grid"
     wire:loading.class="opacity-50"
     wire:loading.attr="aria-busy"
     role="region"
     aria-label="Annuaire des profils">
    <div class="annuaire-toolbar mb-3" role="search">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-lg-4">
                <label for="annuaire-search" class="form-label small mb-1">Recherche</label>
                <input id="annuaire-search"
                       type="search"
                       wire:model.live.debounce.400ms="q"
                       class="form-control"
                       placeholder="Nom, organisation, pays, domaine..."
                       aria-label="Rechercher dans l'annuaire">
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label for="annuaire-pays" class="form-label small mb-1">Pays</label>
                <select id="annuaire-pays" wire:model.live="pays" class="form-select">
                    <option value="">Tous les pays</option>
                    @foreach ($paysOptions as $p)
                        <option value="{{ $p->slug }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label for="annuaire-type" class="form-label small mb-1">Catégorie</label>
                <select id="annuaire-type" wire:model.live="type" class="form-select">
                    <option value="">Toutes</option>
                    @foreach ($typeOptions as $value => $libelle)
                        <option value="{{ $value }}">{{ $libelle }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label for="annuaire-domaine" class="form-label small mb-1">Domaine</label>
                <select id="annuaire-domaine"
                        wire:model.live="domaines"
                        class="form-select"
                        multiple
                        size="1"
                        aria-label="Domaines d'expertise (sélection multiple)"
                        aria-multiselectable="true">
                    @foreach ($domaineOptions as $d)
                        <option value="{{ $d->slug }}">{{ $d->libelle }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label for="annuaire-tag" class="form-label small mb-1">Tags</label>
                <select id="annuaire-tag"
                        wire:model.live="tags"
                        class="form-select"
                        multiple
                        size="1"
                        aria-label="Tags (sélection multiple)"
                        aria-multiselectable="true">
                    @foreach ($tagOptions as $t)
                        <option value="{{ $t->slug }}">{{ $t->libelle }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <label for="annuaire-tri" class="form-label small mb-1">Trier par</label>
                <select id="annuaire-tri" wire:model.live="tri" class="form-select">
                    <option value="">Par défaut</option>
                    @foreach ($triOptions as $value => $libelle)
                        <option value="{{ $value }}">{{ $libelle }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2">
            <button type="button"
                    class="btn btn-sm btn-link"
                    wire:click="resetFilters">
                Réinitialiser les filtres
            </button>

            <div class="btn-group" role="group" aria-label="Mode d'affichage">
                <button type="button"
                        class="btn btn-sm btn-outline-secondary {{ $mode === 'grille' ? 'active' : '' }}"
                        wire:click="setMode('grille')"
                        aria-pressed="{{ $mode === 'grille' ? 'true' : 'false' }}">
                    Grille
                </button>
                <button type="button"
                        class="btn btn-sm btn-outline-secondary {{ $mode === 'liste' ? 'active' : '' }}"
                        wire:click="setMode('liste')"
                        aria-pressed="{{ $mode === 'liste' ? 'true' : 'false' }}">
                    Liste
                </button>
            </div>
        </div>
    </div>

    <div aria-live="polite" aria-atomic="false">
        @if ($profils->total() === 0)
            <div class="alert alert-info" role="status">
                Aucun profil ne correspond à votre recherche.
            </div>
        @else
            <p class="visually-hidden" id="annuaire-resultats-count">
                {{ $profils->total() }} profil(s) trouvé(s) — page {{ $profils->currentPage() }} sur {{ $profils->lastPage() }}.
            </p>

            @if ($mode === 'grille')
                <div class="row" role="list" aria-describedby="annuaire-resultats-count">
                    @foreach ($profils as $profil)
                        <div class="col-6 col-sm-6 col-lg-4 col-xxl-3" role="listitem">
                            @include('livewire.annuaire.profil-carte', ['profil' => $profil])
                        </div>
                    @endforeach
                </div>
            @else
                <ul class="list-group" aria-describedby="annuaire-resultats-count">
                    @foreach ($profils as $profil)
                        @include('livewire.annuaire.profil-ligne', ['profil' => $profil])
                    @endforeach
                </ul>
            @endif

            <nav class="mt-4" aria-label="Pagination de l'annuaire">
                {{ $profils->withQueryString()->links() }}
            </nav>
        @endif
    </div>
</div>
