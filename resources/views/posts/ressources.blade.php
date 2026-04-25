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


    <section class="space" data-bg-src="{{asset('front/assets/img/bg/course-bg-pattern.jpg')}}">
        <div class="container">
            <div class="row">
                <div class="col-xl-6 m-auto">
                    <div class="title-area text-center wow fadeInUp" data-wow-delay="0.3s">
                        <div class="sec-icon">
                            <div class="vs-circle"></div>
                        </div>
                        <span class="sec-subtitle">{{$pageTitle}}</span>
                        <h2 class="sec-title h1">{{$pageTitle}}</h2>
                    </div>
                </div>
            </div>
            <div class="row wow fadeInUp" data-wow-delay="0.4s">
                <div class="col-sm-6 col-xl-4">
                    <div class="media-style9">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-1.svg')}}" alt="icon"></div>
                        <h5 class="media-title">Interactive Lessons</h5>
                        <p>DucDucamb welcomed every pain mstances owing to the claims of will frequently.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="media-style9">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-2.svg')}}" alt="icon"></div>
                        <h5 class="media-title">Free First Lesson</h5>
                        <p>DucDucamb welcomed every pain mstances owing to the claims of will frequently.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="media-style9">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-3.svg')}}" alt="icon"></div>
                        <h5 class="media-title">Trained & Experienced </h5>
                        <p>DucDucamb welcomed every pain mstances owing to the claims of will frequently.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="media-style9">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-4.svg')}}" alt="icon"></div>
                        <h5 class="media-title">Question, Quiz & Course </h5>
                        <p>DucDucamb welcomed every pain mstances owing to the claims of will frequently.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="media-style9">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-5.svg')}}" alt="icon"></div>
                        <h5 class="media-title">Teach From Anywhere</h5>
                        <p>DucDucamb welcomed every pain mstances owing to the claims of will frequently.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="media-style9">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-6.svg')}}" alt="icon"></div>
                        <h5 class="media-title">Work Independently</h5>
                        <p>DucDucamb welcomed every pain mstances owing to the claims of will frequently.</p>
                    </div>
                </div>
            </div>
            <div class="text-center pt-lg-4">
                <p class="font-body fs-md fw-medium mb-2">Call us now and transform your career today</p>

            </div>
        </div>
    </section><!--==============================
  Work Process Area
  ==============================-->



@endsection

@section('title')
    Resources
@endsection

