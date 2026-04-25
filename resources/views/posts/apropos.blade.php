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

    <section class="space-top">
        <div class="container">
            <div class="row">
                <div class="col-xl-9">
                    <div class="title-area mb-3 mb-xl-5">
                        <span class="sec-subtitle">Welcome to global education</span>
                        <h2 class="sec-title">Teach online and connect with students all over the world</h2>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4 mb-30 mb-xl-0">
                    <p class="fs-md mt-n1">Ducamb welcomed every pain avoided but in certain circumstances owing to the claims of igation that off business it will frequently occu the obligations of business it will frequently of curs that pleasures.</p>
                    <div class="media-style1">
                        <div class="media-img"><img src="{{asset('front/assets/img/about/author-1-1.png')}}" alt="About Author"></div>
                        <div class="media-body">
                            <span class="media-label">Thomas Walkar</span>
                            <p class="media-info">President, Director</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="list-style1 vs-list ">
                        <ul>
                            <li>Steady stream of new students</li>
                            <li>Smart calendar</li>
                            <li>Interactive classroom</li>
                            <li>Convenient payment methods</li>
                            <li>Professional development webinars</li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-4 mt-n5 pt-5 pt-xl-0">
                    <div class="img-box3">
                        <div class="img-1 mega-hover"><img class="w-100" src="{{asset('front/assets/img/about/about-s-1.png')}}" alt="About Img"></div>
                        <div class="shape-dotted jump"></div>
                    </div>
                </div>
            </div>
        </div>
    </section><!--==============================
  Features Area
  ==============================-->
    <section class=" space-top space-extra-bottom">
        <div class="container">
            <div class="row vs-carousel" data-slide-show="4" data-md-slide-show="3" data-sm-slide-show="2" data-xs-slide-show="2">
                <div class="col-sm-6 col-lg-3">
                    <div class="media-style8">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/about-icon-1.svg')}}" alt=""></div>
                        <h5 class="media-title">80+ Centres</h5>
                        <p class="media-text">With over 80 local training centres in the world.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="media-style8">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/about-icon-2.svg')}}" alt=""></div>
                        <h5 class="media-title">1M+ Courses</h5>
                        <p class="media-text">Our unique training, based on practical activity.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="media-style8">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/about-icon-3.svg')}}" alt=""></div>
                        <h5 class="media-title">Expérience</h5>
                        <p class="media-text">Notre expertise et notre expérience dans le domaine.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="media-style8">
                        <div class="media-icon"><img src="{{asset('front/assets/img/icon/about-icon-4.svg')}}" alt=""></div>
                        <h5 class="media-title">65K Students</h5>
                        <p class="media-text">Our heritage and longevity as the leading.</p>
                    </div>
                </div>
            </div>
        </div>
    </section><!--==============================
      Team Area
  ==============================-->
    <section class="space-top space-extra-bottom bg-smoke">
        <div class="container">
            <div class="title-area text-center">
                <div class="sec-icon">
                    <div class="vs-circle"></div>
                </div>
                <span class="sec-subtitle">IDEAL TUTOR FOR EVERYONE</span>
                <h2 class="sec-title h1">Qualified Online Tutors</h2>
            </div>
            <div class="row vs-carousel" data-slide-show="4" data-lg-slide-show="3" data-md-slide-show="2" data-sm-slide-show="2" data-center-mode="true">
                <div class="col-sm-6 col-xl-3">
                    <div class="team-style2">
                        <div class="team-content">
                            <h4 class="team-name h5"><a href="#">Moniqa Romin</a></h4>
                            <p class="team-degi">Maths & Physics Tutor</p>
                            <span class="team-courses">100+ Lessons Completed</span>
                            <div class="team-img">
                                <img src="{{asset('front/assets/img/team/team-s-3-1.png')}}" alt="member">
                            </div>
                            <span class="team-rate"><span class="team-price">$63 </span>/ per hour</span>
                            <p class="team-experi">2+ years experience</p>
                            <div class="team-review"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="team-style2">
                        <div class="team-content">
                            <h4 class="team-name h5"><a href="#">Marry Chain</a></h4>
                            <p class="team-degi">Economics Professor</p>
                            <span class="team-courses">633+ Lessons Completed</span>
                            <div class="team-img">
                                <img src="{{asset('front/assets/img/team/team-s-3-2.png')}}" alt="member">
                            </div>
                            <span class="team-rate"><span class="team-price">$45 </span>/ per hour</span>
                            <p class="team-experi">8+ years experience</p>
                            <div class="team-review"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="team-style2">
                        <div class="team-content">
                            <h4 class="team-name h5"><a href="#">Alice Heard</a></h4>
                            <p class="team-degi">Statistics Professor</p>
                            <span class="team-courses">755+ Lessons Completed</span>
                            <div class="team-img">
                                <img src="{{asset('front/assets/img/team/team-s-3-3.png')}}" alt="member">
                            </div>
                            <span class="team-rate"><span class="team-price">$99 </span>/ per hour</span>
                            <p class="team-experi">7+ years experience</p>
                            <div class="team-review"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="team-style2">
                        <div class="team-content">
                            <h4 class="team-name h5"><a href="#">Jenny White</a></h4>
                            <p class="team-degi">Art Professor</p>
                            <span class="team-courses">336+ Lessons Completed</span>
                            <div class="team-img">
                                <img src="{{asset('front/assets/img/team/team-s-3-4.png')}}" alt="member">
                            </div>
                            <span class="team-rate"><span class="team-price">$26 </span>/ per hour</span>
                            <p class="team-experi">8+ years experience</p>
                            <div class="team-review"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="team-style2">
                        <div class="team-content">
                            <h4 class="team-name h5"><a href="#">William Smith</a></h4>
                            <p class="team-degi">Science Professor</p>
                            <span class="team-courses">220+ Lessons Completed</span>
                            <div class="team-img">
                                <img src="{{asset('front/assets/img/team/team-s-3-5.png')}}" alt="member">
                            </div>
                            <span class="team-rate"><span class="team-price">$22 </span>/ per hour</span>
                            <p class="team-experi">6+ years experience</p>
                            <div class="team-review"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="team-style2">
                        <div class="team-content">
                            <h4 class="team-name h5"><a href="#">George Hobbs</a></h4>
                            <p class="team-degi">Maths & Physics Tutor</p>
                            <span class="team-courses">778+ Lessons Completed</span>
                            <div class="team-img">
                                <img src="{{asset('front/assets/img/team/team-s-3-6.png')}}" alt="member">
                            </div>
                            <span class="team-rate"><span class="team-price">$18 </span>/ per hour</span>
                            <p class="team-experi">7+ years experience</p>
                            <div class="team-review"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!--==============================
  Call To Action
  ==============================-->
    <section class="  " data-bg-src="assets/img/bg/divider-bg-1-1.jpg">
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
    </section><!--==============================
  Features Area
  ==============================-->
    <section class="space-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-7 text-center text-xl-start">
                    <div class="title-area">
                        <span class="sec-subtitle">TRAINING AND LEADERSHIP PROGRAMME</span>
                        <h2 class="sec-title h1">Training Programme</h2>
                    </div>
                    <div class="row gx-80 gy-xl-4 mb-4 mb-xl-0">
                        <div class="col-md-6 col-xl-6 wow fadeInUp" data-wow-delay="0.2s">
                            <div class="media-style4">
                                <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-1.svg')}}" alt=""></div>
                                <h5 class="media-title">Interactive Lessons</h5>
                                <p>Ducamb welcomed every pain mstances owing to the claims of will frequently.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-6 wow fadeInUp" data-wow-delay="0.3s">
                            <div class="media-style4">
                                <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-2.svg')}}" alt=""></div>
                                <h5 class="media-title">Free First Lesson</h5>
                                <p>Ducamb welcomed every pain mstances owing to the claims of will frequently.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-6 wow fadeInUp" data-wow-delay="0.3s">
                            <div class="media-style4">
                                <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-3.svg')}}" alt=""></div>
                                <h5 class="media-title">Trained & Experienced</h5>
                                <p>Ducamb welcomed every pain mstances owing to the claims of will frequently.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-6 wow fadeInUp" data-wow-delay="0.4s">
                            <div class="media-style4">
                                <div class="media-icon"><img src="{{asset('front/assets/img/icon/training-icon-1-4.svg')}}" alt=""></div>
                                <h5 class="media-title">Question, Quiz & Course</h5>
                                <p>Ducamb welcomed every pain mstances owing to the claims of will frequently.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="position-relative">
                        <form action="#" class="form-style2">
                            <div class="form-inner">
                                <h3 class="form-title h5">Join over <span class="text-theme">50,000 students</span> who’ve now registered for their courses. Don’t miss out.</h3>
                                <div class="row">
                                    <div class="col-md-6 col-xl-12">
                                        <div class="form-group">
                                            <input type="text" name="name" id="name" placeholder="Complate Name">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-12">
                                        <div class="form-group">
                                            <input type="text" name="email" id="email" placeholder="Email Address">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-12">
                                        <div class="form-group">
                                            <select name="coursenames" id="coursenames">
                                                <option selected disabled hidden>Select Course</option>
                                                <option>Development</option>
                                                <option>Ui Development</option>
                                                <option>CMS Learning</option>
                                                <option>Jamstack Master</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-12">
                                        <div class="form-group">
                                            <input type="text" name="email" id="phone" placeholder="Phone No">
                                        </div>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button type="button" class="vs-btn">Apply Today</button>
                                        <a class="form-link" href="#">Frequently Asked Questions</a>
                                    </div>
                                </div>
                            </div>
                            <div class="vs-circle color2"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section><!--==============================
      Testimonial Area
  ==============================-->
    <section class="overflow-hidden space-top space-extra-bottom">
        <div class="container">
            <div class="title-area text-center wow fadeInUp" data-wow-delay="0.3s">
                <div class="sec-icon">
                    <div class="vs-circle"></div>
                </div>
                <span class="sec-subtitle">WHAT PEOPLE SAY</span>
                <h2 class="sec-title h1">Our Students Words</h2>
            </div>
            <div class="row vs-carousel" data-dots="true" data-slide-show="3" data-lg-slide-show="2" data-md-slide-show="2" data-sm-slide-show="1">
                <div class="col-sm-6 col-xl-4">
                    <div class="testi-style1">
                        <div class="testi-content">
                            <p class="testi-text">“ Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam hendrerit nisi sed sollicitudin pellentesque. Nunc posuere purus rhoncus pulvinar ”</p>
                        </div>
                        <div class="testi-client">
                            <img src="{{asset('front/assets/img/testimonial/testimonial-2-1.png')}}" alt="author">
                            <h3 class="testi-name h5">Jessica Moniqa</h3>
                            <span class="testi-degi">B.Tech-Civil , 2017-2022</span>
                            <div class="testi-rating"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="testi-style1">
                        <div class="testi-content">
                            <p class="testi-text">“ I had the opportunity to meet with the dynamic & distinguishe faculties, who are highly qualified & friendly patients. With their assist & guidance I… ”</p>
                        </div>
                        <div class="testi-client">
                            <img src="{{asset('front/assets/img/testimonial/testimonial-2-2.png')}}" alt="author">
                            <h3 class="testi-name h5">Willimes Parkar</h3>
                            <span class="testi-degi">CSE Computer , 2017-2022</span>
                            <div class="testi-rating"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="testi-style1">
                        <div class="testi-content">
                            <p class="testi-text">“ So how did the classical Latin become so incoherent? According to McClintock, a 15th century typesetter likely scrambled part of Cicero's ”</p>
                        </div>
                        <div class="testi-client">
                            <img src="{{asset('front/assets/img/testimonial/testimonial-2-3.png')}}" alt="author">
                            <h3 class="testi-name h5">Silverster Scott</h3>
                            <span class="testi-degi">DBBL Pvt Ltd, 2011-2015</span>
                            <div class="testi-rating"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="testi-style1">
                        <div class="testi-content">
                            <p class="testi-text">“ One brave soul did take a stab at translating the almost-not-quite-Latin. According to The Guardian, Jaspreet Singh Boparai undertook the challenge ”</p>
                        </div>
                        <div class="testi-client">
                            <img src="{{asset('front/assets/img/testimonial/testimonial-2-4.png')}}" alt="author">
                            <h3 class="testi-name h5">Marry Rose</h3>
                            <span class="testi-degi">Moto Build LTD, 2015-2018</span>
                            <div class="testi-rating"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> <!--==============================
      Brand Area
  ==============================-->
    <div class="space-bottom">
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
    <!--==============================
    Footer Area
  ==============================-->
@endsection

@section('title')
    A propos
@endsection

