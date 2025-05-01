<?php

use App\Http\Controllers\Usercontroller;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Incidenciacontroller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login']);

//Route::middleware('auth')->group(function () {
    Route::resource('users', Usercontroller::class);
    Route::resource('incidencias', Incidenciacontroller::class);
    Route::get('/incidencias/create', [IncidenciaController::class, 'create'])->name('incidencias.create');
    Route::get('/dashboard', function () {
        return view('dashboard');
    });
//});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/'); // Redirige a donde tú quieras, como el login
})->name('logout');

