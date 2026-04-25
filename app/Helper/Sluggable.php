<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gatien
 * Date: 02/04/2017
 * Time: 09:49
 */

namespace App\Helper;

use Illuminate\Support\Str;


trait Sluggable
{

    public function setSlugAttribute($slug){

        if (empty($slug)){
            $this->attributes['slug'] = Str::slug($this->name);
        }else{
            $this->attributes['slug'] = Str::slug($slug);
        }
    }

}
