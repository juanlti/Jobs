<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessPaymentSecurely implements ShouldQueue, ShouldBeEncrypted
{
    use Queueable, Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $creditCardNumber, public float $amount, public int $customerId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        Log::info('Procesando pago de $' . $this->amount . ' a ' . $this->creditCardNumber . ' para el cliente ' . $this->customerId,[

            'card'=>'****'.substr($this->creditCardNumber,-4),
        ]);

        //simulamos el procesamiento
        sleep(2);

        Log::info('Pago procesado por el monto de $' . $this->amount . ' del cliente ' . $this->customerId . '');
    }
}
