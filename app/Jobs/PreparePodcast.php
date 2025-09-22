<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class PreparePodcast implements ShouldQueue
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
        Log::info("Preparando podcast {$this->podcastId} con titulo {$this->title}");

        sleep(seconds: 2);

        Log::info("Podcast #{$this->podcastId} preparado");
    }
}
