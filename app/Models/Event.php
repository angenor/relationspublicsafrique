<?php

namespace App\Models;

use App\Helper\Sluggable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Event
 *
 * Évolution additive (feature 004-evenements). Deux dettes résolues :
 *  - `online` est désormais **le format** de l'événement (présentiel/en ligne) et
 *    n'est PLUS un gate de visibilité (research R1). La visibilité publique
 *    repose uniquement sur `status='published'` (scope `published()`).
 *  - `pays_id` est ajouté par migration corrective (research R2).
 *
 * @property int $id
 * @property string $title
 * @property string|null $slug
 * @property text $description
 * @property text|null $resume
 * @property text|null $objectifs
 * @property text|null $programme
 * @property text|null $public_cible
 * @property text|null $compte_rendu
 * @property string|null $image
 * @property string|null $location
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon $end_date
 * @property \Carbon\Carbon|null $registration_deadline
 * @property int|null $max_participants
 * @property int $current_participants
 * @property decimal $price
 * @property string $status
 * @property bool $is_featured
 * @property bool $online
 * @property string $registration_mode
 * @property string|null $registration_url
 * @property int|null $user_id
 * @property int|null $category_id
 * @property int|null $pays_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Event extends Model
{
    use HasFactory, Sluggable;

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_deadline' => 'datetime',
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'online' => 'boolean',
        'view' => 'integer',
        'pays_id' => 'integer',
    ];

    /** Statut éditorial (saisi) — gate de visibilité via `published()` (R1). */
    public static array $statuses = [
        'draft' => 'Brouillon',
        'published' => 'Publié',
        'cancelled' => 'Annulé',
        'completed' => 'Terminé',
    ];

    /** Mode d'inscription (research R9). */
    public static array $registrationModes = [
        'internal' => 'Inscription sur le site',
        'external' => 'Lien externe',
    ];

    /** Statut temporel dérivé des dates (research R3) — affichage badge. */
    public static array $temporalStatuses = [
        'upcoming' => 'À venir',
        'ongoing' => 'En cours',
        'past' => 'Clos',
    ];

    // ----- Relations -----

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function pays(): BelongsTo
    {
        return $this->belongsTo(Pays::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function registeredUsers(): HasMany
    {
        return $this->hasMany(EventRegistration::class)->where('status', '!=', 'cancelled');
    }

    /** Intervenants (research R5) — ordonnés par position. */
    public function speakers(): HasMany
    {
        return $this->hasMany(EventSpeaker::class)->orderBy('position');
    }

    /** Médias post-événement (research R6) — ordonnés par position. */
    public function medias(): HasMany
    {
        return $this->hasMany(EventMedia::class)->orderBy('position');
    }

    public function isUserRegistered(int $userId): bool
    {
        return $this->registrations()
            ->where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->exists();
    }

    // ----- Accessors -----

    public function getImgAttribute(): string
    {
        return $this->image ? asset('storage/'.$this->image) : asset('images/default-event.jpg');
    }

    public function getResumeTextAttribute(): string
    {
        return $this->resume ?: \Str::limit(strip_tags($this->description), 200);
    }

    public function getLinkAttribute(): string
    {
        return route('events.show', ['id' => $this->id, 'slug' => $this->slug]);
    }

    public function getPublishedAttribute(): string
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    public function getByAttribute(): string
    {
        return $this->user?->name ?? 'Anonyme';
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_date->isFuture();
    }

    public function getIsOngoingAttribute(): bool
    {
        $now = Carbon::now();

        return $this->start_date->lte($now) && $this->end_date->gte($now);
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->end_date->isPast();
    }

    /** Statut temporel dérivé (research R3) : upcoming | ongoing | past. */
    public function getTemporalStatusAttribute(): string
    {
        if ($this->is_ongoing) {
            return 'ongoing';
        }
        if ($this->is_upcoming) {
            return 'upcoming';
        }

        return 'past';
    }

    public function getTemporalStatusLabelAttribute(): string
    {
        return self::$temporalStatuses[$this->temporal_status] ?? '';
    }

    /** Lieu affiché : « En ligne » si format en ligne, sinon la ville (FR-009). */
    public function getFormatLabelAttribute(): string
    {
        return $this->online ? 'En ligne' : (string) ($this->location ?? '');
    }

    /** Au moins un média post-événement de type replay. */
    public function getHasReplayAttribute(): bool
    {
        if ($this->relationLoaded('medias')) {
            return $this->medias->contains(fn ($m) => $m->type === 'replay');
        }

        return $this->medias()->where('type', 'replay')->exists();
    }

    /** Médias/compte rendu disponibles sur un événement clos (alimente le CTA replay). */
    public function getHasPostEventMediaAttribute(): bool
    {
        if (! $this->is_completed) {
            return false;
        }
        $hasMedia = $this->relationLoaded('medias')
            ? $this->medias->isNotEmpty()
            : $this->medias()->exists();

        return $hasMedia || filled($this->compte_rendu);
    }

    /**
     * Inscription interne possible (research R1/R9) — NE dépend PLUS de `online`.
     * Vrai ssi : publié, non clos, mode interne, deadline non dépassée, capacité OK.
     */
    public function getCanRegisterAttribute(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        if ($this->is_completed) {
            return false;
        }

        if ($this->registration_mode !== 'internal') {
            return false;
        }

        if ($this->registration_deadline && $this->registration_deadline->isPast()) {
            return false;
        }

        if ($this->max_participants && $this->current_participants >= $this->max_participants) {
            return false;
        }

        return true;
    }

    /**
     * Nature du CTA contextuel (cf. contrat public-routes, matrice CTA) :
     * register_internal | register_external | view_replay | view_details.
     */
    public function getCtaAttribute(): string
    {
        if ($this->status === 'cancelled') {
            return 'view_details';
        }

        if ($this->temporal_status === 'past') {
            return $this->has_post_event_media ? 'view_replay' : 'view_details';
        }

        // À venir / en cours.
        if ($this->status === 'published'
            && $this->registration_mode === 'external'
            && filled($this->registration_url)) {
            return 'register_external';
        }

        if ($this->can_register) {
            return 'register_internal';
        }

        return 'view_details';
    }

    // ----- Actions éditoriales -----

    /** Publier (research R1) — bascule le statut éditorial, source de visibilité. */
    public function publish(): void
    {
        $this->status = 'published';
        $this->save();
    }

    /** Dépublier (research R1) — repasse en brouillon. */
    public function unpublish(): void
    {
        $this->status = 'draft';
        $this->save();
    }

    /**
     * @deprecated R1 — `online` n'est plus un gate de visibilité mais le format.
     * Conservé pour compatibilité ; la visibilité passe par publish()/unpublish().
     */
    public function activate(): void
    {
        $this->online = true;
        $this->save();
    }

    /** @deprecated R1 — voir activate(). */
    public function deactivate(): void
    {
        $this->online = false;
        $this->save();
    }

    // ----- Scopes -----

    /**
     * @deprecated R1 — `online` = format ; n'utilisez PAS ce scope comme gate de
     * visibilité publique. La visibilité repose uniquement sur `published()`.
     */
    public function scopeOnline(Builder $query): Builder
    {
        return $query->where('online', true);
    }

    /**
     * Source unique de visibilité publique (research R1).
     * Inclut `completed` (état archivé issu du cron optionnel `events:mark-completed`,
     * R8) afin que les événements clos restent visibles (replay/photos — SC-006).
     * Exclut `draft` et `cancelled`.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereIn('status', ['published', 'completed']);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('start_date', '>', Carbon::now());
    }

    public function scopeOngoing(Builder $query): Builder
    {
        $now = Carbon::now();

        return $query->where('start_date', '<=', $now)->where('end_date', '>=', $now);
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where('end_date', '<', Carbon::now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }
}
