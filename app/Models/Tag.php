<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['libelle', 'slug'];

    protected static function booted(): void
    {
        static::saving(function (self $model): void {
            if (empty($model->slug) && !empty($model->libelle)) {
                $model->slug = Str::slug($model->libelle);
            }
        });
    }

    public function profils(): BelongsToMany
    {
        return $this->belongsToMany(Profil::class, 'profil_tag');
    }
}
