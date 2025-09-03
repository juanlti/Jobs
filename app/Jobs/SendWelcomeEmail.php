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
     */
    public function handle(): void
    {
        Log::info("Enviando email de bienvenida a {$this->userEmail}  para {$this->userName}");

        //sleep(seconds: 30);

        Log::info("Email enviado a {$this->userEmail}");
    }
}
