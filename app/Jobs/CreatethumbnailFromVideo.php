<?php

namespace App\Jobs;

use App\Models\Chapitre;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class CreatethumbnailFromVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Chapitre|Chapitre[]|Course|Course[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|null
     */
    private mixed $chapitre;

    /**
     * Create a new job instance.
     */
    public function __construct( public string $type, public int $id)
    {

        if ($type == 'chapitre'){
            $this->chapitre = Chapitre::find($id);
        }else{
            $this->chapitre =  Course::find($id);
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        if ($this->type == 'chapitre'){

            FFMpeg::fromDisk('videos')
                ->open( $this->chapitre->video )
                ->getFrameFromSeconds(2)
                ->export()
                ->toDisk('public')
                ->save("/chapitre/".$this->id.'.png' );
        }else{


            FFMpeg::fromDisk('videos')
                ->open( $this->chapitre->video )
                ->getFrameFromSeconds(2)
                ->export()
                ->toDisk('public')
                ->save("/cours/".$this->id.'.png' );
        }


    }
}
