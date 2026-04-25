<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\WidgetText
 *
 * @property int $id
 * @property string $name
 * @property string $key
 * @property string $slug
 * @property string|null $link
 * @property string|null $content
 * @property string|null $resume
 * @property int|null $position
 * @property int|null $online
 * @property string|null $image
 * @property-read mixed $img
 * @method static \Database\Factories\WidgetTextFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|WidgetText newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WidgetText newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WidgetText query()
 * @method static \Illuminate\Database\Eloquent\Builder|WidgetText whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WidgetText whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WidgetText whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WidgetText whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WidgetText whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WidgetText whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WidgetText whereOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WidgetText wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WidgetText whereResume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WidgetText whereSlug($value)
 * @mixin \Eloquent
 */
class WidgetText extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public $appends = ['img'];
    public $timestamps = false;


    public static $types = [
        'telephone1_site_web' => [
            'key' => 'telephone1_site_web',
            'name' => 'Téléphone 1',
            'slug' =>  'telephone1_site_web',
            'image' => 'img/cours.jpg',

        ],
        'email_site_web' => [
            'key' => 'email_site_web',
            'name' => 'email_site_web 1',
            'slug' =>  'email_site_web',
            'image' => 'img/cours.jpg',

        ],

        'bienvenue_sur_relation_public' => [
            'key' => 'bienvenue_sur_relation_public',
            'name' => 'bienvenue_sur_relation_public 1',
            'slug' =>  'bienvenue_sur_relation_public',
            'image' => 'img/cours.jpg',

        ],


        'block_annuaire_accueil' => [
            'key' => 'block_annuaire_accueil',
            'name' => 'ANNUAIRE',
            'slug' =>  'annuaire',
            'image' => 'front/assets/img/icon/feature-icon-1-1.svg',
            'content' => 'rejoignez la communauté des communicants d’Afrique',

        ],
        'block_cours_accueil' => [
            'key' => 'block_cours_accueil',
            'name' => 'COURS',
            'slug' =>  'cours',
            'image' => 'front/assets/img/icon/feature-icon-1-1.svg',
            'content' => 'formez-vous aux relations publics',

        ],
        'block_actualité_accueil' => [
            'key' => 'block_actualité_accueil',
            'name' => 'ACTUALITÉ',
            'slug' =>  'cours',
            'image' => 'front/assets/img/icon/feature-icon-1-1.svg',
            'content' => 'l’actualité de l’Afrique vue par des communicants',

        ],
        'block_co_fondateur' => [
            'key' => 'block_co_fondateur',
            'name' => 'block_co_fondateur 1',
            'slug' =>  'block_co_fondateur',
            'image' => 'img/cours.jpg',

        ],

        'lien_facebook' => [
            'key' => 'lien_facebook',
            'name' => 'lien_facebook 1',
            'slug' =>  'lien_facebook',
            'image' => 'img/cours.jpg',

        ],
        'lien_twitter' => [
            'key' => 'lien_twitter',
            'name' => 'lien_twitter 1',
            'slug' =>  'lien_twitter',
            'image' => 'img/cours.jpg',

        ],

        'lien_linkedin' => [
            'key' => 'lien_linkedin',
            'name' => 'lien_linkedin 1',
            'slug' =>  'lien_linkedin',
            'image' => 'img/cours.jpg',

        ],

        'contenu_copy_write' => [
            'key' => 'contenu_copy_write',
            'name' => 'contenu_copy_write 1',
            'slug' =>  'contenu_copy_write',
            'image' => 'img/cours.jpg',

        ],

        'slogan_site_web' => [
            'key' => 'slogan_site_web',
            'name' => 'slogan_site_web 1',
            'slug' =>  'slogan_site_web',
            'image' => 'img/cours.jpg',

        ],
        'logo_footer_site_web' => [
            'key' => 'logo_footer_site_web',
            'name' => 'logo_footer_site_web 1',
            'slug' =>  'logo_footer_site_web',
            'image' => 'img/cours.jpg',

        ],
        'logo_topmeneu_site_web' => [
            'key' => 'logo_footer_site_web',
            'name' => 'logo_footer_site_web 1',
            'slug' =>  'logo_footer_site_web',
            'image' => 'logos/logo1.png',

        ],
        'block_partenaire_photo80x80' => [
            'key' => 'block_partenaire_photo80x80',
            'name' => 'block_partenaire_photo80x80 1',
            'slug' =>  'block_partenaire_photo80x80',
            'image' => 'logos/logo1.png',


        ],

    ];

    public function getImgAttribute()
    {
        return  asset('storage/' . $this->image);
    }

    public static function getImage(string $key)
    {
        $widget = Cache::remember("widget_$key", 3600, function () use ($key) {
            return WidgetText::where(['online' => 1, 'key' => $key])->first();
        });

        if (!$widget) {
            \Log::warning("Widget manquant pour image: $key");
            return null;
        }

        return $widget->img;
    }

    public static function getName(string $key)
    {
        $widget = Cache::remember("widget_$key", 3600, function () use ($key) {
            return WidgetText::where(['online' => 1, 'key' => $key])->first();
        });

        if (!$widget) {
            \Log::warning("Widget manquant pour nom: $key");
            return null;
        }

        return $widget->name;
    }


    public static function getContent(string $key)
    {
        // Validation de la clé
        if (!preg_match('/^[a-z0-9_]+$/', $key)) {
            \Log::warning("Clé widget invalide: $key");
            return null;
        }

        $widget = Cache::remember("widget_$key", 3600, function () use ($key) {
            return WidgetText::where(['online' => 1, 'key' => $key])->first();
        });

        if (!$widget) {
            \Log::warning("Widget manquant: $key");
            return null;
        }

        return $widget;
    }


    public static function getLink(string $key)
    {
        $widget = Cache::remember("widget_$key", 3600, function () use ($key) {
            return WidgetText::where(['online' => 1, 'key' => $key])->first();
        });

        if (!$widget) {
            \Log::warning("Widget manquant pour lien: $key");
            return null;
        }

        return $widget->link;
    }


    public function activate()
    {
        $this->online = ! $this->online;
        $this->save();
    }

    /**
     * Récupère tous les widgets actifs avec cache
     */
    public static function getAllWidgets()
    {
        return Cache::remember('all_widgets', 3600, function () {
            return WidgetText::where('online', 1)->get()->keyBy('key');
        });
    }

    /**
     * Vide le cache des widgets
     */
    public static function clearCache()
    {
        Cache::forget('all_widgets');
        // Vider le cache de tous les widgets individuels
        $keys = WidgetText::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("widget_$key");
        }
    }
}
