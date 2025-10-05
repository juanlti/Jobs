<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendDelayedEmail implements ShouldQueue
{
    use Queueable,Dispatchable;

    //public $queue = 'emails';


    /**
     * Create a new job instance.
     */
    public function __construct(public string $email,public string $subject,public string $content)
    {
        //le asignamos la cola especifica
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Enviando email progromado a {$this->email}",[
            'subject'=>$this->subject,
            'content'=>$this->content,
        ]);
        //simula el tiempo de envio
        sleep(5);

        Log::info("Email enviado a {$this->email}");
    }
}
