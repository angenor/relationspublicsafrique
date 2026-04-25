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




    <section class="space-top space-extra-bottom">
        <div class="container">
            <div class="row">

                @foreach( $cours as $k=> $post )


                <div class="col-sm-6 col-xl-4">
                    <div class="course-style1 has-border">
                        <a href="{{$post->link}}">
                            <div class="course-img">

                                <img class="w-100" src="{{$post->img}}" alt="Course Img" style="height: 350px ; object-fit: cover" >

                            </div>
                        </a>

                        <div class="course-content">
                            <div class="course-top">
                                <div class="course-review">

                                </div>
                                <span class="course-price">${{$post->price}} </span>
                            </div>
                            <h3 class="h5 course-name"><a href="{{$post->link}}" class="text-inherit">{!! $post->name !!}</a></h3>
{{--                            <div class="course-teacher"><a class="text-inherit" href="#">By Ana Watson</a></div>--}}
                        </div>
                        <div class="course-meta">
{{--                            <span><i class="fal fa-users"></i> {!! $post->totalAchat() !!} </span>--}}
                            <span><i class="fal fa-clock"></i>{!! $post->duration !!}</span>
                            <span><i class="fal fa-calendar-alt"></i> {!! $post->duree !!}</span>
                        </div>
                    </div>
                </div>


                @endforeach

            </div>
            <div class="vs-pagination">
                <ul>
                   {{ $cours->links() }}
                </ul>
            </div>
        </div>
    </section>
    <!--==============================
    Footer Area
  ==============================-->



@endsection

@section('title')
    Apprendre
@endsection

