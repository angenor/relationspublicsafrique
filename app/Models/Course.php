<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $appends = ['img','video_link','linkscours','vlink','duree'];


    public static function hasUserAbonned( Course $cours )
    {

        if (auth()->check()) {
            $conde = ['course_id' => $cours->id, 'user_id' => auth()->user()->id,'status'=>'payer']; ;

            return \App\Models\AchatCours::where($conde)->exists();


        }
        return  false;
    }

    public function user() : BelongsTo
    {
        return  $this->belongsTo(User::class) ;
    }

    public function getImgAttribute()
    {

        return  asset('storage/cours/'.$this->id.'.png');
    }

//video_link attribute
    public function  getVideoLinkAttribute () : string
    {
        return asset($this->video);
    }


    public function useer() : BelongsTo
    {
        return  $this->belongsTo(User::class) ;
    }

    public function category() : BelongsTo
    {
        return  $this->belongsTo(Category::class) ;
    }

    public function link() :Attribute
    {
        return  Attribute::make(
            get: fn($value) => route('apprendre.show',['id'=>$this->id,'slug'=>$this->slug])
        ) ;
    }

    public function duree() :Attribute
    {
        return  Attribute::make(
            get: fn($value) => $this->duration.' min'
        ) ;
    }

    public function linkscours() :Attribute
    {

        return  Attribute::make(
            get: fn($value) => route('cours.suivre-le-cours',['id'=>$this->id,'slug'=>$this->slug])
        ) ;
    }



    public function chapitres() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return  $this->HasMany(Chapitre::class) ;
    }

    public function  getVlinkAttribute () : string
    {
        return asset('videos/cours/'. ($this->id).'/'.$this->processed_file);

    }

    public function totalAchat()
    {
        return AchatCours::where(['course_id' => $this->id])->count();
    }








}
