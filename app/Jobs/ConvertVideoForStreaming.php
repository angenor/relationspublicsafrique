<?php

namespace App\Jobs;

use App\Models\Chapitre;
use App\Models\Course;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ConvertVideoForStreaming implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private mixed $chapitre;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $type, int $id)
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
        $destination ='/'.($this->chapitre->id).'/'.$this->chapitre->id.'.png';
//        $lowBitrate = (new X264)->setKiloBitrate(250);
        $midBitrate = (new X264)->setKiloBitrate(500);
//        $highBitrate = (new X264)->setKiloBitrate(1000);

        FFMpeg::fromDisk('videos')
            ->open( $this->chapitre->video )
            ->exportForHLS()
            ->setSegmentLength(10) // optional
            ->setKeyFrameInterval(48) // optional
//            ->addFormat($lowBitrate)
            ->addFormat($midBitrate)
//            ->addFormat($highBitrate)
            ->onProgress( function ($percentage) {
                $this->chapitre->update(['progressing_percentage' => $percentage]);
            })
            ->toDisk("videos/$this->type")
            ->save($destination);

            $this->chapitre->update([
            'processed' => true,
            'processed_file' => $this->chapitre->id.'_0_500.m3u8',
            ]);
    }
}
