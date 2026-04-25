@extends('layouts.default')
@section('title',$pageTitle)


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

    <livewire:filter-profiles/>


@endsection

