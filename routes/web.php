<?php

use App\Jobs\GenerateSiteMap;
use App\Jobs\PreparePodcast;
use App\Jobs\ProcessImage;
use App\Jobs\ProcessPayment;
use App\Jobs\ProcessPaymentSecurely;
use App\Jobs\ProcessUserUpload;
use App\Jobs\PublishPodcast;
use App\Jobs\SendWelcomeEmail;
use App\Jobs\SyncInventory;
use App\Jobs\TranscribePodcast;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('llego', function () {

    $currenteDrive = config('queue.default');

    SendWelcomeEmail::dispatch(userEmail: 'usuario@ejemplo.com', userName: 'usuario Ejemplo');
    return "Email programado usando drive {$currenteDrive} . Revisa los logs para mas detalles.";
});

Route::get('/send-welcome-to-queue/{queue}', function ($queue) {

    // despachamos el job a una cola especifica
    SendWelcomeEmail::dispatch(userEmail: 'usuario@ejemplo.com', userName: 'usuario Ejemplo')->onQueue($queue);

    return "Email programado en la cola: {$queue}";
});

Route::get('/send-welcome-to-queue/{connection}/{queue}', function ($connection, $queue) {
    //despachamos el jobs a una conexion y cola especifica
    SendWelcomeEmail::dispatch(userEmail: 'usuario@ejemplo.com', userName: 'usuario Ejemplo')
        ->onConnection($connection)
        ->onQueue($queue);

    return "Email programado en la conexion: {$connection} , en la cola: {$queue}";
});


Route::get('dispatch-basic', function () {
    SendWelcomeEmail::dispatch(userEmail: 'juancruz@hotmail.com', userName: 'juancruz');
    config('queue.default');
    return "Email programado en la cola: " . config('queue.default');
});


// capitulo 2

//dispatch-async, e ejecuta de manera Síncrono  (mismo tiempo), no necesita un worker
Route::get('dispatch-basic-async', function () {
    SendWelcomeEmail::dispatchSync(userEmail: 'juancruz@hotmail.com', userName: 'juancruz');
    config('queue.default');
    return "Email programado en la cola: " . config('queue.default');
});

//dispatch after response, envia la respuesta y luego ejecuta el jobs,se ejecuta de manera Síncrono  (mismo tiempo),
// no necesita un workers.
Route::get('dispatch-basic-async-after-response', function () {
    SendWelcomeEmail::dispatchAfterResponse(userEmail: 'juancruz@hotmail.com', userName: 'juancruz');
    config('queue.default');
    return "Email programado en la cola: " . config('queue.default');
});

//dispatch con delay
Route::get('dispatch-basic-delay-now/{minutes?}', function ($minutes = 1) {
    SendWelcomeEmail::dispatch(userEmail: 'juancruz@hotmail.com', userName: 'juancruz')->delay(now()->addMinutes($minutes));
    config('queue.default');
    return "Email programado en la cola: " . config('queue.default') . "se ejecutara en : {$minutes} minutos";
});

//dispatch con condidicional

Route::get('dispatch-if/{condition?}', function ($condition = 1) {
    $shouldSend = (bool)$condition;
    SendWelcomeEmail::dispatchIf($shouldSend, userEmail: 'juancruz@hotmail.com', userName: 'juancruz');
    config('queue.default');
    return "Email programado en la cola: " . config('queue.default') . " con condicional si solo si si es verdadero : " . $condition;
});

//Jobs ProcessPayment
Route::get('/process-payments', function () {
    // despachamos jobs con diferentes prioridades
    ProcessPayment::dispatch(ordenId: 600, amount: 10, isHighPriority: false); //baja  prioridad
    ProcessPayment::dispatch(ordenId: 525, amount: 1000, isHighPriority: true); //alta prioridad
    ProcessPayment::dispatch(ordenId: 450, amount: 500, isHighPriority: false);//baja  prioridad
    ProcessPayment::dispatch(ordenId: 530, amount: 1500, isHighPriority: true);//alta prioridad

    return 'Pagos enviados a diferentes colas. Para procesar con prioridad, ejecuta:
            "php artisan queue:work --queue=payments-high,payments-default"';

});

//Jobs SyncInventory, jobs con posibilidades de error y lo capturamos
Route::get('/jobs-syncInventory/{id}', function ($id) {

    $products = [
        1 => 'iPhone 15 Pro',
        2 => 'Samsung Galaxy S24',
        3 => 'MacBook Pro'
    ];
    $name = $products[$id] ?? "Producto #{$id}";

    SyncInventory::dispatch(productId: $id, productName: $name . " <==== juan");

    return "Sincronizacion de '{$name}'  enviada a la cola - Ejecuta queue:work";

});

// routes/web.php
Route::get('/h-test', function () {
    dispatch(function () {
        \Log::info('Horizon test OK');
    })->onQueue('default');
    return 'ok';
});
// routes/web.php
Route::get('/horizon/debug', function () {
    return response()->json([
        'env' => app()->environment(),
        'horizon_prefix' => config('horizon.prefix'),
        'redis' => config('database.redis.default'),
        'queues' => config('horizon.environments.' . app()->environment() . '.supervisor-1.queue'),
    ]);
});

Route::get('/generate-sitemap/{domain}/{count?}', function ($domain, $count = 3) {


    for ($i = 0; $i < $count; $i++) {
        GenerateSiteMap::dispatch($domain, $i);
    }
    return "Generando de sitemap para {$domain} programada  {$count} veces - Ejecuta queue:work";
})->name('settings.profile');

Route::get('/processUser/{idUser}/{fileName}', function ($idUser, $fileName) {
    ProcessUserUpload::dispatch(userId: $idUser = 15, fileName: $fileName = 'juan');
    return "Procesando usuario terminado {$idUser} - Ejecuta queue: " . config('queue.default');
})->name('settings.profile');

Route::get('/processPodcast/{id}/{title}', function ($id, $title) {

    Bus::chain([
        new PreparePodcast($id, $title),
        new TranscribePodcast($id, $title),
        new PublishPodcast($id, $title),
    ])->catch(function ($exception) use ($id) {
        Log::error("Error en la cadena de procesamiento del podcast #{$id}",
            ['error' => $exception->getMessage()]);
    })->dispatch();

    return "Procesamiento en cadena del podcast  {$id} -  programado - Ejecuta queue: " . config('queue.default');
});

Route::get('/procss-imagees', function () {
    $images = [
        'image1.jpg',
        'image2.jpg',
        'image3.jpg',
        'image4.jpg',
        'image5.jpg',
        'image6.jpg',
        'image7.jpg',

    ];

    $totalImages = count($images);
    // la capacidad del Bas es dinamico
    $batch = Bus::batch([])
        ->then(function ($batch, $exeption) {
            Log::info("Todas las imagenes han sido procesadas", [
                'processd' => $batch->processedJobs(),
                'failed' => $batch->failedJobs,

            ]);
        })
        ->catch(function ($batch, $exception) {
            Log::error("Error en el procesamiento por lotes", [
                'failed_job' => $batch->failedJobs,
            ]);
        })
        ->finally(function ($batch) {
            Log::info("Proceso de imagenes finalizado", [
                'finished_job' => $batch->finishedAt,
            ]);
        })->name('process-images')
        ->dispatch();

    Log::info("informacion del Batch antes de agregale los jobs", [$batch->toArray()]);
    //agregamos los jobs (imagenes) al batch
    foreach ($images as $index => $image) {
        $batch->add(new ProcessImage($image, $index + 1, $totalImages));
    }


    return "Procsamiento por lotes de {$totalImages} imagnes iniciado - ID de batch: {$batch->id} - Ejecuta queue: " . config('queue.default');
});

Route::get('/batch-status/{id}', function ($id) {
    $batch = Bus::findBatch($id);

    if (!$batch) {
        return "Batch no encontrado";
    }
    return [
        'id' => $batch->id,
        'name' => $batch->name,
        'total_jobs' => $batch->totalJobs,
        'failed_jobs' => $batch->failedJobs,
        'pending_jobs' => $batch->pendingJobs,
        'processed_jobs' => $batch->processedJobs(),
        'progress' => $batch->progress() . '%',
        'finished' => $batch->finishedAt,
        'cancelled' => $batch->cancelledAt,

    ];
})->name('settings.profile');

//jobs encriptados para datos sensibles

Route::get('/process-secure-payment/{customerId}', function ($customerId) {

    //ProcessPaymentSecurely::dispatchSync('4242424242424242', 99.99, 1);
    //estos datos deben venir de un formulario de pago

    $creditCardNumber = '424242424242';
    $cvv = '123';
    $expirationDate = '12/2022';
    $amount = 99.99;

    //utilizamos los datos recibidos para instanciar el job
    dispatch(new ProcessPaymentSecurely($creditCardNumber, $amount,$customerId));
    return "Procesamiento seguro de pago programado - Ejecuta queue: " . config('queue.default');
});
