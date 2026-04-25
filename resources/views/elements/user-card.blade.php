{{--<div class="shadow-sm bg-white overflow-hidden rounded-t-xl rounded">--}}
{{--    <div class="min-h-[150px] bg-gray-400">--}}
{{--        <a href="{{$user->url}}" class="aspect-[4/2]">--}}
{{--            <img src="{{$user->img}}" alt="{{$user->fullname}}" class="aspect-[4/2] object-cover">--}}
{{--        </a>--}}
{{--    </div>--}}
{{--    <div class="p-3">--}}
{{--        <h3 class="text-lg font-semibold">{{$user->fullname}}</h3>--}}
{{--        <h3>{{$user->pays->name}}</h3>--}}
{{--    </div>--}}
{{--</div>--}}

<a href="{{$profil->link}}">

    <div class="max-w-md w-full bg-white shadow rounded-lg overflow-hidden">
        <div class="relative">
            <img class="h-56 w-full object-cover" src="{{asset($profil->cover)}}" alt="">
            <div class="absolute inset-0 bg-gray-900 opacity-60"></div>

            <div class="absolute inset-0 p-4 flex flex-col justify-end">
                <div class="flex items-center">
                    <img class="w-32 h-32 rounded-full mr-3 object-cover" src="{{$profil->image}}" alt="Avatar de l'utilisateur">

                </div>
                <h2 class="text-lg font-bold text-white leading-tight">{{ $profil->fullname }}</h2>


            </div>
        </div>

        <div class="px-4 py-2 mt-2">
            {!! $profil->resume_bio !!}

        </div>
    </div>

</a>
