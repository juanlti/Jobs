<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessImage implements ShouldQueue
{
    use Queueable, Batchable, Dispatchable;

    public function __construct(
        public string $fileName,
        public int $index,
        public int $total
    ) {
        //
    }

    public function handle(): void
    {
        // Si el job se ejecuta fuera de un batch, $this->batch() puede ser null
        if ($this->batch()?->cancelled()) {
            Log::info("Procesamiento de {$this->fileName} cancelado");
            return;
        }

        Log::info("Procesando imagen {$this->index}/{$this->total}: {$this->fileName}");

        // Simulamos proceso
        sleep(rand(1, 3));

        Log::info("Imagen {$this->fileName} procesada correctamente");
    }
}
