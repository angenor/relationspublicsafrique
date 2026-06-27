@php use \App\Models\WidgetText as WT; @endphp
    <!doctype html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Relations Publics Afrique |  @yield('title',config('app.name', 'BIENVENUE'))</title>

    <meta name="description" content="Relations Publics Afrique.">
    <meta name="keywords" content="Relations publiques, Afrique">
    <meta name="robots" content="index, follow">
    <meta charset="utf-8">
    <link rel="canonical" href="https://www.relationspublicsafrique.org/">


    <meta property="og:title" content="Relations Publiques Afrique |  @yield('title',config('app.name', 'BIENVENUE')) ">
    <meta property="og:description" content="Relations Publiques Afrique |">
    <meta property="og:url" content="{{ asset('logos/faveicon.png') }}">
    <meta property="og:image" content="{{ asset('logos/logo1.png') }}">

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicons - Place favicon.ico in the root directory -->
    <link rel="shortcut icon" href="{{asset('logos/faveicon.png ')}}" type="image/x-icon">
    <link rel="icon" href="{{asset('logos/faveicon.png ')}}" type="image/x-icon">

    @yield('meta')
    <!--==============================
	  Google Fonts
	============================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">


    <!--==============================
	    All CSS File
	============================== -->
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{asset('front/assets/css/bootstrap.min.css')}}">
    {{--    <!-- <link rel="stylesheet" {{asset('front/href="assets')}}/css/app.min.css"> -->--}}
    <!-- Fontawesome Icon -->
    <link rel="stylesheet" href="{{asset('front/assets/css/fontawesome.min.css')}}">
    <!-- Magnific Popup -->
    <link rel="stylesheet" href="{{asset('front/assets/css/magnific-popup.min.css')}}">
    <!-- Slick Slider -->
    <link rel="stylesheet" href="{{asset('front/assets/css/slick.min.css')}}">
    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="{{asset('front/assets/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('front/assets/css/mystyle.css')}}">
    <link rel="stylesheet" href="{{asset('front/assets/css/events-custom.css')}}">

    @livewireStyles

    @yield('jss')

        @vite(['resources/css2/app.css', 'resources/js2/app.js'])

    <!-- Variables de thème personnalisées -->
    <link rel="stylesheet" href="{{ asset('front/assets/css/theme-variables.css') }}">

    @yield('css')

    @php
        $routename = request()->route()->getName() ;

    @endphp

</head>


<body>
{{--{{\App\Models\WidgetText::getContent('tel1')->name}}--}}
<div class="" id="app2">

    <!--[if lte IE 9]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a
        href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
    <![endif]-->


    <!--********************************
           Code Start From Here
    ******************************** -->


    <!--==============================
     Preloader
    ==============================-->
@persist('loader')
    <div class="preloader">
        <button class="vs-btn preloaderCls"> Annuler le préchargement</button>
        <div class="preloader-inner">
            <div class="loader"></div>
        </div>
    </div>
@endpersist

    <!--==============================
    Mobile Menu
  ============================== -->
    <div class="vs-menu-wrapper">
        <div class="vs-menu-area text-center">
            <button class="vs-menu-toggle"><i class="fal fa-times"></i></button>
            <div class="mobile-logo">
                <a href="/" wire:navigate><img src="{{asset('logos/faveicon.png ')}}" alt="cestlacom" style="max-width: 150px;"></a>
            </div>
            <div class="vs-mobile-menu">
                <ul>

                <li class=""> <a href="{{route('events.index')}}" wire:navigate>Lomé COM’ TOUR</a> </li>
                <li class=""> <a href="{{ route('events.index') }}" wire:navigate>Événements</a> </li>

                     <li class="menu-item-has-children">
                        <a href="#" wire:navigate>Nous</a>
                        <ul class="sub-menu">
                            <li><a href="{{route('nous.mission')}}" wire:navigate>Mission</a></li>
                            <li><a href="{{route('nous.vision')}}" wire:navigate>Vision</a></li>
                            <li><a href="{{route('nous.historique')}}" wire:navigate>Historique</a></li>
                            <li><a href="{{ route('projets.index') }}" wire:navigate>Nos projets</a></li>
                        </ul>
                    </li>
                    <li class="menu-item-has-children">
                        <a href="{{route('blog')}}" wire:navigate>Actualité</a>
                        <ul class="sub-menu">
                            <li><a href="{{route('blog')}}" wire:navigate>Articles</a></li>
                            <li><a href="#" wire:navigate>Comptes rendus</a></li>
                            <li><a href="{{ route('media.home') }}" wire:navigate>Média</a></li>
                        </ul>
                    </li>
                     <li class="menu-item-has-children">
                        <a href="{{route('annuaire.consulter')}}" wire:navigate>Annuaire</a>
                        <ul class="sub-menu">
                            <li><a href="{{route('annuaire.consulter')}}" wire:navigate>Consulter</a></li>
                            <li><a href="{{route('annuaire.rejoindre')}}" wire:navigate>Rejoindre</a></li>
                            <li><a href="{{route('annuaire.suggerer')}}" wire:navigate>Suggérer une adhésion</a></li>
                        </ul>
                    </li>

                    <li><a href="{{route('appartenir')}}" wire:navigate>Appartenir</a></li>
                    <li><a href="/formations" wire:navigate>Formation</a></li>
                    <li><a href="{{route('contact')}}" wire:navigate> Contact</a></li>

                </ul>
            </div>
        </div>
    </div><!--==============================
    Popup Search Box
    ============================== -->
    <div class="popup-search-box d-none d-lg-block  ">
        <button class="searchClose"><i class="fal fa-times"></i></button>
        <form action="#">
            <input type="text" class="border-theme" placeholder="What are you looking for">
            <button type="submit"><i class="fal fa-search"></i></button>
        </form>
    </div>
    <!--==============================
    Header Area
    ==============================-->
    <header class="vs-header header-layout1">
        <div class="header-top">
            <div class="container">
                <div class="row justify-content-between align-items-center gx-50">
                    <div class="col d-none d-xl-block">
                        <div class="header-links">
                            <ul>

{{--                                <li><i class="fas fa-phone-alt"></i>Téléphone: <a--}}
{{--                                        href="{{WT::getContent('telephone1_site_web')->name??''}}">{{WT::getContent('telephone1_site_web')->name??''}}</a>--}}
{{--                                </li>--}}
                                <li><i class="fas fa-envelope"></i>Email: <a
                                        href="mailto:{{WT::getContent('email_site_web')->name??''}}">{{WT::getContent('email_site_web')->name??''}}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col col-xl-auto d-none d-md-block">
{{--                        <a class="user-login" href="{{route('login')}}"><i class="fas fa-user-circle"></i> Connexion</a>--}}
                    </div>
                    <div class="col-md-auto text-center">
                        <div class="header-social">
                            <a href="{{ WT::getContent('lien_facebook')->link ?? '' }}" target="_blank"><i
                                    class="fab fa-facebook-f"></i></a>
                            <a href="{{ WT::getContent('lien_twitter')->link ?? '' }}" target="_blank"><i
                                    class="fab fa-twitter"></i></a>
                            <a href="{{ WT::getContent('lien_linkedin')->link ?? '' }}" target="_blank"><i
                                    class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-wrapper">
            <div class="sticky-active">
                <div class="container position-relative z-index-common">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto">
                            <div class="vs-logo"><a href="{{url('/')}}" wire:navigate><img  src="{{ WT::getContent('logo_topmeneu_site_web')->img ?? '' }}" alt="logo"></a>

                            </div>
                        </div>
                        <div class="col text-end text-xl-center">
                            <nav class="main-menu menu-style1 d-none d-lg-block">
                                <ul>
                                    <li class="">
                                        <a href="{{ route('events.index') }}" wire:navigate>Lomé COM’ TOUR</a>
                                    </li>
                                    <li class="">
                                        <a href="{{ route('events.index') }}" wire:navigate>Événements</a>
                                    </li>


                                    <li class="menu-item-has-children">
                                        <a href="#" wire:navigate>Nous</a>
                                        <ul class="sub-menu">
                                            <li><a href="{{route('nous.mission')}}" wire:navigate>Mission</a></li>
                                            <li><a href="{{route('nous.vision')}}" wire:navigate>Vision</a></li>
                                            <li><a href="{{route('nous.historique')}}" wire:navigate>Historique</a></li>
                                            <li><a href="{{ route('projets.index') }}" wire:navigate>Nos projets</a></li>
                                        </ul>
                                    </li>
                                    <li class="menu-item-has-children">
                                        <a href="{{route('blog')}}" wire:navigate>Actualité</a>
                                        <ul class="sub-menu">
                                            <li><a href="{{route('blog')}}" wire:navigate>Articles</a></li>
                                            <li><a href="#" wire:navigate>Comptes rendus</a></li>
                                            <li><a href="{{ route('media.home') }}" wire:navigate>Média</a></li>
                                        </ul>
                                    </li>

                                    <li class="menu-item-has-children">
                                        <a href="{{route('annuaire.consulter')}}" wire:navigate>Annuaire</a>
                                        <ul class="sub-menu">
                                            <li><a href="{{route('annuaire.consulter')}}" wire:navigate>Consulter</a></li>
                                            <li><a href="{{route('annuaire.rejoindre')}}" wire:navigate>Rejoindre</a></li>
                                            <li><a href="{{route('annuaire.suggerer')}}" wire:navigate>Suggérer une adhésion</a></li>
                                        </ul>
                                    </li>

                                    <li><a href="/formations" wire:navigate>Formation</a></li>
                                    <li><a href="{{route('contact')}}" wire:navigate> Contact</a></li>
{{--                                    <li class=""> <a href="{{route('apprendre')}}">Apprendre</a> </li>--}}

                                </ul>
                            </nav>
                            <button class="vs-menu-toggle d-inline-block d-lg-none"><i class="fal fa-bars"></i></button>
                        </div>
                        <div class="col-auto d-none d-xl-block">
                            <div class="header-btns">

                                @if(auth()->check())
                                    <form   id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                    <a href="/logout"    class="vs-btn style4  " onclick="if (confirm('Voulez-vous vous deconnecter ?')) {document.getElementById('logout-form').submit();} return false;">Se deconnecter </a>
                                    <a href="/user/tableau-de-bord" class="mx-2"> <span>{{ auth()->user()->name }}</span></a>
                                @else
                                    <a href="/register" class="vs-btn style4">S' inscrire </a>
                                    <a href="/login" class="vs-btn style4 mx-2">Se connecter </a>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header><!--==============================
  Hero Area
  ==============================-->




    @yield('content')




    <!--==============================
Footer Area
==============================-->
    <footer class="footer-wrapper footer-layout1">
        <div class="shape-mockup jump d-none d-xxxl-block" data-bottom="0%" data-left="-270px">
            <div class="vs-border-circle"></div>
        </div>
        <div class="widget-area">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="" style="width: 100%;height: 50px"></div>
                   {{-- <div class="col-md-6 col-xl-auto">
                        <div class="widget footer-widget">
                            <div class="vs-widget-about">
                                <div class="footer-logo"><a href="#">

                                        <img src="{{ WT::getContent('logo_footer_site_web')->img ?? '' }}"
                                             alt="logo"></a>
                                </div>
                                <p class="footer-text">{{ WT::getContent('slogan_site_web')->content ?? '' }}</p>
                                <p class="footer-info"><i class="fal fa-phone-alt"></i><a class="text-inherit"
                                                                                          href="tel:+{{ WT::getContent('telephone1_site_web')->name ?? '' }}">{{ WT::getContent('telephone1_site_web')->name ?? '' }}</a>
                                </p>
                                <p class="footer-info"><i class="fal fa-envelope"></i><a class="text-inherit"
                                                                                         href="mailto:{{ WT::getContent('email_site_web')->name ?? '' }}">{{ WT::getContent('email_site_web')->name ?? '' }}</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-6 col-xl-auto">
                        <div class="widget nav_menu footer-widget">
                            <h3 class="widget_title">RPA</h3>
                            <div class="menu-all-pages-container footer-menu">
                                <ul class="menu">
                                    <li><a href="{{route('blog')}}">Actualité</a></li>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-6 col-xl-auto">
                        <div class="widget nav_menu footer-widget">
                            <h3 class="widget_title">RPA</h3>
                            <div class="menu-all-pages-container footer-menu">
                                <ul class="menu">
                                    <li><a href="{{route('blog')}}">Actualité</a></li>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-auto">
                        <div class="widget  footer-widget">
                            <h3 class="widget_title">Actualité</h3>


                            <div class="recent-post-wrap">
                                @php  $blogs = App\Models\Post::with(['user'])->where([ 'type' => 'blog', 'online' => 1])->limit(2)->get() ;@endphp
                                @forelse($blogs->take(2) as $k=>$blog)
                                    <div class="recent-course">
                                        <div class="media-img"><a href="#"><img src="{{$blog->img}}"
                                                                                alt="Blog Image"></a></div>
                                        <div class="media-body">
                                            <div class="recent-course-meta"><a
                                                    href="{{$blog->link}}">{{$blog->name}}</a></div>
                                            <h4 class="post-title"><a class="text-inherit" href="{{$blog->link}}">
                                                    {!! Str::limit($blog->resume,20) !!}
                                                </a>
                                            </h4>
                                        </div>
                                    </div>

                                @empty

                                @endforelse


                            </div>
                        </div>
                    </div>--}}
                </div>
            </div>
        </div>
        <div class="copyright-wrap">
            <div class="container">
                <div class="row justify-content-between align-items-center">
                    <div class="text-center col-lg-auto">
                        <a href="/login">login</a>
                        <a href="/admin">admin</a>
                        <p class="copyright-text">

                            Copyright ©️ Relations Publics Afrique
                        </p>
                    </div>
                    <div class="col-auto d-none d-lg-block">
                        <div class="social-style1">
                            <a href="{{ WT::getContent('lien_facebook')->link ?? '' }}" target="_blank"><i
                                    class="fab fa-facebook-f"></i>Facebook</a>
                            <a href="{{ WT::getContent('lien_twitter')->link ?? '' }}" target="_blank"><i
                                    class="fab fa-twitter"></i>Twitter</a>
                            <a href="{{ WT::getContent('lien_linkedin')->link ?? '' }}" target="_blank"><i
                                    class="fab fa-linkedin-in"></i>Linked In</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer> <!-- Scroll To Top -->
    <a href="#" class="scrollToTop scroll-btn"><i class="far fa-arrow-up"></i></a>




</div>


<!-- jQuery (CDN with fallback) -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"><\\/script>')</script>
<!-- Slick Slider -->
<script src="{{asset('front/assets/js/slick.min.js')}}"></script>
{{--<!--{{asset('front/ <script src="assets/js/app.min.js"></script> ')}}-->--}}
<!-- Bootstrap -->
<script src="{{asset('front/assets/js/bootstrap.min.js')}}"></script>
<!-- Wow.js Animation -->
<script src="{{asset('front/assets/js/wow.min.js')}}"></script>
<!-- Magnific Popup -->
<script src="{{asset('front/assets/js/jquery.magnific-popup.min.js')}}"></script>
<!-- Main Js File -->
<script src="{{asset('front/assets/js/main.js')}}"></script>

<script>
    (function(){
        function initMobileMenu(){
            if (window.jQuery && jQuery.fn && typeof jQuery.fn.vsmobilemenu === 'function') {
                jQuery('.vs-menu-wrapper').vsmobilemenu();
            }
            // Fallback: ensure burger toggles wrapper visibility
            var $ = window.jQuery;
            if ($) {
                var $wrapper = $('.vs-menu-wrapper');
                var $toggles = $('.vs-menu-toggle');
                if ($wrapper.length && $toggles.length) {
                    $toggles.off('click.__fallback').on('click.__fallback', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $wrapper.toggleClass('vs-body-visible');
                    });
                    // Close on outside click
                    $(document).off('click.__fallback').on('click.__fallback', function(){
                        $wrapper.removeClass('vs-body-visible');
                    });
                    $wrapper.off('click.__fallback').on('click.__fallback', function(e){ e.stopPropagation(); });
                    // Close on ESC
                    $(document).off('keydown.__fallback').on('keydown.__fallback', function(e){ if (e.key === 'Escape') { $wrapper.removeClass('vs-body-visible'); }});
                }
            }
            // Native fallback (no jQuery dependency)
            try {
                var wrapper = document.querySelector('.vs-menu-wrapper');
                var toggles = document.querySelectorAll('.vs-menu-toggle');
                if (wrapper && toggles.length) {
                    toggles.forEach(function(btn){
                        btn.removeEventListener('click', window.__vsToggleHandler, true);
                        window.__vsToggleHandler = function(ev){ ev.preventDefault(); ev.stopPropagation(); wrapper.classList.toggle('vs-body-visible'); };
                        btn.addEventListener('click', window.__vsToggleHandler, true);
                    });
                    document.removeEventListener('click', window.__vsOutsideHandler, true);
                    window.__vsOutsideHandler = function(){ wrapper.classList.remove('vs-body-visible'); };
                    document.addEventListener('click', window.__vsOutsideHandler, true);
                    wrapper.removeEventListener('click', window.__vsWrapperHandler, true);
                    window.__vsWrapperHandler = function(e){ e.stopPropagation(); };
                    wrapper.addEventListener('click', window.__vsWrapperHandler, true);
                    document.removeEventListener('keydown', window.__vsEscHandler, true);
                    window.__vsEscHandler = function(e){ if (e.key === 'Escape') { wrapper.classList.remove('vs-body-visible'); } };
                    document.addEventListener('keydown', window.__vsEscHandler, true);
                }
            } catch (e) {}
        }
        if (document.readyState !== 'loading') {
            initMobileMenu();
        } else {
            document.addEventListener('DOMContentLoaded', initMobileMenu);
        }
        document.addEventListener('livewire:navigated', initMobileMenu);
    })();
    </script>



@persist('googletagmanager')
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-QYMTJTZMSS"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-QYMTJTZMSS');
</script>
@endpersist
@livewireScripts
@stack('scripts')

<!-- Modal de confirmation -->
@include('components.confirmation-modal')

</body>

</html>
