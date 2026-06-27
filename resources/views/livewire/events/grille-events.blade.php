<div class="events-grille"
     wire:loading.class="is-loading"
     wire:loading.attr="aria-busy"
     role="region"
     aria-label="Explorateur d'événements">

    <div class="events-grille__toolbar" role="search">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-lg-4">
                <label for="events-q" class="form-label small mb-1">Recherche</label>
                <input id="events-q" type="search" class="form-control"
                       wire:model.live.debounce.400ms="q"
                       placeholder="Titre, lieu, mot-clé…"
                       aria-label="Rechercher un événement">
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <label for="events-statut" class="form-label small mb-1">Statut</label>
                <select id="events-statut" class="form-select" wire:model.live="statut">
                    <option value="">Tous</option>
                    @foreach ($statutOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <label for="events-type" class="form-label small mb-1">Type</label>
                <select id="events-type" class="form-select" wire:model.live="type">
                    <option value="">Tous</option>
                    @foreach ($typeOptions as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <label for="events-pays" class="form-label small mb-1">Pays</label>
                <select id="events-pays" class="form-select" wire:model.live="pays">
                    <option value="">Tous</option>
                    @foreach ($paysOptions as $id => $nom)
                        <option value="{{ $id }}">{{ $nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <label for="events-tri" class="form-label small mb-1">Trier</label>
                <select id="events-tri" class="form-select" wire:model.live="tri">
                    <option value="">Par défaut</option>
                    @foreach ($triOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-end align-items-center mt-2">
            <button type="button" class="btn btn-sm btn-link" wire:click="resetFilters">Réinitialiser les filtres</button>
        </div>
    </div>

    <div aria-live="polite" aria-atomic="false" class="mt-3">
        @if ($events->total() === 0)
            <div class="alert alert-info events-grille__empty" role="status">
                @if ($statut !== '' || $type !== '' || $pays !== '' || $q !== '')
                    Aucun événement ne correspond à votre recherche.
                    <button type="button" class="btn btn-sm btn-link" wire:click="resetFilters">Réinitialiser les filtres</button>
                @else
                    Aucun événement n'est publié pour le moment. Revenez bientôt&nbsp;!
                @endif
            </div>
        @else
            <p class="visually-hidden" id="events-resultats-count">{{ $events->total() }} événement(s) trouvé(s).</p>

            @if ($statut !== '')
                {{-- Liste filtrée sur un seul statut temporel --}}
                <div class="row g-4" role="list" aria-describedby="events-resultats-count">
                    @foreach ($events as $event)
                        <div class="col-12 col-sm-6 col-lg-4" role="listitem" wire:key="event-{{ $event->id }}">
                            @include('components.event-card', ['event' => $event])
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Listing groupé par statut temporel : En cours / À venir / Clos --}}
                @php $grouped = $events->getCollection()->groupBy('temporal_status'); @endphp
                @foreach (['ongoing' => 'En cours', 'upcoming' => 'À venir', 'past' => 'Clos'] as $key => $label)
                    @php $items = $grouped->get($key); @endphp
                    @if ($items && $items->isNotEmpty())
                        <section class="events-group">
                            <h2 class="events-group__title">
                                {{ $label }}
                                <span class="events-group__count">{{ $counts[$key] ?? $items->count() }}</span>
                            </h2>
                            <div class="row g-4" role="list">
                                @foreach ($items as $event)
                                    <div class="col-12 col-sm-6 col-lg-4" role="listitem" wire:key="event-{{ $event->id }}">
                                        @include('components.event-card', ['event' => $event])
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif
                @endforeach
            @endif

            @if ($events->hasMorePages())
                <div class="text-center my-4">
                    <button type="button" class="btn btn-theme" wire:click="chargerPlus"
                            wire:loading.attr="disabled" data-events-loadmore>
                        <span wire:loading.remove wire:target="chargerPlus">Charger plus</span>
                        <span wire:loading wire:target="chargerPlus">Chargement…</span>
                    </button>
                </div>
            @endif

            <nav class="mt-3" aria-label="Pagination des événements">
                {{ $events->onEachSide(1)->links() }}
            </nav>
        @endif
    </div>
</div>
