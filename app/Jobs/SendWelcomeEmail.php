<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public readonly string $userEmail, public readonly string $userName)
    {


    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        //throw new \Exception('Ha ocurrido un error');

        Log::info("Enviando email de bienvenida a {$this->userEmail}  para {$this->userName}");

        //sleep(seconds: 30);

        Log::info("Email enviado a {$this->userEmail}");
    }
}
