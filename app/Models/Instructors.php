<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instructors extends Model
{
    use HasFactory;

    protected $table = 'instructors';

    protected $guarded = [];

    public function courses() :HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function instructor() : BelongsTo
    {
        return $this->belongsTo(Instructors::class);
    }
}
