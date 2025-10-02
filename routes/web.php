<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\PWAController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {

    Route::middleware(['auth', 'permission:users.ver'])->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::post('/import-sedes', [ImportController::class, 'importSedes'])->name('import.sedes');
    Route::post('/import-incidencias', [ImportController::class, 'importIncidencias'])->name('import.incidencias');

    Route::middleware(['auth', 'permission:incidencias.crear'])->group(function () {
        Route::get('/incidencias/create', [IncidenciaController::class, 'create'])->name('incidencias.create');
        Route::post('/incidencias', [IncidenciaController::class, 'store'])->name('incidencias.store');
    });

    Route::middleware(['auth', 'permission:incidencias.ver'])->group(function () {
        Route::get('/incidencias', [IncidenciaController::class, 'index'])->name('incidencias.index');
        Route::get('/incidencias/{id}/auditoria', [IncidenciaController::class, 'getAuditoria'])->name('incidencias.auditoria')->where('id', '[0-9]+');
        Route::get('/incidencias/justshow/{id}', [IncidenciaController::class, 'justshow'])->name('incidencias.justshow')->where('id', '[0-9]+');
        Route::get('/incidencias/{id}', [IncidenciaController::class, 'show'])->name('incidencias.show')->where('id', '[0-9]+');
    });

    Route::middleware(['auth', 'permission:incidencias.editar'])->group(function () {
        Route::get('/incidencias/{id}/edit', [IncidenciaController::class, 'edit'])->name('incidencias.edit')->where('id', '[0-9]+');
        Route::put('/incidencias/{id}', [IncidenciaController::class, 'update'])->name('incidencias.update')->where('id', '[0-9]+');
        Route::post('/incidencias/{id}/comentario', [IncidenciaController::class, 'comentarios'])->name('comentarios.store')->where('id', '[0-9]+');
        Route::put('/incidencias/{id}/update-file', [IncidenciaController::class, 'updateFile'])->name('incidencias.updateFile')->where('id', '[0-9]+');
    });

    Route::middleware(['auth', 'permission:incidencias.eliminar'])->group(function () {
        Route::delete('/incidencias/{id}', [IncidenciaController::class, 'destroy'])->name('incidencias.destroy')->where('id', '[0-9]+');
    });

    Route::middleware(['auth', 'permission:incidencias.exportarExcel'])->group(function () {
        Route::get('/clientes/exportarExcelCliente', [ClienteController::class, 'exportarExcelCliente'])->name('clientes.exportarExcelCliente');
        Route::get('/exportarExcel', [IncidenciaController::class, 'exportarExcel'])->name('incidencias.exportarExcel');
    });

    Route::middleware(['auth', 'permission:clientes.ver'])->group(function () {
        Route::get('/index', [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('/clientes', [ClienteController::class, 'create'])->name('clientes.create');
        Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
        Route::get('/clientes/{id}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('/clientes/{id}', [ClienteController::class, 'update'])->name('clientes.update');
        Route::delete('/clientes/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
    });

    Route::middleware(['auth', 'permission:incidencias.asignar'])->group(function () {
        Route::put('/incidencias/{id}/asignar', [IncidenciaController::class, 'asignar'])->name('incidencias.asignar');
    });
    
    Route::middleware(['auth', 'permission:incidencias.cambiarEstado'])->group(function () {
        Route::post('/incidencias/{id}/cambiar_estado', [IncidenciaController::class, 'cambiarEstado'])->name('incidencias.cambiarEstado');
    });
    
    Route::middleware(['auth', 'permission:incidencias.resolver'])->group(function () {
        Route::put('/incidencias/{id}/resolver', [IncidenciaController::class, 'resolverIncidencia'])->name('incidencias.resolverIncidencia');
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
    
    // 🔔 WEB PUSH ROUTES (con autenticación web tradicional)
    Route::post('/web-push/subscribe', [App\Http\Controllers\Api\WebPushController::class, 'subscribe'])->name('web-push.subscribe');
    Route::post('/web-push/test-incidencia', [App\Http\Controllers\Api\WebPushController::class, 'testIncidenciaNotification'])->name('web-push.test-incidencia');
    Route::post('/web-push/unsubscribe', [App\Http\Controllers\Api\WebPushController::class, 'unsubscribe'])->name('web-push.unsubscribe');
    Route::get('/web-push/status', [App\Http\Controllers\Api\WebPushController::class, 'status'])->name('web-push.status');
    
    // 🔍 DIAGNÓSTICO DE NOTIFICACIONES
    Route::get('/diagnostico-notificaciones', function() {
        return view('diagnostico-notificaciones');
    })->name('diagnostico.notificaciones');
    
    Route::get('/test-push', function() {
        return view('diagnostico-simple');
    })->name('test.push.simple');
    
    Route::post('/test/notificacion-asignacion', function() {
        try {
            \Illuminate\Support\Facades\Artisan::call('test:notificacion-asignacion');
            $output = \Illuminate\Support\Facades\Artisan::output();
            
            return response()->json([
                'success' => true,
                'output' => $output,
                'message' => 'Comando ejecutado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    })->name('test.notificacion-asignacion');
    
    Route::post('/test/notificacion-directa', function() {
        try {
            // Ejecutar la misma lógica del comando pero directamente
            $usuario = \App\Models\User::first();
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'error' => 'No hay usuarios en la base de datos'
                ], 400);
            }
            
            $incidencia = \App\Models\Incidencias::first();
            if (!$incidencia) {
                return response()->json([
                    'success' => false,
                    'error' => 'No hay incidencias en la base de datos'
                ], 400);
            }
            
            // Crear un usuario "asignador" ficticio
            $asignadoPor = \App\Models\User::where('id', '!=', $usuario->id)->first() ?? $usuario;
            
            // Enviar la notificación
            $usuario->notify(new \App\Notifications\IncidenciaAsignada($incidencia, $asignadoPor));
            
            return response()->json([
                'success' => true,
                'message' => 'Notificación enviada exitosamente',
                'data' => [
                    'usuario' => $usuario->name,
                    'incidencia' => $incidencia->asuntoincidencia,
                    'asignado_por' => $asignadoPor->name
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    })->name('test.notificacion-directa');
    
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

