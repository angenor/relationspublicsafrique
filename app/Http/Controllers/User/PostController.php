<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function consulterprofil()
    {

        $user = User::with(['profil','pays'])->where(['id'=>Auth::user()->id])->first();
        $posts = Post::with(['user'])->where(['online' => 1]) ->latest()->paginate();


        return view('user.consulterprofil',compact('user','posts'));
    }

        public function tableauDeBord()
    {

//        return "<h1>Désolé</h1>";
//        dd('king');
        $user = User::with(['profil','pays'])->where(['id'=>Auth::user()->id])->first();

        return view('user.posts.index',compact('user'));
    }


}
