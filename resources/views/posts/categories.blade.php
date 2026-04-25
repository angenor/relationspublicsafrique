@extends('layouts.default')

@section('content')

    <!--==============================
    Breadcumb
============================== -->
    <div class="breadcumb-wrapper " data-bg-src="{{asset('front/assets/img/breadcumb/breadcumb-bg.png')}}">
        <div class="container z-index-common">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">{{$pageTitle}}</h1>
                <p class="breadcumb-text">{{$pagesousTitle}}</p>
                <div class="breadcumb-menu-wrap">
                    <ul class="breadcumb-menu">
                        <li><a href="{{url('/')}}">Accueil</a></li>
                        <li>{{$pageTitle}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div><!--==============================
    About Area
==============================-->

    <section class="vs-blog-wrapper space-top space-extra-bottom">
        <div class="container">
            <div class="row gx-40">
                <div class="col-lg-8">

                    @forelse($posts as $p=>$post)
                        <div class="vs-blog blog-single">
                            <div class="blog-img">
                                <a href="{{$post->link}}">
                                    @if($post->image)
                                        <img src="{{$post->img}}" alt="{{$post->user->name}}">
                                    @endif
                                </a>



                                <a href="{{ $post->link }}" class="blog-date">
                                    @if($post->created_at)
                                        <span class="day">{{ $post->created_at->format('d') }}</span>
                                        <span class="month">{{ $post->created_at->format('M') }}</span>
                                    @else
                                        <span class="day">--</span>
                                        <span class="month">--</span>
                                    @endif
                                </a>
                            </div>
                            <div class="blog-content">
                                <div class="blog-meta">
                                    <a href="#"><i class="fal fa-user"></i>{{$post->user->name}}</a>
                                    {{--                                <a href="#"><i class="fal fa-comment-lines"></i>112 Comments</a>--}}
                                    <a href="#"><i class="fal fa-eye"></i>{{$post->view}}</a>
                                </div>
                                <h2 class="blog-title"><a href="{{$post->link}}">{!! $post->name !!}</a></h2>
                                <p>{!! $post->resume !!}</p>
                                <a href="{{$post->link}}" class="vs-btn style3"><i class="far fa-angle-right"></i>Consulter</a>
                            </div>
                        </div>


                    @empty

                       <div class="p-4 img-thumbnail ">
                           <h4> Aucun article pour cette catégorie</h4>
                       </div>

                    @endforelse



                    <div class="vs-pagination">
                        {{$posts->render()}}
                    </div>



                    {{--                        <div class="vs-pagination">--}}
                    {{--                            <ul>--}}
                    {{--                                <li class="prev"><a href="#">Previous</a></li>--}}
                    {{--                                <li><a href="#">1</a></li>--}}
                    {{--                                <li><a href="#">2</a></li>--}}
                    {{--                                <li><a href="#">.....</a></li>--}}
                    {{--                                <li><a href="#">10</a></li>--}}
                    {{--                                <li class="next"><a href="#">Next</a></li>--}}
                    {{--                            </ul>--}}
                    {{--                        </div>--}}


                </div>
                <div class="col-lg-4">
                    <aside class="sidebar-area">
                        <div class="widget widget_search  ">
                            <h3 class="widget_title">Recherche</h3>
                            <form class="search-form">
                                <input type="text" placeholder="Search Keyword">
                                <button type="submit"><i class="far fa-search"></i></button>
                            </form>
                        </div>
                        <div class="widget widget_categories    ">
                            <h3 class="widget_title">Categories</h3>
                            <ul>

                                @foreach($cats as $p=>$cat)
                                    <li><a href="{{  route('categories.slug', $cat->slug)}}">{{$cat->name}}</a> <span> ({{$cat->posts()->where(['type'=>'blog'])->count()}})</span></li>
                                @endforeach
                            </ul>
                        </div>
                        {{--        <div class="widget   ">
                                    <h3 class="widget_title">Recent News</h3>
                                    <div class="recent-post-wrap">
                                        <div class="recent-post">
                                            <div class="media-img"><img
                                                    src="{{asset('front/assets/img/blog/recent-post-1-1.jpg')}}" alt="thing">
                                            </div>
                                            <div class="media-body">
                                                <h4 class="post-title"><a class="text-inherit" href="#">Get Techs HTML5 JS
                                                        Tutorial Newbies</a></h4>
                                                <div class="recent-post-meta"><a href="#">15 February, 2023</a></div>
                                            </div>
                                        </div>
                                        <div class="recent-post">
                                            <div class="media-img"><img
                                                    src="{{asset('front/assets/img/blog/recent-post-1-2.jpg')}}" alt="thing">
                                            </div>
                                            <div class="media-body">
                                                <h4 class="post-title"><a class="text-inherit" href="#">Tadit Soul Can Tech
                                                        About Docan</a></h4>
                                                <div class="recent-post-meta"><a href="#">21 January, 2023</a></div>
                                            </div>
                                        </div>
                                        <div class="recent-post">
                                            <div class="media-img"><img
                                                    src="{{asset('front/assets/img/blog/recent-post-1-3.jpg')}}" alt="thing">
                                            </div>
                                            <div class="media-body">
                                                <h4 class="post-title"><a class="text-inherit" href="#">Goha Soul Can Tech Us
                                                        About Web</a></h4>
                                                <div class="recent-post-meta"><a href="#">29 November, 2023</a></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>--}}
                        {{--   <div class="widget   ">
                               <h4 class="widget_title">Gallery Photos</h4>
                               <div class="sidebar-gallery">
                                   <div class="gallery-thumb">
                                       <img src="{{asset('front/assets/img/widget/gal-1-1.jpg')}}" alt="Gallery Image"
                                            class="w-100">
                                       <a href="{{asset('front/assets/img/widget/gal-1-1.jpg')}}"
                                          class="popup-image gal-btn"><i class="fal fa-plus"></i></a>
                                   </div>
                                   <div class="gallery-thumb">
                                       <img src="{{asset('front/assets/img/widget/gal-1-2.jpg')}}" alt="Gallery Image"
                                            class="w-100">
                                       <a href="{{asset('front/assets/img/widget/gal-1-2.jpg')}}"
                                          class="popup-image gal-btn"><i class="fal fa-plus"></i></a>
                                   </div>
                                   <div class="gallery-thumb">
                                       <img src="{{asset('front/assets/img/widget/gal-1-3.jpg')}}" alt="Gallery Image"
                                            class="w-100">
                                       <a href="{{asset('front/assets/img/widget/gal-1-3.jpg')}}"
                                          class="popup-image gal-btn"><i class="fal fa-plus"></i></a>
                                   </div>
                                   <div class="gallery-thumb">
                                       <img src="{{asset('front/assets/img/widget/gal-1-4.jpg')}}" alt="Gallery Image"
                                            class="w-100">
                                       <a href="{{asset('front/assets/img/widget/gal-1-4.jpg')}}"
                                          class="popup-image gal-btn"><i class="fal fa-plus"></i></a>
                                   </div>
                                   <div class="gallery-thumb">
                                       <img src="{{asset('front/assets/img/widget/gal-1-5.jpg')}}" alt="Gallery Image"
                                            class="w-100">
                                       <a href="{{asset('front/assets/img/widget/gal-1-5.jpg')}}"
                                          class="popup-image gal-btn"><i class="fal fa-plus"></i></a>
                                   </div>
                                   <div class="gallery-thumb">
                                       <img src="{{asset('front/assets/img/widget/gal-1-6.jpg')}}" alt="Gallery Image"
                                            class="w-100">
                                       <a href="{{asset('front/assets/img/widget/gal-1-6.jpg')}}"
                                          class="popup-image gal-btn"><i class="fal fa-plus"></i></a>
                                   </div>
                               </div>
                           </div>--}}

                    </aside>
                </div>
            </div>
        </div>
    </section>

    <!--==============================
    CTA Area
  ==============================-->
    {{--    <section class="space-extra" data-bg-src="{{asset('front/assets/img/bg/blog-single-divider-bg-1-1.jpg')}}">--}}
    {{--        <div class="container">--}}
    {{--            <div class="row justify-content-between text-center text-lg-start">--}}
    {{--                <div class="col-lg-6 mb-40 mb-lg-0">--}}
    {{--                    <h2 class="mt-n2 h2 mb-3">Future Learn’s Purpose is to transform access to education.</h2>--}}
    {{--                    <p class=" mb-4 pb-2 fs-md col-xl-11">Sign up to our newsletter and we'll send fresh new courses and--}}
    {{--                        special offers direct to your inbox, once a week.</p>--}}
    {{--                    <a href="#" class="vs-btn style2"><i class="far fa-angle-right"></i>Get a Quote</a>--}}
    {{--                </div>--}}
    {{--                <div class="col-auto d-none d-lg-block">--}}
    {{--                    <div class="sec-line2"></div>--}}
    {{--                </div>--}}
    {{--                <div class="col-lg-auto">--}}
    {{--                    <h6 class="mt-n1">Academic Leadership Team</h6>--}}
    {{--                    <div class="mini-avater">--}}
    {{--                        <a href="#"><img src="{{asset('front/assets/img/team/team-s-1-1.png')}}" alt="avater"></a>--}}
    {{--                        <a href="#"><img src="{{asset('front/assets/img/team/team-s-1-2.png')}}" alt="avater"></a>--}}
    {{--                        <a href="#"><img src="{{asset('front/assets/img/team/team-s-1-3.png')}}" alt="avater"></a>--}}
    {{--                        <a href="#"><img src="{{asset('front/assets/img/team/team-s-1-4.png')}}" alt="avater"></a>--}}
    {{--                    </div>--}}
    {{--                </div>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </section>--}}


    <!--==============================
    Footer Area
  ==============================-->

    {{--    <div class="">
            <div class="py-4 mx-4 max-w-7xl lg:mx-auto">

                <div class="flex flex-col relative">
                    <div class="bg-white rounded w-64 shadow border mr-4 h-[calc(100vh-200px)] sticky top-20 left-0 hidden  ">

                    </div>
                    <div class="flex-1 grid gap-4 lg:grid-cols-3">
                        --}}{{--                   <div class=" h-64">--}}{{--
                        --}}{{--                        <div class="">--}}{{--

                        --}}{{--                            <textarea name="post" id="post" cols="30" rows="10" class="w-full border-none" placeholder="rédiger un article"></textarea>--}}{{--
                        --}}{{--                            <div class="">--}}{{--
                        --}}{{--                                --}}{{--
                        --}}{{--                            </div>--}}{{--

                        --}}{{--                        </div>--}}{{--

                        --}}{{--                   </div>--}}{{--
                        @foreach($posts as $p=>$post)

                            --}}{{--                       {{$post}}--}}{{--
                            <div class="blog-post bg-white rounded-lg overflow-hidden shadow">
                                <div class="flex p-3 items-center w-full border-b justify-between">
                                    <a href="{{$post->user->profil->link}}" class="flex  items-center   ">

                                        <img src="{{$post->user->profil->img}}" alt="{{$post->user->name}}" class="h-10 w-10 rounded-full object-cover shadow  bg-sky-900 ring-1 ring-gray-400">

                                        <div class="ml-2 ">
                                            <h4 class="text-xs font-bold text-sky-800 truncate">{{$post->user->profil->fullname}}</h4>
                                            <span class="text-xs">{{$post->created_at}}</span>
                                        </div>
                                    </a>
                                    <div class="flex  items-center">
                                        <button class="px-1">
                                            <ion-icon name="trash-outline" class="h-6 w-6"></ion-icon>
                                        </button>
                                        <button class="px-1">
                                            <ion-icon name="ellipsis-vertical-outline"></ion-icon>
                                        </button>
                                    </div>
                                </div>
                                <div class="py-1 px-3">
                                    <a href="{{$post->link}}">
                                        <h3 class=" text-sky-800  truncate text-lg font-semibold">{{$post->name}}</h3>
                                    </a>

                                </div>
                                <div class="blog-thumb">
                                    <a href="{{$post->link}}">
                                        <img src="{{$post->img}}" class="w-full  h-[300px] object-cover bg-sky-900"
                                             alt="{{$post->name}}">

                                    </a>
                                </div>
                                <div class="py-1 px-3 h-16">
                                        {!! $post->resume     !!}
                                </div>

                            </div>

                        @endforeach
                    </div>

                    <div class="flex items-center">
                        {{$posts->render()}}
                    </div>

                </div>
            </div>
        </div>--}}

@endsection

@section('title')
    Actualités
@endsection
