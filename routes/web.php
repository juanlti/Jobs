<?php

use App\Jobs\ProcessPayment;
use App\Jobs\SendWelcomeEmail;
use App\Jobs\SyncInventory;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

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
    dispatch(function () { \Log::info('Horizon test OK'); })->onQueue('default');
    return 'ok';
});
// routes/web.php
Route::get('/horizon/debug', function () {
    return response()->json([
        'env' => app()->environment(),
        'horizon_prefix' => config('horizon.prefix'),
        'redis' => config('database.redis.default'),
        'queues' => config('horizon.environments.'.app()->environment().'.supervisor-1.queue'),
    ]);
});

