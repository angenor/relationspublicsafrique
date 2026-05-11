@php
    $nomComplet = trim(($profil->prenom ?? '').' '.($profil->nom ?? ''));
    $categorieLabel = $profil->type_profil ? mb_strtoupper($profil->type_profil) : null;
    $url = route('profil.show', [$profil->slug, $profil->id]);
@endphp

<div class="team-style2 has-border" aria-label="Profil de {{ $nomComplet }}">
    <div class="team-content">
        <h5 class="team-name h5 text-wrap truncate" style="font-size: medium">
            <a href="{{ $url }}" wire:navigate>
                {{ $nomComplet }}
            </a>
        </h5>

        @if ($categorieLabel)
            <p class="team-degi">{{ $categorieLabel }}</p>
        @endif

        <div class="team-img">
            <a href="{{ $url }}" wire:navigate>
                <img src="{{ $profil->photo_url }}"
                     alt="{{ $nomComplet }}"
                     loading="lazy">
            </a>
        </div>

        @if ($profil->pays || $profil->ville)
            <p class="team-experi">
                <i class="fas fa-map-marker-alt"></i>
                @if ($profil->ville){{ $profil->ville }}@endif
                @if ($profil->ville && $profil->pays), @endif
                @if ($profil->pays){{ $profil->pays->name }}@endif
            </p>
        @endif
    </div>
</div>
