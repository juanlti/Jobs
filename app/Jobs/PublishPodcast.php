<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class PublishPodcast implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $podcastId, public string $title)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Publicando podcast #' . $this->podcastId . ' con titulo ' . $this->title . '');
        sleep(5);
        Log::info('Podcast #' . $this->podcastId . ' publicado');
    }
}
