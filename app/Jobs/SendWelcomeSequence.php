<?php

namespace App\Jobs;

use Bus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendWelcomeSequence implements ShouldQueue
{
    use Queueable,Dispatchable;

    // cada job va tener 200 segundos para que considere un jobs fallido si supero el timeout
    public int $timeout=200;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $userId,public string $userEmail,public string $userName)
    {
        //le asignamos a una cola especifica
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Iniciando secuencia de  bienvenida para {$this->userEmail}");

        //enviamos email de bienvenida inmediatamente
        $this->sendEmail('Bienvenido a nuestra plataforma', 'Hola {$this->userName}, gracias por registrarte....');

        //Programamos el resto de la secuencia con diferentes retrasos
        Bus::chain([
            //email de tips - 15 segundos despues
            (new SendDelayedEmail($this->userEmail,subject: 'Tips para tu primera publicacion',content: 'Hola {$this->userName}, aqui tienes algunos tips para tu primera publicacion...' ))->delay(now()->addSeconds(15)),

            //email de funcionalidades - 30 segundos despues
            (new SendDelayedEmail($this->userEmail,subject: 'Descubre mas funcionalidades de nuestra plataforma',content: 'Hola {$this->userName}, aqui tienes algunas funcionalidades nuevas...' ))->delay(now()->addSeconds(30)),

            // email de descuento - 50 segundos despues
            (new SendDelayedEmail($this->userEmail,subject: "Un regalo especial para vos",content: 'Hola {$this->userName}, te ofrecemos un 10% de descuento...' ))->delay(now()->addSeconds(50)),

        ])->dispatch();

        Log::info("Secuencia de bienvenida terminada para {$this->userEmail}");

    }

    private function sendEmail(string $subject,string $content): void{
        //simulamos el envio de un email
        Log::info("Enviando email a {$this->userEmail}",[
            'subject'=>$subject,
            'content'=>$content,
        ]);
        sleep(1);

        Log::info("Email enviado a {$this->userEmail}");
    }

}
