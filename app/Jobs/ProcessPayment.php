<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;


class ProcessPayment implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $ordenId, public float $amount, public bool $isHighPriority = false)
    {
        //verificamos si un pago supera el umbral de 1000, caso afirmativo, entonces tiene priodiad sobre otros pagos
        $queue = $this->isHighPriority ? 'payments-high' : 'payments-default';
        $this->onQueue($queue);

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $priority = $this->isHighPriority ? 'alta' : 'normal';
        Log::info("Procesando pago #{$this->ordenId} por {$this->amount} dolares (Prioridad: {$priority})");

        //simulamos procesamiento de pago;
        sleep(seconds: $this->isHighPriority ? 1 : 3);

        Log::info("Pago #{$this->ordenId} completado");


    }
}
