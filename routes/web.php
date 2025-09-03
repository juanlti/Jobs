<?php

use App\Jobs\SendWelcomeEmail;
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

    SendWelcomeEmail::dispatch(userEmail: 'usuario@ejemplo.com', userName: 'usuario Ejemplo')->onQueue($queue);

    return "Email programado en la cola: {$queue}";
});

/*
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';
*/
