<?php

namespace App\Models;

use App\Helper\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Profil
 *
 * @method static \Database\Factories\ProfilFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Profil newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profil newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profil query()
 * @property int $id
 * @property string|null $name
 * @property string $slug
 * @property string $nom
 * @property string|null $prenom
 * @property string|null $title
 * @property string|null $fonction
 * @property string|null $domaine
 * @property string|null $facebook
 * @property string|null $twitter
 * @property string|null $youtube
 * @property string|null $linkding
 * @property string|null $site
 * @property string|null $contact
 * @property string|null $adresse
 * @property string|null $tel
 * @property string|null $email
 * @property string|null $bio
 * @property string $image
 * @property int $online
 * @property int $aprouve
 * @property int $pays_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $img
 * @property-read \App\Models\Pays|null $pays
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereAdresse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereAprouve($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereDomaine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereFonction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereLinkding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil wherePaysId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil wherePrenom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereSite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereYoutube($value)
 * @property string|null $cover
 * @method static \Illuminate\Database\Eloquent\Builder|Profil whereCover($value)
 * @mixin \Eloquent
 */
class Profil extends Model
{
    use HasFactory;
    public $guarded = ['id'];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function pays(): BelongsTo
    {
        return $this->belongsTo(Pays::class);
    }

    public function getImgAttribute()
    {
        return $this->image ? '/storage/' . $this->image : asset('img/img1.jpeg');
    }

    public function fullname(): Attribute
    {
        return  Attribute::make(
            get: fn($value) =>  $this->nom . ' ' . $this->prenom
        );
    }
    public function link(): Attribute
    {
        return  Attribute::make(
            get: fn($value) => route('profil.show', [$this->slug, $this->id])
        );
    }

    public function resumeBio(): Attribute
    {
        return  Attribute::make(
            get: fn($value) => \Str::limit($this->bio, 80)
        );
    }
}
