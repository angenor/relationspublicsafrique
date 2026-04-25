@extends('layouts.front')

@section('content')
    <div class="">
        <div class="py-4  lg:px-16 px-4  lg:mx-auto">
            <div class="grid  md:grid-cols-2 lg:grid-cols-3   gap-4">
                @foreach($profils as $key=>$user)
                    @include('elements.user-card',['profil'=>$user])
                @endforeach
            </div>
        </div>
    </div>

@endsection
