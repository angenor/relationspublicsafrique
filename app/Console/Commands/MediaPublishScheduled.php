<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;

class MediaPublishScheduled extends Command
{
    protected $signature = 'media:publish-scheduled';

    protected $description = 'Bascule les contenus média programmés en publiés une fois leur échéance atteinte (déclenche la newsletter via l\'observer).';

    public function handle(): int
    {
        $count = 0;

        Media::query()
            ->where('status', 'scheduled')
            ->where('published_at', '<=', now())
            ->cursor()
            ->each(function (Media $media) use (&$count): void {
                // L'update déclenche MediaObserver::saved (alerte newsletter en US5).
                $media->update(['status' => 'published']);
                $count++;
            });

        $this->info("Médias publiés : {$count}");

        return self::SUCCESS;
    }
}
