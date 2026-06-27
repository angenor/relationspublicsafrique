{{-- Médias post-événement (research R6) : replay (embed/natif), photos (lightbox), documents.
     Tolérant aux médias indisponibles. Section masquée si vide (géré côté show). --}}
<section class="event-detail__section" id="medias" aria-labelledby="sec-medias">
    <h2 id="sec-medias">Replay, photos &amp; documents</h2>
    <div class="event-galerie">
        @foreach ($event->medias as $media)
            @if ($media->type === 'replay')
                @php $embed = $media->embed; @endphp
                @if ($embed !== '')
                    <figure class="event-galerie__item event-galerie__item--video">
                        {!! $embed !!}
                        @if ($media->legende)<figcaption>{{ $media->legende }}</figcaption>@endif
                    </figure>
                @elseif ($media->chemin_url)
                    <figure class="event-galerie__item event-galerie__item--video">
                        <video controls preload="metadata" src="{{ $media->chemin_url }}"></video>
                        @if ($media->legende)<figcaption>{{ $media->legende }}</figcaption>@endif
                    </figure>
                @endif
            @elseif ($media->type === 'image' && $media->chemin_url)
                <figure class="event-galerie__item event-galerie__item--image">
                    <a href="{{ $media->chemin_url }}"
                       class="event-galerie__link"
                       data-event-lightbox
                       data-legende="{{ $media->legende }}">
                        <img src="{{ $media->chemin_url }}" alt="{{ $media->legende ?: $event->title }}" loading="lazy">
                    </a>
                    @if ($media->legende)<figcaption>{{ $media->legende }}</figcaption>@endif
                </figure>
            @elseif ($media->type === 'document' && $media->chemin_url)
                <a class="event-doc" href="{{ $media->chemin_url }}" target="_blank" rel="noopener">
                    <i class="fal fa-file-download"></i> {{ $media->legende ?: 'Télécharger le document' }}
                </a>
            @endif
        @endforeach
    </div>
</section>
