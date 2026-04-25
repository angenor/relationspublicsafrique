@extends('layouts.front')

@section('content')
    <div class="">

        <div class="py-4 mx-4 max-w-7xl lg:mx-auto">


            <div class=" w-full">


                <div class="h-64 flex items-center">
                    <div class="bg-white h-64 flex items-center w-full rounded-lg relative justify-center lg:justify-start " style="background-image:url('{{asset('images/front/ban.jpg')}}'); background-position: center;background-size: cover">
                        <div class="h-full w-full absolute bg-black/70  rounded-lg" ></div>
                        <div class="relative lg:ml-20">
{{--                            <img src="{{$profil->img}}" alt="{{$profil->fullname}}" class="h-56 w-56 object-cover  bg-white rounded-xl border mt-32 shadow-2xl ring ring-gray-100">--}}
                        </div>


                    </div>
                </div>

                <div class="mt-16">

                    <div class="flex">
                    <div class="w-full">
                       <div class="shadow bg-white rounded p-3 font-bold text-sky-800  mb-3">
                           <x-view-user-info-item :title="$profil->fullname"></x-view-user-info-item>
                           <x-view-user-info-item :title="$profil->title"></x-view-user-info-item>
                           <x-view-user-info-item :title="$profil->fonction"></x-view-user-info-item>
                           <x-view-user-info-item :title="$profil->domaine"></x-view-user-info-item>
                           <x-view-user-info-item :title="$profil->email"></x-view-user-info-item>
                           <x-view-user-info-item :title="$profil->pays->name"></x-view-user-info-item>

                       </div>

                        <div class="shadow bg-white rounded p-3 font-bold text-sky-800 space-y-1.5 ">

                            <x-view-user-info-item :title="$profil->bio"></x-view-user-info-item>
                            <x-view-user-info-item :title="$profil->contact"></x-view-user-info-item>
                            <x-view-user-info-item :title="$profil->adresse"></x-view-user-info-item>
                            <x-view-user-info-item :title="$profil->tel"></x-view-user-info-item>
                            <x-view-user-info-item :title="$profil->facebook"></x-view-user-info-item>
                            <x-view-user-info-item :title="$profil->twitter"></x-view-user-info-item>
                            <x-view-user-info-item :title="$profil->youtube"></x-view-user-info-item>
                            <x-view-user-info-item :title="$profil->linkding"></x-view-user-info-item>
                            <x-view-user-info-item :title="$profil->site"></x-view-user-info-item>
                        </div>
                    </div>

                    <div class=" lg:w-80">
                        <div class=" bg-white h-full rounded lg:ml-3.5 lg:border-l hidden lg:block p-3 shadow-sm">


                        </div>
                    </div>


                    </div>
                </div>

            </div>




        </div>
    </div>

@endsection
