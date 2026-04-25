<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Formation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'content',
        'image',
        'video',
        'duration',
        'price',
        'level',
        'level_name',
        'language',
        'is_published',
        'is_featured',
        'sort_order',
        'tags',
        'certificate',
        'max_students',
        'rating',
        'rating_count',
        'enrollment_count',
        'published_at',
        'start_date',
        'end_date',
        'instructor_id',
        'category_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'tags' => 'array',
        'published_at' => 'datetime',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'rating' => 'decimal:2',
    ];

    // Relations
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructors::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function chapitres(): HasMany
    {
        return $this->hasMany(Chapitre::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(FormationEnrollment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(FormationReview::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'formation_enrollments')
            ->withPivot(['status', 'progress', 'enrolled_at', 'completed_at', 'last_accessed_at', 'completed_chapters', 'final_grade', 'certificate_url'])
            ->withTimestamps();
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level_name', $level);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhereJsonContains('tags', $search);
        });
    }


    // Accessors & Mutators
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('img/' . $this->image);
        }
        return asset('img/formations/formation-default.jpg');
    }

    public function getVideoUrlAttribute()
    {
        if ($this->video) {
            return asset('storage/' . $this->video);
        }
        return null;
    }

    public function getFormattedPriceAttribute()
    {
        if ($this->price) {
            return number_format($this->price, 0, ',', ' ') . ' FCFA';
        }
        return 'Gratuit';
    }

    public function getFormattedDurationAttribute()
    {
        if ($this->duration) {
            $hours = floor($this->duration / 60);
            $minutes = $this->duration % 60;

            if ($hours > 0) {
                return $hours . 'h ' . $minutes . 'min';
            }
            return $minutes . 'min';
        }
        return null;
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }

    public function getEnrollmentCountAttribute()
    {
        return $this->enrollments()->count();
    }

    public function getIsEnrolledAttribute()
    {
        if (auth()->check()) {
            return $this->enrollments()->where('user_id', auth()->id())->exists();
        }
        return false;
    }

    public function getProgressAttribute()
    {
        if (auth()->check()) {
            $enrollment = $this->enrollments()->where('user_id', auth()->id())->first();
            return $enrollment ? $enrollment->progress : 0;
        }
        return 0;
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($formation) {
            if (empty($formation->slug)) {
                $formation->slug = Str::slug($formation->name);
            }
        });

        static::updating(function ($formation) {
            if ($formation->isDirty('name') && empty($formation->slug)) {
                $formation->slug = Str::slug($formation->name);
            }
        });
    }

    // Helper methods
    public function canEnroll()
    {
        if (!$this->is_published) {
            return false;
        }

        if ($this->max_students && $this->enrollments()->count() >= $this->max_students) {
            return false;
        }

        if ($this->end_date && $this->end_date->isPast()) {
            return false;
        }

        return true;
    }

    public function isCompletedByUser($userId = null)
    {
        $userId = $userId ?? auth()->id();

        if (!$userId) {
            return false;
        }

        $enrollment = $this->enrollments()->where('user_id', $userId)->first();
        return $enrollment && $enrollment->status === 'completed';
    }

    public function updateRating()
    {
        $this->rating = $this->reviews()->avg('rating') ?? 0;
        $this->rating_count = $this->reviews()->count();
        $this->save();
    }
}
