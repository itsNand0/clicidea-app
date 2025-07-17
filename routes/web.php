<?php

use App\Http\Controllers\Usercontroller;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Clientecontroller;
use App\Http\Controllers\Incidenciacontroller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::resource('users', Usercontroller::class);
    Route::resource('incidencias', Incidenciacontroller::class);
    Route::get('/exportarExcel', [IncidenciaController::class, 'exportarExcel'])->name('incidencias.exportarExcel');
    Route::get('/index', [Clientecontroller::class, 'index'])->name('clientes.index');
    Route::get('/incidencias/create', [IncidenciaController::class, 'create'])->name('incidencias.create');
    Route::get('/incidencias/{id}', [IncidenciaController::class, 'show'])->name('incidencias.show');
    Route::put('/incidencias/{id}/asignar', [IncidenciaController::class, 'asignar'])->name('incidencias.asignar');
    Route::get('/incidencias/{id}/auditoria', [IncidenciaController::class, 'getAuditoria'])->name('incidencias.auditoria');
    Route::post('/incidencias/{id}/comentario', [Incidenciacontroller::class, 'comentarios'])->name('comentarios.store');
    Route::post('/incidencias/{id}/cambiar_estado', [Incidenciacontroller::class, 'cambiarEstado'])->name('incidencias.cambiarEstado');
    Route::put('/incidencias/{id}/resolver', [Incidenciacontroller::class, 'resolverIncidencia'])->name('incidencias.resolverIncidencia');
    Route::put('/incidencias/{id}/update-file', [IncidenciaController::class, 'updateFile'])->name('incidencias.updateFile');
    Route::get('/dashboard', function () {return view('dashboard');})->name('view.dashboard');
    Route::get('/incidencias/justshow/{id}', [IncidenciaController::class, 'justshow'])->name('incidencias.justshow');
    Route::get('/buscar-incidencia', function (Illuminate\Http\Request $request) {
        $id = $request->query('id');

        if ($id && App\Models\Incidencias::find($id)) {
            return redirect()->route('incidencias.show', ['id' => $id]);
        }

        return redirect()->back()->with('error', 'Incidencia no encontrada.');
    })->name('buscar.incidencia');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/'); 
})->name('logout');

