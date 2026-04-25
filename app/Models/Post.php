<?php

namespace App\Models;

use App\Helper\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder;

/**
 * App\Models\Post
 *
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property int|null $position
 * @property string|null $content
 * @property int $online
 * @property string|null $type
 * @property int $note
 * @property int|null $user_id
 * @property int|null $category_id
 * @property int $parent_id
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\PostFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUserId($value)
 * @property string|null $resume
 * @property int $view
 * @property string|null $externe_link
 * @property string $dateevente
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read string $by
 * @property-read mixed $img
 * @property-read string $published
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Post isonline()
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereDateevente($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereExterneLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereResume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereView($value)
 * @mixin \Eloquent
 */
class Post extends Model
{
    use HasFactory ,Sluggable;

    public $guarded = ['id'];
    public static $typearticles = [

        'sliders'=>' Les 3 Sliders',
        'bienvenue_sur_relations_publics'=>'BIENVENUE SUR RELATIONS PUBLICS AFRIQUE',
        'evenements'=>'Événements',
        'faq'=>'FAQ',
    ] ;

     public static $typeblogs = [

        'blog'=>'List des articles du blog',

    ] ;


    public function categories() :BelongsToMany
    {
        return $this->belongsToMany(Category::class);

    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getImgAttribute()
    {

        return  asset('storage/'.$this->image);
    }

    public function resume() :Attribute
    {
        return  Attribute::make(
            get: fn($value) => \Str::limit($this->content,200)
//            get: fn($value) =>  $this->content
        ) ;
    }

    public function link() :Attribute
    {
        return  Attribute::make(
            get: fn($value) => route('blog.show',['id'=>$this->id,'slug'=>$this->slug])
        ) ;
    }

    public function getPublishedAttribute(): string
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    public function getByAttribute(): string
    {
        return $this->user->name;
    }

    public function activate(): void
    {
        $this->online= 1 ;
        $this->save();

    }


    public function scopeIsonline(Builder $query): Builder
    {
        return  $query->where('online','=',1) ;
    }

//    public function getLinkAttribute(): string
//    {
//        return route('article.show',['id'=>$this->id,'slug'=>$this->slug]);
//    }

}
