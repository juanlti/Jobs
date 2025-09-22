<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;


class ProcessUserUpload implements ShouldQueue, ShouldBeUnique
{
    use Queueable, Dispatchable;

    /**
     * Tiempo durante el cual el job se condiera unico (en segundos) y por lo tanto esta activo para ese usuario
     * ejmplo: el usuario desea subir 2 archivos y cada archivo se representa con un job, entonces el primer archivo va a subir y con una duracion 1 minuto
     *  Y el segundo archivo no se subiria
     *
     */

    public int $uniqueFor = 60; // 1 minuto

    public function __construct(public int $userId, public string $fileName)
    {
        //
    }


    public function uniqueId()
    {
        //define que hace este job sea unico ( en este caso por el idUser)
        return $this->userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Procesando archivo {$this->fileName} para el usuario {$this->userId}");

        // simulamos el procesamiento
        sleep(seconds: 5);

        Log::info("Procesamiento de   {$this->fileName} terminado");


    }
}
