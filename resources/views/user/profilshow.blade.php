@extends('layouts.default')

@section('content')



        <div class="breadcumb-wrapper " data-bg-src="{{asset('front/assets/img/breadcumb/breadcumb-bg.png')}}">
            <div class="container z-index-common">
                <div class="breadcumb-content">
                    <h1 class="breadcumb-title">{{$pageTitle}}</h1>
                    <p class="breadcumb-text">{{$pagesousTitle}}</p>
                    <div class="breadcumb-menu-wrap">
                        <ul class="breadcumb-menu">
                            <li><a href="{{url('/')}}">Accueil</a></li>
                            <li><a href="{{route('appartenir')}}" wire:navigate>{{$pageTitle}}</a></li>
                            <li>{{$profil->fullname}}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>


        <section class="space-top space-extra-bottom">
            <div class="container">
                <div class="row justify-content-center align-items-center gx-80 mb-lg-4 pb-3">
                    <div class="col-lg-5 col-xl-auto order-lg-2 mb-4 mb-lg-0 pb-2 pb-lg-0">
                        <div class="img-box1 style3">
                            <div class="vs-circle">
                                <div class="mega-hover">
                                    <img src="{{$profil->img}}" alt="{{$profil->name}}"    id="profilimg-show" style=" height: 300px ; width: 300px">
{{--                                    <img src="{{asset('front/assets/img/about/about-1-1.png')}}" alt="banner">--}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 col-xl order-lg-1 mb-4 mb-md-0">
                        <div class="team-details">
                            <h2 class="team-name h2">{{$profil->fullname}}</h2>
                            <p class="team-degi">{{$profil->fullname}}</p>

                            <p class="team-experi">{{$profil->pays->name}}</p>
                            <p class="team-experi">{{$profil->adresse}}</p>
{{--                            <div class="team-review"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>--}}
                            <div class="social-style2">
                                @if(!empty($profil->facebook))
                                    <a target="_blank" href="{{$profil->facebook}}"><i class="fab fa-facebook-f m-lg-2"></i></a>

                                @endif
                                @if(!empty( $profil->twitter))
                                    <a target="_blank" href="{{$profil->twitter}}"><i class="fab fa-twitter m-lg-2"></i></a>

                                @endif

                                @if( !empty($profil->linkding))
                                    <a target="_blank" href="{{$profil->linkding}}"><i class="fab fa-linkedin-in m-lg-2"></i></a>

                                @endif

                                @if(!empty($profil->site))
                                    <a target="_blank" href="{{$profil->site}}"><i class="fab fa-wolf-pack-battalion m-lg-2"></i></a>


                                @endif

                                @if(!empty($profil->youtube))
                                    <a target="_blank" href="{{$profil->youtube}}"><i class="fab fa-youtube m-lg-2"></i></a>

                                @endif

                            </div>
                        </div>
                    </div>


                   {{-- <div class="col-md-6 col-lg-3 col-xl order-lg-3">
                        <h4 class="border-title2">Qualifications</h4>
                        <div class="graduation-media">
                            <h6 class="year">2001.</h6>
                            <div class="media-body">
                                <h6 class="media-title">Post Graduation</h6>
                                <p class="media-text">Oxford Universty</p>
                            </div>
                        </div>
                        <div class="graduation-media">
                            <h6 class="year">2005.</h6>
                            <div class="media-body">
                                <h6 class="media-title">Graduation in English</h6>
                                <p class="media-text">Oxford Universty</p>
                            </div>
                        </div>
                        <div class="graduation-media">
                            <h6 class="year">2011.</h6>
                            <div class="media-body">
                                <h6 class="media-title">Master of Arts</h6>
                                <p class="media-text">Oxford Universty</p>
                            </div>
                        </div>
                    </div>--}}


                </div>
                <h2 class="border-title2 mb-4">A Propos</h2>
                <p>
                    {!! $profil->bio !!}
                </p>

            </div>
        </section>
{{--        <section class="space-top space-extra-bottom" data-bg-src="{{asset('front/assets/img/bg/course-bg-pattern.jpg')}}">
            <div class="container">
                <div class="title-area text-center wow fadeInUp" data-wow-delay="0.3s">
                    <div class="sec-icon">
                        <div class="vs-circle"></div>
                    </div>
                    <span class="sec-subtitle">WELCOME TO GLOBAL EDUCATION</span>
                    <h2 class="sec-title">Explore Courses</h2>
                </div>
                <div class="row vs-carousel" data-slide-show="3" data-lg-slide-show="3" data-md-slide-show="2">
                    <div class="col-sm-6 col-xl-4">
                        <div class="course-style2">
                            <div class="course-img">
                                <a href="#"><img class="w-100" src="{{asset('front/assets/img/course/course-2-1.jpg')}}" alt="Course Img"></a>
                                <span class="course-price">$778</span>
                            </div>
                            <div class="course-content">
                                <h3 class="h5 course-name"><a href="#" class="text-inherit">Advance Begineer's Goal & Managing Course</a></h3>
                                <div class="course-meta">
                                    <span><i class="fas fa-user-tie"></i>204 Students</span>
                                    <a href="#"><i class="far fa-tv"></i>12 Leson</a>
                                    <span><i class="fal fa-clock"></i>2h 11m</span>
                                </div>
                                <div class="course-footer">
                                    <div class="course-teacher"><a href="#" class="text-inherit"><img src="{{asset('front/assets/img/course/course-2-1.png')}}" alt="Course">By Ana Watson</a></div>
                                    <div class="course-review"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <div class="course-style2">
                            <div class="course-img">
                                <a href="#"><img class="w-100" src="{{asset('front/assets/img/course/course-2-2.jpg')}}" alt="Course Img"></a>
                                <span class="course-price">$963</span>
                            </div>
                            <div class="course-content">
                                <h3 class="h5 course-name"><a href="#" class="text-inherit">Advance Technology & Architecture Course</a></h3>
                                <div class="course-meta">
                                    <span><i class="fas fa-user-tie"></i>779 Students</span>
                                    <a href="#"><i class="far fa-tv"></i>79 Leson</a>
                                    <span><i class="fal fa-clock"></i>6h 36m</span>
                                </div>
                                <div class="course-footer">
                                    <div class="course-teacher"><a href="#" class="text-inherit"><img src="{{asset('front/assets/img/course/course-2-2.png')}}" alt="Course">By Vivi Marian</a></div>
                                    <div class="course-review"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <div class="course-style2">
                            <div class="course-img">
                                <a href="#"><img class="w-100" src="{{asset('front/assets/img/course/course-2-3.jpg')}}" alt="Course Img"></a>
                                <span class="course-price">$445</span>
                            </div>
                            <div class="course-content">
                                <h3 class="h5 course-name"><a href="#" class="text-inherit">Basic Chemistry Programs arranged on Lab</a></h3>
                                <div class="course-meta">
                                    <span><i class="fas fa-user-tie"></i>75 Students</span>
                                    <a href="#"><i class="far fa-tv"></i>78 Leson</a>
                                    <span><i class="fal fa-clock"></i>17h 11m</span>
                                </div>
                                <div class="course-footer">
                                    <div class="course-teacher"><a href="#" class="text-inherit"><img src="{{asset('front/assets/img/course/course-2-3.png')}}" alt="Course">By Maio Polo</a></div>
                                    <div class="course-review"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>--}}



@endsection
