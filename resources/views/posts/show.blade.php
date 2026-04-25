@extends('layouts.default')
@section('meta')
    <meta name="description" content="{{ $post->name }}">
    <meta name="keywords" content="{{$post->name  }}">
    <meta name="author" content="{{ $post->user->name }}">



    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $post->name }} | Relations Publics Afrique">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($post->content,50) }}">
    <meta property="og:image" content="{{ $post->img }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="Relations Publics Afrique">
    <meta property="article:published_time" content="{{ $post->created_at->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
    <meta property="article:author" content="{{ $post->user->name }}">
    <meta property="article:section" content="{{ $post->categories[0]->name?? $pageTitle }}">
    <meta property="article:tag" content="{{ $post->name }}">

    <!-- Twitter -->
@endsection

@section('content')





    <div class="breadcumb-wrapper " data-bg-src="{{asset('front/assets/img/breadcumb/breadcumb-bg.png')}}">
        <div class="container z-index-common">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">{{$pageTitle}}</h1>
                <p class="breadcumb-text">{{$pagesousTitle}}</p>
                <div class="breadcumb-menu-wrap">
                    <ul class="breadcumb-menu">
                        <li><a href="{{url('/')}}">Accueil</a></li>
                        <li><a href="{{route('blog')}}">{{$pageTitle}}</a></li>
                        <li>{{$post->name}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!--==============================
          Blog Area
      ==============================-->
    <section class="vs-blog-wrapper blog-details space-top space-extra-bottom">
        <div class="container">
            <div class="row gx-40">
                <div class="col-lg-8">

                    <div class="vs-blog blog-single">
                        <div class="blog-img">
                            @if($post->image)
                                <img src="{{$post->img}}" alt="Blog Image" class="img-fluid">
                            @endif


                            @if($post->created_at)


                                    <a href="#" class="blog-date">
                                        <span class="day">{{ $post->created_at->format('d') }}</span>
                                        <span class="month">{{ $post->created_at->format('M') }}</span>
                                    </a>
                            @else

                            @endif


                        </div>
                        <div class="">
                            {!! $post->video !!}
                        </div>
                        <div class="blog-content">
                            <div class="blog-meta">
                                <a href="#"><i class="fal fa-user"></i>{{$post->user->name}}</a>
{{--                                <a href="#"><i class="fal fa-comment-lines"></i>112 Comments</a>--}}
                                <a href="#"><i class="fal fa-eye"></i>{{$post->view}}</a>
                            </div>
                            <h2 class="blog-title">{!! $post->name !!}</h2>
                            <article>
                                {!! $post->content !!}
                            </article>

                        </div>
                        <div class="share-links clearfix">
                            <div class="row justify-content-between">
                                <div class="col-xl-auto"><span class="share-links-title">Tags:</span>
                                    @foreach($post->categories as $k=>$cat)
                                        <div class="tagcloud">
                                            <a href="{{ route('categories.slug', $cat->slug) }}">{{$cat->name}}</a>

                                        </div>
                                    @endforeach


                                </div>
                                <div class="col-xl-auto text-xl-end">
                                    <span class="share-links-title">Partager l'article</span>



                                    <ul class="social-links">
                                        <li><a href="{{$btnShare['facebook']}}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="{{$btnShare['twitter']}}" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="{{$btnShare['linkedin']}}" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div><!-- Post Pagination Style -->

                       {{-- <div class="vs-comments-wrap   ">
                            <h2 class="blog-inner-title">3 Comments</h2>
                            <ul class="comment-list">
                                <li class="vs-comment-item">
                                    <div class="vs-post-comment">
                                        <div class="comment-avater">
                                            <img src="{{asset('front/assets/img/blog/comment-author-1.jpg')}}" alt="Comment Author">
                                        </div>
                                        <div class="comment-content">
                                            <h4 class="name h4">Lynda Reyes</h4>
                                            <span class="commented-on"><i class="fal fa-calendar-alt"></i> 22 March, 2023</span>
                                            <p class="text">Deauty products Lorem ipsum dolor sit amet, consectetur adipisicing the Lorem ipsum dolor sitamet consectetur adipiscing eiusmod</p>
                                            <div class="reply_and_edit">
                                                <a href="#" class="replay-btn"><i class="fas fa-reply"></i>Replay</a>
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="children">
                                        <li class="vs-comment-item">
                                            <div class="vs-post-comment">
                                                <div class="comment-avater">
                                                    <img src="{{asset('front/assets/img/blog/comment-author-2.jpg')}}" alt="Comment Author">
                                                </div>
                                                <div class="comment-content">
                                                    <h4 class="name h4">Adom Lee</h4>
                                                    <span class="commented-on"><i class="fal fa-calendar-alt"></i> 23 Augest, 2023</span>
                                                    <p class="text">Deauty products Lorem ipsum dolor sit amet, consectetur adipisicing the Lorem ipsum dolor sitamet consectetur adipiscing eiusmod</p>
                                                    <div class="reply_and_edit">
                                                        <a href="#" class="replay-btn"><i class="fas fa-reply"></i>Replay</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                                <li class="vs-comment-item">
                                    <div class="vs-post-comment">
                                        <div class="comment-avater">
                                            <img src="{{asset('front/assets/img/blog/comment-author-3.jpg')}}" alt="Comment Author">
                                        </div>
                                        <div class="comment-content">
                                            <h4 class="name h4">Tara sing</h4>
                                            <span class="commented-on"><i class="fal fa-calendar-alt"></i> 26 April, 2023</span>
                                            <p class="text">Deauty products Lorem ipsum dolor sit amet, consectetur adipisicing the Lorem ipsum dolor sitamet consectetur adipiscing eiusmod</p>
                                            <div class="reply_and_edit">
                                                <a href="#" class="replay-btn"><i class="fas fa-reply"></i>Replay</a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="vs-comment-form  ">
                            <div id="respond" class="comment-respond">
                                <h3 class="blog-inner-title">Leave a Comment</h3>
                                <p class="form-text">Your email address will not be published. Required fields are marked*</p>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <input type="text" class="form-control" placeholder="Full Name">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <input type="email" class="form-control" placeholder="Email Address">
                                    </div>
                                    <div class="col-12 form-group">
                                        <textarea class="form-control" placeholder="Write your comment"></textarea>
                                    </div>
                                    <div class="col-12 ">
                                        <div class="custom-checkbox notice">
                                            <input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes">
                                            <label for="wp-comment-cookies-consent"> Save my name, email, and website in this browser for the next time I comment.</label>
                                        </div>
                                    </div>
                                    <div class="col-12 form-group">
                                        <button class="vs-btn">Post Comment</button>
                                    </div>
                                </div>
                            </div>
                        </div>--}}
                    </div>

                </div>
                <div class="col-lg-4">
                    <aside class="sidebar-area">

                        <div class="widget widget_categories">
                            <h3 class="widget_title">Categories --------</h3>
                            <ul>

                                @foreach($cats as $p=>$cat)
                                    <li><a href="{{ route('categories.slug', $cat->slug) }}">{{$cat->name}}</a> <span> ({{$cat->posts()->where(['type'=>'blog'])->count()}})</span></li>
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



{{--
    <div class="">
        <div class="py-4 mx-4 max-w-7xl lg:mx-auto">

            <div class="flex flex-col relative">

                        <div class="blog-post bg-white mb-4 rounded-lg overflow-hidden h-full min-h-screen  ">
                            <div class="flex p-3 items-center w-full border-b justify-between">
                                <a href="{{$post->user->profil->link}}" class="flex  items-center w-1/2  ">

                                        <img src="{{$post->user->profil->img}}" alt="{{$post->user->name}}" class="h-10 w-10 rounded-full object-cover shadow  bg-sky-900 ring-1 ring-gray-400">

                                    <div class="ml-3">
                                        <h4 class="text-sm font-bold text-sky-600">{{$post->user->profil->fullname}}</h4>
                                        <span class="text-xs font-semibold ">{{$post->created_at}}</span>
                                    </div>
                                </a>
                                <div class="flex  items-center">
                                    <button class="px-3">
                                        <ion-icon name="trash-outline" class="h-6 w-6"></ion-icon>
                                    </button>
                                    <button class="px-3">
                                        <ion-icon name="ellipsis-vertical-outline"></ion-icon>
                                    </button>
                                </div>
                            </div>

                            <div class="p-4">
                                <a href="{{$post->link}}">
                                    <h3 class=" text-sky-800   text-2xl">{{$post->name}}</h3>
                                </a>
                            </div>

                            <div class="flex  w-full">

                                <div class="py-1 px-3 w-2/5">
                                    <div class="blog-thumb">
                                        <a href="{{$post->link}}">
                                            <img src="{{$post->img}}" class="w-full rounded  object-cover bg-sky-900"  alt="{{$post->name}}">

                                        </a>
                                    </div>
                                </div>

                                <div class="py-1 px-3 w-3/5 ">

                                    <p class="text-sm">
                                        {{$post->img}}
                                        {{$post->image}}
                                    <hr>
                                        {!! $post->content !!}
                                    </p>
                                </div>

                            </div>


                        </div>


            </div>
        </div>
    </div>
--}}

@endsection

@section('title')
     {{$post->name}}
@endsection

@section('jss')


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs=" crossorigin="anonymous"></script>
<script src="{{ asset('js/share.js') }}"></script>




@endsection
