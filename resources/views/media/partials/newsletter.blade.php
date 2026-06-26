<section class="media-newsletter" aria-labelledby="newsletter-title">
    <h2 id="newsletter-title" class="h5">Recevez les nouveautés de la newsroom</h2>
    <p class="small mb-3 opacity-75">Articles, interviews, podcasts et vidéos, directement dans votre boîte mail.</p>

    @if (session('media_newsletter_status'))
        <div class="alert alert-light text-dark" role="status">{{ session('media_newsletter_status') }}</div>
    @endif

    <form action="{{ route('media.newsletter.subscribe') }}" method="POST" class="d-flex gap-2 flex-wrap align-items-start">
        @csrf
        {{-- Honeypot anti-spam : doit rester vide. --}}
        <div class="media-hp" aria-hidden="true">
            <label for="newsletter-website">Ne pas remplir</label>
            <input type="text" name="website" id="newsletter-website" tabindex="-1" autocomplete="off">
        </div>

        <div>
            <input type="email" name="email" class="form-control" placeholder="votre@email.com"
                   value="{{ old('email') }}" required aria-label="Votre adresse email" style="max-width: 320px;">
            @error('email')
                <div class="small mt-1 text-warning">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-light fw-semibold">S'abonner</button>
    </form>
</section>
