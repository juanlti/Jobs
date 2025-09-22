<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Log;

class GenerateSiteMap implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public int $tries = 5;

    public function __construct(public string $domain, public int $idJob)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Generando mapa del sitio para ' . $this->domain . 'con id job :' . $this->idJob);

        Log::info('Recopilando URLs..... ' . 'con id job :' . $this->idJob);

        sleep(5);

        Log::info('Generano XML .' . 'con id job :' . $this->idJob);
        sleep(3);

        Log::info("'Sitemap para '.{$this->domain}.' generado correctamente' " . 'con id job :' . $this->idJob);
    }


    public function middleware()
    {
        return [
            (new WithoutOverlapping($this->domain, 10,))
        ];
    }
}
