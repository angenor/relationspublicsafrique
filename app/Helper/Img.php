<?php
/**
 * Created by IntelliJ IDEA.
 * User: e-commerce
 * Date: 13/06/2018
 * Time: 11:20
 */

namespace App\Helper;


use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;


class Img
{
    public static function resize($image,int $x , int $y, bool $resize =false){
        if(empty($image))
            return false ;
        $fileInfo = pathinfo( $image) ;
        $filename = Str::slug( $fileInfo['filename']).'-'.$x.'x'.$y.'.'.$fileInfo['extension'] ;

        $file = 'img/photos/min/';
        if(!file_exists($file ))
            mkdir($file,777, true);
        $file = 'img/photos/min/'. $filename ;

        if (file_exists($file)){
            return  asset($file)  ;
        }else{
//            dd( ($image) ) ;
//            Image::configure(array('driver' => 'imagick'));
            Image::configure(array('driver' => 'gd'));
            if($resize){
                $img = Image::make($image)->resize($x, $y);
            }else{
                $img = Image::make($image)->fit($x, $y);
            }

            $img->save( $file, 90);
            return  asset($file) ;

        }
    }
}
