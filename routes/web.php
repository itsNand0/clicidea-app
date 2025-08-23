<?php

use App\Http\Controllers\Usercontroller;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Clientecontroller;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Incidenciacontroller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {

    Route::middleware(['auth', 'permission:users.ver'])->group(function () {
        Route::resource('users', Usercontroller::class);
    });

    Route::post('/import-sedes', [ImportController::class, 'importSedes'])->name('import.sedes');
    Route::post('/import-incidencias', [ImportController::class, 'importIncidencias'])->name('import.incidencias');

    Route::middleware(['auth', 'permission:incidencias.ver'])->group(function () {
        Route::resource('incidencias', Incidenciacontroller::class);
        Route::get('/incidencias/{id}/auditoria', [IncidenciaController::class, 'getAuditoria'])->name('incidencias.auditoria');
        Route::get('/incidencias/{id}', [IncidenciaController::class, 'show'])->name('incidencias.show');
        Route::post('/incidencias/{id}/comentario', [Incidenciacontroller::class, 'comentarios'])->name('comentarios.store');
        Route::put('/incidencias/{id}/update-file', [IncidenciaController::class, 'updateFile'])->name('incidencias.updateFile');
        Route::get('/incidencias/justshow/{id}', [IncidenciaController::class, 'justshow'])->name('incidencias.justshow');
    });

    Route::middleware(['auth', 'permission:incidencias.exportarExcel'])->group(function () {
        Route::get('/clientes/exportarExcelCliente', [Clientecontroller::class, 'exportarExcelCliente'])->name('clientes.exportarExcelCliente');
        Route::get('/exportarExcel', [IncidenciaController::class, 'exportarExcel'])->name('incidencias.exportarExcel');
    });

    Route::middleware(['auth', 'permission:clientes.ver'])->group(function () {
        Route::get('/index', [Clientecontroller::class, 'index'])->name('clientes.index');
        Route::get('/clientes', [Clientecontroller::class, 'create'])->name('clientes.create');
        Route::post('/clientes', [Clientecontroller::class, 'store'])->name('clientes.store');
        Route::get('/clientes/{id}/edit', [Clientecontroller::class, 'edit'])->name('clientes.edit');
        Route::put('/clientes/{id}', [Clientecontroller::class, 'update'])->name('clientes.update');
        Route::delete('/clientes/{id}', [Clientecontroller::class, 'destroy'])->name('clientes.destroy');
    });

    Route::middleware(['auth', 'permission:incidencias.crear'])->group(function () {
        Route::get('/incidencias/create', [IncidenciaController::class, 'create'])->name('incidencias.create');
    });
    
    Route::middleware(['auth', 'permission:incidencias.asignar'])->group(function () {
        Route::put('/incidencias/{id}/asignar', [IncidenciaController::class, 'asignar'])->name('incidencias.asignar');
    });
    
    Route::middleware(['auth', 'permission:incidencias.cambiarEstado'])->group(function () {
        Route::post('/incidencias/{id}/cambiar_estado', [Incidenciacontroller::class, 'cambiarEstado'])->name('incidencias.cambiarEstado');
    });
    
    Route::middleware(['auth', 'permission:incidencias.resolver'])->group(function () {
        Route::put('/incidencias/{id}/resolver', [Incidenciacontroller::class, 'resolverIncidencia'])->name('incidencias.resolverIncidencia');
    });

    
    Route::get('/dashboard', function () {return view('dashboard');})->name('view.dashboard');

    Route::middleware(['auth', 'permission:incidencias.crear'])->group(function () {
        Route::get('/buscar-incidencia', function (Illuminate\Http\Request $request) {
            $id = $request->query('id');

            if ($id && App\Models\Incidencias::find($id)) {
                return redirect()->route('incidencias.show', ['id' => $id]);
            }

            return redirect()->back()->with('error', 'Incidencia no encontrada.');
        })->name('buscar.incidencia');
    });
    
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/'); 
})->name('logout');

