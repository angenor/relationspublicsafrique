{{-- Liste des intervenants (research R5) — section masquée si aucune ligne (géré côté show). --}}
<section class="event-detail__section" aria-labelledby="sec-speakers">
    <h2 id="sec-speakers">Intervenants</h2>
    <div class="event-speakers">
        @foreach ($event->speakers as $speaker)
            <div class="event-speaker">
                @if ($speaker->photo_url)
                    <img class="event-speaker__photo" src="{{ $speaker->photo_url }}" alt="{{ $speaker->nom }}" loading="lazy">
                @endif
                <div>
                    <p class="event-speaker__name">{{ $speaker->nom }}</p>
                    @if ($speaker->role || $speaker->organisation)
                        <span class="event-speaker__role">{{ collect([$speaker->role, $speaker->organisation])->filter()->implode(' · ') }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>
