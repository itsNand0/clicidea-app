<?php

use App\Http\Controllers\Usercontroller;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Clientecontroller;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Incidenciacontroller;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\PWAController;
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

    Route::middleware(['auth', 'permission:incidencias.crear'])->group(function () {
        Route::get('/incidencias/create', [IncidenciaController::class, 'create'])->name('incidencias.create');
        Route::post('/incidencias', [IncidenciaController::class, 'store'])->name('incidencias.store');
    });

    Route::middleware(['auth', 'permission:incidencias.ver'])->group(function () {
        Route::get('/incidencias', [Incidenciacontroller::class, 'index'])->name('incidencias.index');
        Route::get('/incidencias/{id}/auditoria', [IncidenciaController::class, 'getAuditoria'])->name('incidencias.auditoria')->where('id', '[0-9]+');
        Route::get('/incidencias/justshow/{id}', [IncidenciaController::class, 'justshow'])->name('incidencias.justshow')->where('id', '[0-9]+');
        Route::get('/incidencias/{id}', [Incidenciacontroller::class, 'show'])->name('incidencias.show')->where('id', '[0-9]+');
    });

    Route::middleware(['auth', 'permission:incidencias.editar'])->group(function () {
        Route::get('/incidencias/{id}/edit', [Incidenciacontroller::class, 'edit'])->name('incidencias.edit')->where('id', '[0-9]+');
        Route::put('/incidencias/{id}', [Incidenciacontroller::class, 'update'])->name('incidencias.update')->where('id', '[0-9]+');
        Route::post('/incidencias/{id}/comentario', [Incidenciacontroller::class, 'comentarios'])->name('comentarios.store')->where('id', '[0-9]+');
        Route::put('/incidencias/{id}/update-file', [IncidenciaController::class, 'updateFile'])->name('incidencias.updateFile')->where('id', '[0-9]+');
    });

    Route::middleware(['auth', 'permission:incidencias.eliminar'])->group(function () {
        Route::delete('/incidencias/{id}', [Incidenciacontroller::class, 'destroy'])->name('incidencias.destroy')->where('id', '[0-9]+');
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
    
    // Ruta para estadísticas
    Route::middleware(['auth', 'permission:incidencias.ver'])->group(function () {
        Route::get('/estadisticas', [EstadisticasController::class, 'index'])->name('estadisticas.index');
    });

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

// Rutas PWA (solo las que no son archivos estáticos)
Route::get('/pwa/install', [PWAController::class, 'install'])->name('pwa.install');
Route::get('/pwa/diagnostics', function() {
    return view('pwa.diagnostics');
})->name('pwa.diagnostics');

// API para PWA (sin middleware auth para evitar 419)
Route::post('/pwa/register-push-token', [PWAController::class, 'registerPushToken'])->name('pwa.register-push-token');
Route::post('/pwa/test-push', [PWAController::class, 'testPush'])->name('pwa.test-push');

