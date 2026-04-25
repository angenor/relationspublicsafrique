<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormationEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'formation_id',
        'status',
        'progress',
        'enrolled_at',
        'completed_at',
        'last_accessed_at',
        'completed_chapters',
        'final_grade',
        'certificate_url',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'completed_chapters' => 'array',
        'final_grade' => 'decimal:2',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    // Scopes
    public function scopeEnrolled($query)
    {
        return $query->where('status', 'enrolled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDropped($query)
    {
        return $query->where('status', 'dropped');
    }

    // Helper methods
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'progress' => 100,
        ]);
    }

    public function updateProgress($progress)
    {
        $this->update([
            'progress' => min(100, max(0, $progress)),
            'last_accessed_at' => now(),
        ]);

        if ($progress >= 100) {
            $this->markAsCompleted();
        }
    }

    public function addCompletedChapter($chapterId)
    {
        $completedChapters = $this->completed_chapters ?? [];

        if (!in_array($chapterId, $completedChapters)) {
            $completedChapters[] = $chapterId;
            $this->update(['completed_chapters' => $completedChapters]);
        }
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function getProgressPercentageAttribute()
    {
        return $this->progress . '%';
    }
}
