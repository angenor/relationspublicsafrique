<article class="card h-100 profil-card" aria-label="Profil de {{ $profil->prenom }} {{ $profil->nom }}">
    <a href="{{ route('profil.show', [$profil->slug, $profil->id]) }}" class="text-decoration-none text-reset">
        <img src="{{ $profil->photo_url }}"
             alt="Photo de {{ $profil->prenom }} {{ $profil->nom }}"
             class="card-img-top profil-card__photo"
             loading="lazy">
        <div class="card-body">
            <h3 class="h6 mb-1">{{ trim(($profil->prenom ?? '').' '.($profil->nom ?? '')) }}</h3>
            @if ($profil->fonction)
                <p class="text-muted small mb-1">{{ $profil->fonction }}</p>
            @endif
            @if ($profil->organisation)
                <p class="small mb-1">{{ $profil->organisation }}</p>
            @endif
            <p class="small text-muted mb-0">
                @if ($profil->ville)<span>{{ $profil->ville }}</span>@endif
                @if ($profil->ville && $profil->pays) · @endif
                @if ($profil->pays)<span>{{ $profil->pays->name }}</span>@endif
            </p>
        </div>
    </a>
</article>
