{{--
<div>
    --}}
{{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}{{--

    <div class="  h-full bg-gray-50 container mx-auto">
        <h2 class="text-3xl my-[100px]"> Uploader la vidéo pour : {{$cours->name}}</h2>

        <div class="my-3 "
             x-data="{ uploading: false, progress: 0 }"
             x-on:livewire-upload-start="uploading = true"
             x-on:livewire-upload-finish="uploading = false"
             x-on:livewire-upload-error="uploading = false"
             x-on:livewire-upload-progress="progress = $event.detail.progress"
        >

            <form action="">
                <div class="w-full bg-gray-200 rounded-full dark:bg-gray-700 mt-10" x-show="uploading">
                    <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full"  max="100" x-bind:style="`width: ${progress}%`">  <span x-text="progress"></span> %</div>
                </div>

                <input type="file" wire:model="videoFile"  name="video" class=" my-10   w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">

                @error('videoFile')
                <div class="text-red-500">{{$message}}</div>
                @enderror
            </form>




        </div>
    </div>


</div>
--}}


<div>
    {{-- ... --}}
    <div class="h-full bg-gray-50 container mx-auto">
        <h2 class="text-3xl my-[100px]">Uploader la vidéo pour : {{$cours->name}}</h2>
        @if( session()->has('error'))
            <div class="text-red-500">{{session('error')}}</div>

        @endif
        @if( session()->has('success')    )
            <div class="text-green-500">{{session('success')}}</div>
        @endif
        <div class="my-3" x-data="{ uploading: false, progress: 0 }"
             x-on:livewire-upload-start="uploading = true"
             x-on:livewire-upload-finish="uploading = false"
             x-on:livewire-upload-error="uploading = false"
             x-on:livewire-upload-progress="progress = $event.detail.progress"
        >
            <form wire:submit.prevent="uploadVideo" enctype="multipart/form-data">
                <div class="w-full bg-gray-200 rounded-full dark:bg-gray-700 mt-10" x-show="uploading">
                    <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" x-bind:style="`width: ${progress}%`"> <span x-text="progress"></span>%</div>
                </div>
{{--                wire:model="videoFile"--}}
                <input type="file" name="videoFile" wire:model="videoFile"  class="my-10 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">

                @error('videoFile')
                <div class="text-red-500">{{$message}}</div>
                @enderror

                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4">Upload</button>
            </form>
        </div>
    </div>
</div>
