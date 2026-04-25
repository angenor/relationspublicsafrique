<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\ProfilResource;
use App\Models\Pays;
use App\Models\Post;
use App\Models\Profil;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function profil(Request $request)
    {
        $profile = Profil::where(['user_id' => \Auth::id()])->first() ;
        $pays = Pays::where([])->orderBy('name')->get();
       return  response()->json([
           'profil'=>new ProfilResource($profile),
           'pays'=>$pays
       ]);
    }


    public function posts(Request $request)
    {



        $post = Post::firstOrCreate(['user_id' => \Auth::id(),'slug' => '','name' => '','online' => -1]) ;
        $posts = $request->user()->posts()->where('online','<>',-1) ->latest()->paginate() ;

        $post->image = asset('images/front/ban.jpg') ;
        return   PostResource::collection($posts)->additional( ['post'=>new PostResource($post)]);
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'name'=>['required'],
            'content'=>['required'],
        ]);

        $post = $request->user()->posts()->where('id', $request->id)->first();
        $post->name = $request->name;
        $post->slug = \Str::slug($request->name);
        $post->content = $request->content;
        $post->image = $request->image;
        $post->online = $request->online ? 1 : 0 ;
        $post->update();
        return response()->json(['success'=>$post]);
    }
}
