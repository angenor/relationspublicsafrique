@php
    use \App\Models\WidgetText as WT;
@endphp
@extends('layouts.default')


@section('content')
    <section class="hero-layout1">
        <div class="vs-carousel" data-fade="true" data-arrows="true" data-dots="true">


            @foreach($sliders as $slider)
                <div>
                    <div class="hero-inner">
                        <div class="hero-bg" data-bg-src="{{$slider->img}}"></div>
                        <div class="vs-circle animated"></div>
                        <div class="container">
                            <div class="hero-content">
                                <h1 class="hero-title animated">{!! $slider->name !!}</h1>
{{--                                THE <span>WORLD’S #1 ONLINE</span> EDUCATION--}}
                                <p class="hero-text animated text-white" style="color: white !important">{!!$slider->resume!!}</p>


{{--                                <div class="hero-btns animated">--}}
{{--                                    <a href="#" class="vs-btn style5"><i class="far fa-angle-right"></i>Explore Courses</a>--}}
{{--                                </div>--}}

                            </div>
                        </div>
                    </div>
                </div>

            @endforeach






        </div>
    </section>


    <!--==============================
      Features
  ==============================-->


    <section class=" space-top space-extra-bottom">
        <div class="container">
            <div class="row vs-carousel0 wow fadeInUp" data-wow-delay="0.4s" data-slide-show="3" data-lg-slide-show="2" data-md-slide-show="2" data-sm-slide-show="2" data-center-mode="true" data-xl-center-mode="true" data-ml-center-mode="true">

                <div class="col-sm-6 col-xl-4">
                    <div class="feature-style1">
                        <div class="feature-icon">
                            <img src="{{asset('front/assets/img/icon/feature-icon-1-1.svg')}}" alt="Feature Icon">
                            <div class="vs-circle"></div>
                        </div>
                        <h4 class="feature-title"><a href="{!! WT::getContent('block_annuaire_accueil')->link??'' !!}" class="text-inherit" target="_blank">{!! WT::getContent('block_annuaire_accueil')->name??'' !!}</a></h4>
                        <p class="feature-text">{!! WT::getContent('block_annuaire_accueil')->content??'' !!}</p>
                    </div>
                </div>



                <div class="col-sm-6 col-xl-4">
                    <div class="feature-style1">
                        <div class="feature-icon">
                            <img src="{{asset('front/assets/img/icon/feature-icon-1-2.svg')}}" alt="Feature Icon">
                            <div class="vs-circle"></div>
                        </div>
                        <h4 class="feature-title"><a href="{!! WT::getContent('block_cours_accueil')->link??'' !!}" class="text-inherit">{!! WT::getContent('block_cours_accueil')->name??'' !!}</a></h4>
                        <p class="feature-text">{!! WT::getContent('block_cours_accueil')->content??'' !!}</p>
                    </div>
                </div>




{{--                <div class="col-sm-6 col-xl-4">--}}
{{--                    <div class="feature-style1">--}}
{{--                        <div class="feature-icon">--}}
{{--                            <img src="{{asset('front/assets/img/icon/feature-icon-1-3.svg')}}" alt="Feature Icon">--}}
{{--                            <div class="vs-circle"></div>--}}
{{--                        </div>--}}
{{--                        <h4 class="feature-title"><a href="{!! WT::getContent('block_annuaire_accueil')->link??'' !!}" class="text-inherit" target="_blank">{!! WT::getContent('block_actualité_accueil')->name??'' !!}</a></h4>--}}
{{--                        <p class="feature-text">{!! WT::getContent('block_annuaire_accueil')->content??'' !!}</p>--}}
{{--                    </div>--}}
{{--                </div>--}}

                <div class="col-sm-6 col-xl-4">
                    <div class="feature-style1">
                        <div class="feature-icon">
                            <img src="{{asset('front/assets/img/icon/feature-icon-1-4.svg')}}" alt="Feature Icon">
                            <div class="vs-circle"></div>
                        </div>
                        <h4 class="feature-title"><a href="{!! WT::getContent('block_actualité_accueil')->link??'' !!}" class="text-inherit" target="_blank">{!! WT::getContent('block_actualité_accueil')->name??'' !!}</a></h4>
                        <p class="feature-text">{!! WT::getContent('block_actualité_accueil')->content??'' !!}</p>
                    </div>
                </div>


            </div>
        </div>
    </section><!--==============================
      About Area
  ==============================-->
    <section class="overflow-hidden space-extra-bottom">
        <div class="shape-mockup jump-img d-hd-none d-none d-xxxl-block" data-left="-15%" data-top="2%">
            <div class="vs-border-circle"></div>
        </div>
        <div class="shape-mockup jump-reverse d-none d-xxxl-block" data-right="7%" data-top="38%">
            <div class="shape-dotted"></div>
        </div>
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-xl-9 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="title-area">
                        <div class="sec-icon"><span class="vs-circle"></span></div>
                        <span class="sec-subtitle">BIENVENUE SUR RELATIONS PUBLICS AFRIQUE</span>
                        <h2 class="sec-title h1">Apprendre, Appartenir, S’informer   </h2>
                    </div>
                </div>
            </div>


            <div class="row gx-70">
                <div class="col-lg-7 col-xxl-7">
                    <div class="img-box3">
                        <div class="img-1 mega-hover"><img class="w-100" src="{{$bienvenue->img??''}}" alt="About Img"></div>
                        <div class="shape-dotted jump"></div>
                    </div>
                </div>
                <div class="col-lg-5 col-xxl-4 align-self-center">
                    <p class="fs-md">
                        {!! $bienvenue->content??'' !!}
                    </p>

                    <div class="media-style1">
                        <div class="media-img">
                            <img src="{{WT::getContent('block_partenaire_photo80x80')->img}}" alt="About Author" style="width: 80px ;height: 80px">
                        </div>
                        <div class="media-body">
                            <span class="media-label">{!! WT::getContent('block_partenaire_photo80x80')->name??'' !!}</span>
                            <p class="media-info">{!! WT::getContent('block_partenaire_photo80x80')->content??'' !!}</p>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </section>



    {{--    <section class=" space-bottom">
            <div class="container">
                <div class="row align-items-center justify-content-center justify-content-xl-between flex-row-reverse">
                    <div class="col-xl-5 col-xxl-auto wow fadeInUp" data-wow-delay="0.3s">
                        <div class="img-box1">
                            <div class="vs-circle">
                                <div class="mega-hover">
                                    <img src="{{asset('front/assets/img/about/about-1-1.png')}}" alt="banner">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-7 col-xxl text-center text-xl-start">
                        <form action="#" class="form-style1">
                            <h2 class="form-title h1">Search Your Program</h2>
                            <div class="row">
                                <div class="form-group col-auto">
                                    <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
                                    <label for="inlineRadio1">Undergraduate Programs</label>
                                </div>
                                <div class="form-group col-auto">
                                    <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2" checked>
                                    <label for="inlineRadio2">Graduate Programs</label>
                                </div>
                                <div class="form-group col-12">
                                    <div class="form-inner">
                                        <input type="text" placeholder="Enter your email address...">
                                        <button class="icon-btn"><i class="fal fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="row gx-40 justify-content-center justify-content-xl-start">
                            <div class="col-auto">
                                <div class="media-style3">
                                    <div class="media-icon"><i class="far fa-graduation-cap"></i></div>
                                    <div class="media-body">
                                        <span class="media-title">8,000 online courses</span>
                                        <p class="media-text">Explore a variety of fresh topics</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="media-style3">
                                    <div class="media-icon"><i class="fas fa-user-tie"></i></div>
                                    <div class="media-body">
                                        <span class="media-title">Expert Instruction</span>
                                        <p class="media-text">Find the right instructor for you</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="space-top space-extra-bottom" data-bg-src="{{asset('front/assets/img/bg/course-bg-pattern.jpg')}}">
            <div class="container-lg">
                <div class="title-area text-center wow fadeInUp" data-wow-delay="0.3s">
                    <div class="sec-icon">
                        <div class="vs-circle"></div>
                    </div>
                    <span class="sec-subtitle">WELCOME TO GLOBAL EDUCATION</span>
                    <h2 class="sec-title">Explore Courses</h2>
                </div>
                <div class="row vs-carousel wow fadeInUp" data-wow-delay="0.4s" data-slide-show="3" data-lg-slide-show="2" data-md-slide-show="2" data-sm-slide-show="2" data-center-mode="true" data-dots="true">
                    <div class="col-sm-6 col-xl-4">
                        <div class="course-style1">
                            <div class="course-img">
                                <a href="#"><img class="w-100" src="{{asset('front/assets/img/course/course-1-1.png')}}" alt="Course Img"></a>
                                <div class="course-category"><a href="#">Technology</a></div>
                                <a href="https://www.youtube.com/watch?v=_sI_Ps7JSEk" class="vs-btn style2 popup-video"><i class="fas fa-play"></i>Preview Course</a>
                            </div>
                            <div class="course-content">
                                <div class="course-top">
                                    <div class="course-review"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>(5.0)</div>
                                    <span class="course-price">$778</span>
                                </div>
                                <h3 class="h5 course-name"><a href="#">Advance Begineer's Goal & Managing Course</a></h3>
                                <div class="course-teacher"><a href="#" class="text-inherit">By Rose Marry</a></div>
                            </div>
                            <div class="course-meta">
                                <span><i class="fal fa-users"></i>111 Students</span>
                                <span><i class="fal fa-clock"></i>5h 11m</span>
                                <span><i class="fal fa-calendar-alt"></i>10 Augest 2023</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <div class="course-style1">
                            <div class="course-img">
                                <a href="#"><img class="w-100" src="{{asset('front/assets/img/course/course-1-2.png')}}" alt="Course Img"></a>
                                <div class="course-category"><a href="#">Management</a></div>
                                <a href="https://www.youtube.com/watch?v=_sI_Ps7JSEk" class="vs-btn style2 popup-video"><i class="fas fa-play"></i>Preview Course</a>
                            </div>
                            <div class="course-content">
                                <div class="course-top">
                                    <div class="course-review"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>(5.0)</div>
                                    <span class="course-price">$499</span>
                                </div>
                                <h3 class="h5 course-name"><a href="#">Advance Technology & Architecture Course</a></h3>
                                <div class="course-teacher"><a href="#" class="text-inherit">By Jorzy Lamot</a></div>
                            </div>
                            <div class="course-meta">
                                <span><i class="fal fa-users"></i>115 Students</span>
                                <span><i class="fal fa-clock"></i>1h 24m</span>
                                <span><i class="fal fa-calendar-alt"></i>22 April 2023</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <div class="course-style1">
                            <div class="course-img">
                                <a href="#"><img class="w-100" src="{{asset('front/assets/img/course/course-1-3.png')}}" alt="Course Img"></a>
                                <div class="course-category"><a href="#">Chemistry</a></div>
                                <a href="https://www.youtube.com/watch?v=_sI_Ps7JSEk" class="vs-btn style2 popup-video"><i class="fas fa-play"></i>Preview Course</a>
                            </div>
                            <div class="course-content">
                                <div class="course-top">
                                    <div class="course-review"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>(5.0)</div>
                                    <span class="course-price">$556</span>
                                </div>
                                <h3 class="h5 course-name"><a href="#">Basic Chemistry Programs on Lab</a></h3>
                                <div class="course-teacher"><a href="#" class="text-inherit">By Jon Watson</a></div>
                            </div>
                            <div class="course-meta">
                                <span><i class="fal fa-users"></i>778 Students</span>
                                <span><i class="fal fa-clock"></i>4h 05m</span>
                                <span><i class="fal fa-calendar-alt"></i>19 March 2023</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4">
                        <div class="course-style1">
                            <div class="course-img">
                                <a href="#"><img class="w-100" src="{{asset('front/assets/img/course/course-1-4.png')}}" alt="Course Img"></a>
                                <div class="course-category"><a href="#">Busniess</a></div>
                                <a href="https://www.youtube.com/watch?v=_sI_Ps7JSEk" class="vs-btn style2 popup-video"><i class="fas fa-play"></i>Preview Course</a>
                            </div>
                            <div class="course-content">
                                <div class="course-top">
                                    <div class="course-review"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>(5.0)</div>
                                    <span class="course-price">$199</span>
                                </div>
                                <h3 class="h5 course-name"><a href="#">Graduation For Engineering & Architect</a></h3>
                                <div class="course-teacher"><a href="#" class="text-inherit">By Ana Watson</a></div>
                            </div>
                            <div class="course-meta">
                                <span><i class="fal fa-users"></i>204 Students</span>
                                <span><i class="fal fa-clock"></i>2h 11m</span>
                                <span><i class="fal fa-calendar-alt"></i>11 March 2023</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>--}}

    <!--==============================
  Call To Action
  ==============================-->


{{--
    <section class="  " data-bg-src="{{asset('front/assets/img/bg/divider-bg-1-1.jpg')}}">
        <div class="container">
            <div class="row align-items-center text-center text-lg-start">
                <div class="col-lg-5 col-xl-6 space-extra">
                    <h2 class="sec-title text-white mb-3">Expert instruction</h2>
                    <p class="fs-md text-white">Find the right instructor for you from over 10,000 teachers</p>
                    <div class="row gx-60 mb-4 pb-xl-3 text-start justify-content-center justify-content-lg-start">
                        <div class="col-auto col-lg-12 col-xl-auto">
                            <div class="list-style4 vs-list ">
                                <ul class="list-unstyled m-0">
                                    <li>Hand-picked authors</li>
                                    <li>Easy to follow curriculum</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-auto col-lg-12 col-xl-auto">
                            <div class="list-style4 vs-list ">
                                <ul class="list-unstyled m-0">
                                    <li>Free courses</li>
                                    <li>Money-back guarantee</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="vs-btn style5"><i class="far fa-angle-right"></i>Find Our Teachers</a>
                </div>
                <div class="col-lg-7 col-xl-6 align-self-end">
                    <div class="img-box2">
                        <div class="vs-circle"></div>
                        <img class="img-1" src="{{asset('front/assets/img/about/about-1-2.png')}}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>


    --}}

    <!--==============================
      Team Area
  ==============================-->

{{--
    <section class="space-top space-extra-bottom">
        <div class="container">
            <div class="title-area text-center wow fadeInUp" data-wow-delay="0.3s">
                <div class="sec-icon">
                    <div class="vs-circle"></div>
                </div>
                <span class="sec-subtitle">IDEAL TUTOR FOR EVERYONE</span>
                <h2 class="sec-title h1">Qualified Teachers</h2>
            </div>
            <div class="row vs-carousel wow fadeInUp gx-40" data-wow-delay="0.4s" data-slide-show="3" data-lg-slide-show="2" data-md-slide-show="2" data-sm-slide-show="2" data-center-mode="true">
                <div class="col-sm-6 col-lg-4">
                    <div class="team-style1">
                        <div class="team-img">
                            <img class="w-100" src="{{asset('front/assets/img/team/team-1-1.jpg')}}" alt="teacher">
                        </div>
                        <div class="team-content">
                            <div class="team-review"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            <h4 class="team-name"><a href="#">Thomas Walimes</a></h4>
                            <p class="team-degi">Science Tutor</p>
                            <div class="team-social">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="team-style1">
                        <div class="team-img">
                            <img class="w-100" src="{{asset('front/assets/img/team/team-1-2.jpg')}}" alt="teacher">
                        </div>
                        <div class="team-content">
                            <div class="team-review"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            <h4 class="team-name"><a href="#">Nohima Homminu</a></h4>
                            <p class="team-degi">English Tutor</p>
                            <div class="team-social">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="team-style1">
                        <div class="team-img">
                            <img class="w-100" src="{{asset('front/assets/img/team/team-1-3.jpg')}}" alt="teacher">
                        </div>
                        <div class="team-content">
                            <div class="team-review"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            <h4 class="team-name"><a href="#">Kaylin Moore</a></h4>
                            <p class="team-degi">Math Tutor</p>
                            <div class="team-social">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="team-style1">
                        <div class="team-img">
                            <img class="w-100" src="{{asset('front/assets/img/team/team-1-4.jpg')}}" alt="teacher">
                        </div>
                        <div class="team-content">
                            <div class="team-review"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            <h4 class="team-name"><a href="#">Kaylin Moore</a></h4>
                            <p class="team-degi">History Tutor</p>
                            <div class="team-social">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    --}}
    <!--==============================
      Brand Area
  ==============================-->
{{--    <div class="space-bottom">
        <div class="container text-center">
            <span class="sec-subtitle2">You can list your <b><u>partners or instructor's</u></b> brands here!</span>
            <div class="row vs-carousel wow fadeInUp" data-wow-delay="0.4s" data-slide-show="5" data-lg-slide-show="4" data-md-slide-show="3" data-sm-slide-show="2">
                <div class="col-auto"><img src="{{asset('front/assets/img/brand/brand-1-1.png')}}" alt="brand"></div>
                <div class="col-auto"><img src="{{asset('front/assets/img/brand/brand-1-2.png')}}" alt="brand"></div>
                <div class="col-auto"><img src="{{asset('front/assets/img/brand/brand-1-3.png')}}" alt="brand"></div>
                <div class="col-auto"><img src="{{asset('front/assets/img/brand/brand-1-4.png')}}" alt="brand"></div>
                <div class="col-auto"><img src="{{asset('front/assets/img/brand/brand-1-5.png')}}" alt="brand"></div>
                <div class="col-auto"><img src="{{asset('front/assets/img/brand/brand-1-1.png')}}" alt="brand"></div>
            </div>
        </div>
    </div>

    --}}

    <!--==============================
      Upcoming Events Area
  ==============================-->



    <section class="space-top space-extra-bottom" data-bg-src="front/assets/img/bg/course-bg-pattern.jpg">
        <div class="container-lg">
            <div class="title-area text-center wow fadeInUp" data-wow-delay="0.3s">
                <div class="sec-icon">
                    <div class="vs-circle"></div>
                </div>
                <span class="sec-subtitle">à venir</span>
                <h2 class="sec-title">CONFÉRENCES ET ÉVÉNEMENTS</h2>
            </div>
            <div class="row vs-carousel wow fadeInUp" data-wow-delay="0.4s" data-slide-show="3" data-lg-slide-show="2" data-md-slide-show="2" data-sm-slide-show="2" data-center-mode="true" data-dots="true">

                @foreach($evenements  as $evenement)
                <div class="col-sm-6 col-xl-4">
                    <div class="course-style1">
                        <div class="course-img">
                            <a href="{{$evenement->link}}"><img class="w-100" src="{{$evenement->img}}" alt="Course Img" style="height: 350px; object-fit: cover"></a>
                            <div class="course-category"><a href="#">Technology</a></div>

                        </div>
                        <div class="course-content">

                            <h3 class="h6 course-name"><a href="{{$evenement->link}}">{{$evenement->name}}</a></h3>
                            <div class="course-teacher"><a href="#" class="text-inherit">{{$evenement->user->name}}</a></div>
                        </div>
                        <div class="course-meta">
{{--                            <span><i class="fal fa-users"></i>111 Students</span>--}}
{{--                            <span><i class="fal fa-clock"></i>5h 11m</span>--}}
                            <span><i class="fal fa-calendar-alt"></i> {{$evenement->created_at->format('d/m/Y H:m')}} </span>
                        </div>
                    </div>
                </div>

            @endforeach



            </div>

            <div class="text-center mb-30">
                <a href="{{route('blog',['type'=>'evenements'])}}" class="vs-btn style3 mt-2">
                    <i class="far fa-angle-right"></i>
                    <span class="d-none d-lg-inline"> Voir plus</span>
                </a>
            </div>
        </div>
    </section>

{{--
    <section class="overflow-hidden space-top space-extra-bottom">
        <div class="event-shape1"></div>
        <div class="shape-mockup jump d-none d-xxl-block" data-bottom="26%" data-right="-270px">
            <div class="vs-border-circle"></div>
        </div>
        <div class="container">
            <div class="row gx-80">
                @php
                     $dateEvent1 = $evenements[0]->created_at->format('d/m/Y H:m')??'' ;

//                    dd($dateEvent1,$dateEvent2) ;
               @endphp
                @isset($evenements[0]->name)
                    <div class="col-lg-6 col-xxl-5 pb-3 pb-lg-0 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="picture-box1">
                            <div class="picture-1 mega-hover"><img class="w-100" src="{{$evenements[0]->img??''}}" alt="picture" style="height: 600px;object-fit: cover;"></div>
                            <div class="countdown-style1">
                                <span class="countdown-title font-bold">{{$evenements[0]->name??''}}</span>
--}}{{--                                <ul class="countdown-active" data-end-date="{{$dateEvent2}}">--}}{{--
--}}{{--                                <ul class="countdown-active" data-end-date="01/12/2024">--}}{{--
--}}{{--                                    <li><span class="day"></span>d</li>--}}{{--
--}}{{--                                    <li><span class="hour"></span>h</li>--}}{{--
--}}{{--                                    <li><span class="minute"></span>m</li>--}}{{--
--}}{{--                                    <li><span class="seconds"></span>S</li>--}}{{--
--}}{{--                                </ul>--}}{{--

--}}{{--                              <ul class="countdown-active">  <strong> {!! $dateEvent1 !!}</strong></ul>--}}{{--
--}}{{--                                <a href="{{$evenements[0]->link}}" class="link-btn">Découvrir <i class="fas fa-long-arrow-right"></i></a>--}}{{--
                            </div>
                        </div>
                    </div>
                @endif





                <div class="col-lg-6 col-xxl-7 align-self-center wow fadeInUp" data-wow-delay="0.2s">
                    <div class="title-area mb-40 text-center text-md-start">
                        <span class="sec-subtitle text-white">CONFÉRENCES EN LIGNE ET ÉVÉNEMENTS</span>
                        <h2 class="sec-title h1 text-white">à venir</h2>
                    </div>


                    @foreach($evenements->slice(1) as $evenement)

                        <div class="event-style1">
                            <div class="event-date">
                                <span class="day">{{$evenement->created_at->format('d' )??''}}</span><span class="month">{{$evenement->created_at->format('m/Y' )??''}}</span></div>
                            <div class="event-body">
                                <h4 class="event-title"><a href="#" class="text-reset">{{$evenement->name}}</a></h4>
                                <div class="event-meta">
--}}{{--                                    <span><i class="fas fa-clock"></i>  {{$evenement->created_at->format('H:i')}} - {{$evenement->created_at->format('H:i')}}  </span>--}}{{--
--}}{{--                                    <span><i class="fas fa-clock"></i>   8:00 AM - 5:00 PM  </span>--}}{{--
                                    <span><i class="far fa-map"></i>Online</span>
                                    <span><i class="far fa-user"></i>{!! $evenement->speakers !!} Speaker</span>
                                </div>
                            </div>
                        </div>
                    @endforeach


                </div>
            </div>
        </div>
    </section>

    --}}
    <!--==============================
      Blog Area
  ==============================-->
    <section class="space-top space-extra-bottom">
        <div class="container">
            <div class="title-area text-center wow fadeInUp" data-wow-delay="0.3s">
                <div class="sec-icon">
                    <div class="vs-circle"></div>
                </div>
                <span class="sec-subtitle">Actualités</span>
                <h2 class="sec-title">Actualités & Articles</h2>
            </div>
            <div class="row vs-carousel" data-slide-show="2" data-md-slide-show="2">

                @foreach($blogs as $blog)
                    <div class="col-lg-6">
                        <div class="vs-blog blog-style1">
                            <div class="blog-img">
                                <a href="{!! $blog->link !!}">
                                    <img class="w-100" src="{{$blog->img}}" alt="{!! $blog->title !!}" style="border: 1px solid #74818a">
                                </a>

                            </div>
                            <div class="blog-content">
                               {{-- <div class="date-box">
                                    <span class="day">{{$blog->created_at->format('d')}}</span>
                                    <span class="month">{{$blog->created_at->format('M')}}</span>
                                    <span class="post-comment">0 commentaires</span>
                                </div>--}}
                                <h4 class="blog-title"><a href="{!! $blog->link !!}">{!! $blog->title !!}</a></h4>
                                <p>{!! strip_tags($blog->resume,'') !!}</p>
                            </div>
                        </div>
                    </div>
                @endforeach



            </div>
            <div class="text-center mb-30">
                <a href="{{route('blog')}}" class="vs-btn style3 mt-2"><i class="far fa-angle-right"></i>Consulter tous les articles</a>
            </div>
        </div>
    </section><!--==============================
    CTA Area
  ==============================-->
{{--    <section class="space-top space-extra-bottom" data-bg-src="{{asset('front/assets/img/bg/blog-single-divider-bg-1-1.jpg')}}">
        <div class="container">
            <div class="row justify-content-between text-center text-lg-start">
                <div class="col-lg-6 mb-40 mb-lg-0">
                    <h2 class="mt-n2 h2 mb-3">Future Learn’s Purpose is to transform access to education.</h2>
                    <p class=" mb-4 pb-2 fs-md col-xl-11">Sign up to our newsletter and we'll send fresh new courses and special offers direct to your inbox, once a week.</p>
                    <a href="#" class="vs-btn style2"><i class="far fa-angle-right"></i>Get a Quote</a>
                </div>
                <div class="col-auto d-none d-lg-block">
                    <div class="sec-line2"></div>
                </div>
                <div class="col-lg-auto">
                    <h6 class="mt-n1">Academic Leadership Team</h6>
                    <div class="mini-avater">
                        <a href="#"><img src="{{asset('front/assets/img/team/team-s-1-1.png')}}" alt="avater"></a>
                        <a href="#"><img src="{{asset('front/assets/img/team/team-s-1-2.png')}}" alt="avater"></a>
                        <a href="#"><img src="{{asset('front/assets/img/team/team-s-1-3.png')}}" alt="avater"></a>
                        <a href="#"><img src="{{asset('front/assets/img/team/team-s-1-4.png')}}" alt="avater"></a>
                    </div>
                </div>
            </div>
        </div>
    </section>--}}

@endsection



@section('title')
     Bienvenue sur Relations Publics Afrique
@endsection

@section('js')

@endsection


@section('css')

@endsection


