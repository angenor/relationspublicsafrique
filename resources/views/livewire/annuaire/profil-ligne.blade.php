<li class="list-group-item">
    <a href="{{ route('profil.show', [$profil->slug, $profil->id]) }}" class="d-flex align-items-center gap-3 text-decoration-none text-reset">
        <img src="{{ $profil->photo_url }}"
             alt=""
             class="profil-ligne__photo rounded-circle"
             width="56" height="56" loading="lazy">
        <div class="flex-grow-1">
            <div class="fw-semibold">{{ trim(($profil->prenom ?? '').' '.($profil->nom ?? '')) }}</div>
            @if ($profil->fonction || $profil->organisation)
                <div class="small text-muted">
                    {{ $profil->fonction }}@if ($profil->fonction && $profil->organisation) · @endif{{ $profil->organisation }}
                </div>
            @endif
        </div>
        <div class="small text-muted text-end">
            @if ($profil->ville){{ $profil->ville }}@endif
            @if ($profil->pays)<br><span>{{ $profil->pays->name }}</span>@endif
        </div>
    </a>
</li>
