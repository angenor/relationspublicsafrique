<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Pays
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $online
 * @property string|null $indicatif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\PaysFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Pays newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pays newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pays query()
 * @method static \Illuminate\Database\Eloquent\Builder|Pays whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pays whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pays whereIndicatif($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pays whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pays whereOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pays whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pays whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Pays extends Model
{
    use HasFactory;

    public $guarded = ['id'];
}
