<?php

namespace App\Models;

use App\Helper\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

/**
 * App\Models\Event
 *
 * @property int $id
 * @property string $title
 * @property string|null $slug
 * @property text $description
 * @property text|null $resume
 * @property string|null $image
 * @property string|null $location
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon $end_date
 * @property \Carbon\Carbon|null $registration_deadline
 * @property int|null $max_participants
 * @property int $current_participants
 * @property decimal $price
 * @property string $status
 * @property boolean $is_featured
 * @property boolean $online
 * @property int|null $user_id
 * @property int|null $category_id
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
    ];

    public static array $statuses = [
        'draft' => 'Brouillon',
        'published' => 'Publié',
        'cancelled' => 'Annulé',
        'completed' => 'Terminé',
    ];

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

    public function isUserRegistered(int $userId): bool
    {
        return $this->registrations()
            ->where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->exists();
    }

    public function getImgAttribute(): string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/default-event.jpg');
    }

    public function getResumeTextAttribute(): string
    {
        return $this->resume ?: \Str::limit($this->description, 200);
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

    public function getCanRegisterAttribute(): bool
    {
        if (!$this->online || $this->status !== 'published') {
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

    public function activate(): void
    {
        $this->online = true;
        $this->save();
    }

    public function deactivate(): void
    {
        $this->online = false;
        $this->save();
    }

    public function scopeOnline(Builder $query): Builder
    {
        return $query->where('online', true);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
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
