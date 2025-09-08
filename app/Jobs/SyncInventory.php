<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncInventory implements ShouldQueue
{
    use Queueable;

    /*
     * El tiempo maximo de ejecucion en segundos o por que falle
     * Si excede este tiempo, el job se consiera bloqueado
     */
    public int $timeout = 30;


    /**
     * Create a new job instance.
     */
    public function __construct(public int $productId, public string $productName)
    {

    }

    public function tries(): int
    {
        return 3; //intentara ejecutarse maximo 3 veces
    }

    public function backoff(): array
    {
        return [10, 30]; // espera 10 segds despues del primer fallo, 30 segundos despues del segundo
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        Log::info(message: "Sincronizando inventario para producto #{$this->productId}: {$this->productName}");

        if (random_int(1, 100) <= 90) {

            Log::warning(message: "Error al sincronizar producto #{$this->productId}");
            throw new \Exception(message: "Error de conexion con API externa");
        };

        //simulamos procesamiento
        sleep(2);
        Log::info(message: "Sincronizacion completada para producto #{$this->productId}");


    }

    public function failed(\Throwable $exception): void
    {  //capturamos el jobs de la excepcion

        Log::error(
            "Fallo definitivo al sincronizar producto #{$this->productId}",
            ['error' => $exception->getMessage()]
        );


    }


}
