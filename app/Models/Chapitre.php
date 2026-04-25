<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Chapitre extends Model
{
    use HasFactory;


    protected $guarded = ['id'];
    protected $table = 'chapitres';
    protected $appends = ['img','video_link','vlink'];

    public function course() : BelongsTo
    {
        return  $this->belongsTo(Course::class) ;
    }

    public function img() :Attribute
    {
        return  Attribute::make(
            get: fn($value) => asset('storage/chapitre/'.$this->id.'.png')
        ) ;
    }

    public function  getVideoLinkAttribute () : string
    {
        return asset('videos/'.  $this->video);

        return asset($this->video);
    }

    public function  getVlinkAttribute () : string
    {
        return asset('videos/chapitre/'. ($this->id).'/'.$this->processed_file);

        return asset($this->video);
    }






}
