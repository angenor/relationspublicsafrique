<?php

namespace App\Livewire\Video;

use Illuminate\Support\Facades\Http;
use Livewire\Component;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;


class CreateVideo extends Component
{
    use withFileUploads ;

    public $cours;


    public $videoFile;

    public function  mount($cours )
    {

        $this->cours = $cours;
    }

    public function fileCompleted()
    {

    }
    public function render()
    {
        return view('livewire.video.create-video');
    }


    public function uploadVideo()
    {

        if ($this->videoFile) {
            // Code pour uploader le fichier vers l'URL spécifique

            $response = Http::timeout(60*60)->attach('file', $this->videoFile)->post(url('/admin/uploadVideo'));

            // Gérer la réponse de l'upload, par exemple :
            if ($response->successful()) {
                // Succès de l'upload
                session()->flash('success', 'Upload successful!');
            } else {

                session()->flash('error', 'Upload failed!');
            }
        }
    }

}
