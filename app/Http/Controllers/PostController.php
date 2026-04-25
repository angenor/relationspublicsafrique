<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Post;
use App\Models\Profil;

class PostController extends Controller
{
    public function index()
    {
        //        $profils = Profil::with(['pays'])->where(['aprouve' => 1])->get() ;
        $profils = Profil::with(['pays'])->where(['online' => 1])->get();
        $sliders = Post::with(['user'])->where(['type' => 'sliders', 'online' => 1])->limit(4)->get();
        $evenements = Post::with(['user'])->where(['type' => 'evenements', 'online' => 1])->limit(4)->get();
        $blogs = Post::with(['user'])->where(['type' => 'blog', 'online' => 1])->latest()->limit(2)->get();
        $bienvenue = Post::with(['user'])->where(['type' => 'bienvenue_sur_relations_publics', 'online' => 1])->first();
        $faqs = Post::with(['user'])->where(['type' => 'faq', 'online' => 1])->limit(4)->get();

        return view('posts.home', compact('profils', 'sliders', 'evenements', 'blogs', 'bienvenue', 'faqs'));
    }

    public function explorer()
    {
        $profils = Profil::with(['pays'])->where([])->latest()->paginate();

        return view('posts.explorer', compact('profils'));
    }

    public function blog()
    {

        $pageTitle = 'Actualités';
        $pagesousTitle = 'Informez-vous';
        $cats = Category::where(['online' => 1])->latest()->orderBy('name')->latest()->get();
        $posts = Post::with(['user'])->where(['online' => 1])->where(function ($query) {
            if (request()->has('type')) {
                $pageTitle = request('type');
                $query->where('type', request('type'));
            } else {
                $query->where('type', 'blog');
            }
        })->latest()->paginate();

        return view('posts.blog', compact('posts', 'pagesousTitle', 'pageTitle', 'cats'));
    }

    public function contact()
    {
        return view('posts.contact');
    }

    public function recherche() {}

    public function profil()
    {
        return view('posts.profil');
    }

    public function apropos()
    {
        $pageTitle = 'A propos';
        $pagesousTitle = 'A propos';

        return view('posts.apropos', compact('pagesousTitle', 'pageTitle'));
    }

    public function appartenir()
    {
        $pageTitle = 'Appartenir';
        $pagesousTitle = 'Appartenir';
        $profils = Profil::where('online', 1)->orderBy('name')->paginate(12);

        return view('posts.appartenir', compact('pagesousTitle', 'pageTitle', 'profils'));
    }

    public function apprendre()
    {
        $pageTitle = 'Apprendre';
        $pagesousTitle = 'Apprendre';
        $cours = Course::with(['user'])->where(['online' => 1])->where('slug', '<>', '')->latest()->paginate();

        //        foreach ($cours->items() as $item) {
        //            $item->online = 1 ;
        //            if( $item->name == null)
        //            $item->save();
        //        }
        return view('posts.apprendre', compact('pageTitle', 'pagesousTitle', 'cours'));
    }

    public function ressources()
    {
        $pageTitle = 'Ressource';
        $pagesousTitle = 'Ressource';

        return view('posts.ressources', compact('pageTitle', 'pagesousTitle'));
    }

    public function shoqBlog(string $slug, int $id)
    {
        $post = Post::with(['user.profil'])->where(['online' => 1, 'id' => $id])->first();
        $pageTitle = 'Actualités';
        $pagesousTitle = 'Informez-vous';
        $post->view++;
        $post->save();
        $cats = Category::where(['online' => 1])->latest()->orderBy('name')->latest()->get();

        $btnShare = \Share::page($post->link, $post->name)
            ->facebook()
            ->twitter()
            ->linkedin('Extra linkedin summary can be passed here')
            ->whatsapp()
            ->getRawLinks();

        return view('posts.show', compact('post', 'pageTitle', 'pagesousTitle', 'cats', 'btnShare'));
    }

    public function categories(string $slug)
    {

        $pageTitle = 'Actualités';
        $pagesousTitle = 'Informez-vous';
        $posts = Post::with(['user'])
            ->where(['online' => 1])
            ->whereHas('categories', function ($q) use ($slug) {
                $q->where('slug', $slug);
            })
            ->latest()
            ->paginate();

        $cats = Category::where(['online' => 1])->latest()->orderBy('name')->latest()->get();

        return view('posts.categories', compact('posts', 'pageTitle', 'pagesousTitle', 'cats'));
    }
}
