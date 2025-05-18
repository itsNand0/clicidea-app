<?php

use App\Http\Controllers\Usercontroller;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Incidenciacontroller;
use App\Http\Controllers\Tecnicocontroller;
use App\Models\Comentarios;
use App\Models\Incidencias;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login']);

//Route::middleware('auth')->group(function () {
    Route::resource('users', Usercontroller::class);
    Route::resource('incidencias', Incidenciacontroller::class);
    Route::get('/incidencias/create', [IncidenciaController::class, 'create'])->name('incidencias.create');
    Route::get('/incidencias/{id}', [IncidenciaController::class, 'show'])->name('incidencias.show');
    Route::put('/incidencias/{id}/asignar', [IncidenciaController::class, 'asignar'])->name('incidencias.asignar');
    Route::get('/incidencias/{id}/auditoria', [IncidenciaController::class, 'getAuditoria'])->name('incidencias.auditoria');
    Route::post('/incidencias/{id}/comentario', [Incidenciacontroller::class, 'comentarios'])->name('comentarios.store');
    Route::post('/incidencias/{id}/cambiar_estado', [Incidenciacontroller::class, 'cambiarEstado'])->name('incidencias.cambiarEstado');
    Route::put('/incidencias/{id}/resolver', [Incidenciacontroller::class, 'resolverIncidencia'])->name('incidencias.resolverIncidencia');
    Route::put('/incidencias/{id}/update-file', [IncidenciaController::class, 'updateFile'])->name('incidencias.updateFile');

    
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

