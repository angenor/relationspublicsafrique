@extends('layouts.front')

@section('content')

    {{--    <div class="  h-96 bg-cyan-900" style="background-image: url('images/communication-dentreprise.png')">--}}

    {{--    </div>--}}



<div class="mt-16">
    <div id="default-carousel" class="relative w-full mycontainer" data-carousel="slide">
        <!-- Carousel wrapper -->
        <div class="relative h-56 overflow-hidden rounded-lg md:h-96">
            <!-- Item 1 -->
            <div class="hidden duration-1000 ease-in-out" data-carousel-item>
                <div
                    class="bg-gray-400 h-96 flex items-center relative mycontainer   w-full my-5 rounded-lg overflow-hidden"
                    style="background-image: url('{{asset('img/img1.jpeg')}}')">
                    <div class="absolute inset-0 bg-black/50"></div>
                    <div class="ml-16 relative text-white">
                        <div class="max-w-md w-full ">
                            <strong class="text-5xl font-bold "> THE EARTH’S #1 STUDENT CHOOSE</strong>
                            <p class="text-lg my-5"> Search over 200 individual encyclopedias and reference books from
                                the worlds.</p>
                        </div>
                        <div class="">
                            <button class="bg-indigo-900 text-white rounded-lg p-4 px-7">
                                Explorer
                            </button>
                        </div>
                        <div class=" w-full bg-amber-300">
                            action
                        </div>
                    </div>

                </div>

            </div>
            <!-- Item 2 -->
            <div class="hidden duration-1000 ease-in-out" data-carousel-item>
                <div
                    class="bg-gray-400 h-96 flex items-center relative mycontainer   w-full my-5 rounded-lg overflow-hidden"
                    style="background-image: url('{{asset('img/img2.jpeg')}}')">
                    <div class="absolute inset-0 bg-black/50"></div>
                    <div class="ml-16 relative text-white">
                        <div class="max-w-md w-full ">
                            <strong class="text-5xl font-bold "> THE EARTH’S #1 STUDENT CHOOSE</strong>
                            <p class="text-lg my-5"> Search over 200 individual encyclopedias and reference books from
                                the worlds.</p>
                        </div>
                        <div class="">
                            <button class="bg-indigo-900 text-white rounded-lg p-4 px-7">
                                Explorer
                            </button>
                        </div>
                        <div class=" w-full bg-amber-300">
                            action
                        </div>
                    </div>

                </div>

            </div>
            <!-- Item 3 -->
            <div class="hidden duration-1000 ease-in-out" data-carousel-item>
                <div
                    class="bg-gray-400 h-96 flex items-center relative mycontainer   w-full my-5 rounded-lg overflow-hidden"
                    style="background-image: url('{{asset('img/img3.jpeg')}}')">
                    <div class="absolute inset-0 bg-black/50"></div>
                    <div class="ml-16 relative text-white">
                        <div class="max-w-md w-full ">
                            <strong class="text-5xl font-bold "> THE EARTH’S #1 STUDENT CHOOSE</strong>
                            <p class="text-lg my-5"> Search over 200 individual encyclopedias and reference books from
                                the worlds.</p>
                        </div>
                        <div class="">
                            <button class="bg-indigo-900 text-white rounded-lg p-4 px-7">
                                Explorer
                            </button>
                        </div>
                        <div class=" w-full bg-amber-300">
                            action
                        </div>
                    </div>

                </div>

            </div>
            <!-- Item 4 -->
            <div class="hidden duration-1000 ease-in-out" data-carousel-item>
                <div
                    class="bg-gray-400 h-96 flex items-center relative mycontainer   w-full my-5 rounded-lg overflow-hidden"
                    style="background-image: url('{{asset('img/img4.jpeg')}}')">
                    <div class="absolute inset-0 bg-black/50"></div>
                    <div class="ml-16 relative text-white">
                        <div class="max-w-md w-full ">
                            <strong class="text-5xl font-bold "> THE EARTH’S #1 STUDENT CHOOSE</strong>
                            <p class="text-lg my-5"> Search over 200 individual encyclopedias and reference books from
                                the worlds.</p>
                        </div>
                        <div class="">
                            <button class="bg-indigo-900 text-white rounded-lg p-4 px-7">
                                Explorer
                            </button>
                        </div>
                        <div class=" w-full bg-amber-300">
                            action
                        </div>
                    </div>

                </div>

            </div>
            <!-- Item 5 -->
            <div class="hidden duration-1000 ease-in-out" data-carousel-item>
                <div
                    class="bg-gray-400 h-96 flex items-center relative mycontainer   w-full my-5 rounded-lg overflow-hidden"
                    style="background-image: url('{{asset('img/img5.jpeg')}}')">
                    <div class="absolute inset-0 bg-black/50"></div>
                    <div class="ml-16 relative text-white">
                        <div class="max-w-md w-full ">
                            <strong class="text-5xl font-bold "> THE EARTH’S #1 STUDENT CHOOSE</strong>
                            <p class="text-lg my-5"> Search over 200 individual encyclopedias and reference books from
                                the worlds.</p>
                        </div>
                        <div class="">
                            <button class="bg-indigo-900 text-white rounded-lg p-4 px-7">
                                Explorer
                            </button>
                        </div>
                        <div class=" w-full bg-amber-300">
                            action
                        </div>
                    </div>

                </div>

            </div>
        </div>
        <!-- Slider indicators -->
        <div class="absolute z-30 flex space-x-3 -translate-x-1/2 bottom-5 left-1/2">
            <button type="button" class="w-3 h-3 rounded-full" aria-current="true" aria-label="Slide 1"
                    data-carousel-slide-to="0"></button>
            <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 2"
                    data-carousel-slide-to="1"></button>
            <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 3"
                    data-carousel-slide-to="2"></button>
            <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 4"
                    data-carousel-slide-to="3"></button>
            <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 5"
                    data-carousel-slide-to="4"></button>
        </div>
        <!-- Slider controls -->
        <button type="button"  class="absolute top-0 left-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"   data-carousel-prev>


        <span
            class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
            <svg class="w-4 h-4 text-white dark:text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                 fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 1 1 5l4 4"/>
            </svg>
            <span class="sr-only">Previous</span>
        </span>
        </button>
        <button type="button" class="absolute top-0 right-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"   data-carousel-next>


        <span
            class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
            <svg class="w-4 h-4 text-white dark:text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                 fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="m1 9 4-4-4-4"/>
            </svg>
            <span class="sr-only">Next</span>
        </span>
        </button>
    </div>
</div>




    <div class="my-10 lg:grid grid-cols-3 gap-10 mycontainer">

        @foreach(range(1,3) as $k=>$v)
            <div class="fc h-96 bg-white border-blue-500 border-2 shadow-xl rounded-lg">
                <div class="">
                    <div class=""></div>
                    <h1 class="text-2xl font-bold">IT SOFTWARE & ENGINEERING</h1>

                </div>
            </div>
        @endforeach

    </div>


    <div class="mycontainer items-center justify-center flex flex-col my-8">
        <h3 class="text-xl font-bold"> BIENVENUE DANS L'ÉDUCATION MONDIALE</h3>
        <h2 class="text-6xl max-w-5xl  w-full font-bold   text-center p-4"> Faites passer votre organisation
            d'apprentissage au niveau supérieur.</h2>
    </div>



    <div class="">
        <div class="py-4 mx-4 max-w-7xl lg:mx-auto">
            <div class="grid   sm:grid-cols-2 xl:grid-cols-3 lg:grid-cols-3  gap-4">
                @foreach($profils->take(6) as $key=>$user)
                    @include('elements.user-card',['profil'=>$user])
                @endforeach
            </div>
        </div>
    </div>


    <div class="mycontainer">
       <div class="fc flex-col">
           <h4>WELCOME TO GLOBAL EDUCATION</h4>
           <h3> Explore Courses</h3>
       </div>
    </div>


    <div class="grid  grid-cols-3 gap-4 mycontainer">
        @foreach(range(1,6) as $k=>$v)

            <div class="  bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                <a href="#">
                    <img class="rounded-t-lg" src="{{asset('img/img' . ($k + 1) . '.jpeg')}}" alt="" />
                </a>
                <div class="p-5">
                    <a href="#">
                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Noteworthy technology acquisitions 2021</h5>
                    </a>
                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
                    <a href="#" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Read more
                        <svg class="w-3.5 h-3.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                        </svg>
                    </a>
                </div>
            </div>

        @endforeach

    </div>






@endsection

@section('title')
    Bienvenue sur IRPAF
@endsection

@section('js')

@endsection


@section('css')

@endsection
