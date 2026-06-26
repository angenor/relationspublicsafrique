{{-- Liste des commentaires approuvés + formulaire de soumission (modération en back-office). --}}
@php
    $approuves = $media->relationLoaded('commentairesApprouves')
        ? $media->commentairesApprouves
        : $media->commentairesApprouves()->latest()->get();
@endphp

@if (session('media_comment_status'))
    <div class="alert alert-success" role="status">{{ session('media_comment_status') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if ($approuves->isEmpty())
    <p class="text-muted">Soyez le premier à commenter ce contenu.</p>
@else
    <ul class="media-comments__list">
        @foreach ($approuves as $commentaire)
            <li class="media-comment">
                <div class="media-comment__head">
                    <span class="media-comment__author">{{ $commentaire->author_name }}</span>
                    <span class="media-comment__date">{{ $commentaire->created_at->translatedFormat('d F Y') }}</span>
                </div>
                <p class="media-comment__body">{{ $commentaire->body }}</p>
            </li>
        @endforeach
    </ul>
@endif

<form action="{{ route('media.comments.store', ['id' => $media->id]) }}" method="POST" class="media-comment-form">
    @csrf
    <h3 class="h6">Laisser un commentaire</h3>
    <p class="small text-muted">Votre commentaire sera publié après modération. Votre adresse email ne sera pas affichée.</p>

    {{-- Honeypot anti-spam : doit rester vide. --}}
    <div class="media-hp" aria-hidden="true">
        <label for="media-website">Ne pas remplir</label>
        <input type="text" name="website" id="media-website" tabindex="-1" autocomplete="off">
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <label for="comment-name" class="form-label">Nom</label>
            <input type="text" name="author_name" id="comment-name" class="form-control" value="{{ old('author_name') }}" required maxlength="120">
        </div>
        <div class="col-md-6">
            <label for="comment-email" class="form-label">Email</label>
            <input type="email" name="author_email" id="comment-email" class="form-control" value="{{ old('author_email') }}" required>
        </div>
        <div class="col-12">
            <label for="comment-body" class="form-label">Commentaire</label>
            <textarea name="body" id="comment-body" class="form-control" rows="4" required maxlength="3000">{{ old('body') }}</textarea>
        </div>
    </div>

    <button type="submit" class="btn btn-theme mt-3">Envoyer</button>
</form>
