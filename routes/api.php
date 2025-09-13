<?php

use App\Http\Controllers\Api\ClientesController;
use App\Http\Controllers\Api\IncidenciasController;
use App\Http\Controllers\Api\WebPushController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('incidencias', IncidenciasController::class)->names([
    'index' => 'api.incidencias.index',
    'store' => 'api.incidencias.store',
    'show' => 'api.incidencias.show',
    'update' => 'api.incidencias.update',
    'destroy' => 'api.incidencias.destroy'
]);

Route::apiResource('clientes', ClientesController::class)->names([
    'index' => 'api.clientes.index',
    'store' => 'api.clientes.store',
    'show' => 'api.clientes.show',
    'update' => 'api.clientes.update',
    'destroy' => 'api.clientes.destroy'
]);

// 🔔 WEB PUSH ROUTES
Route::get('/web-push/vapid-key', [WebPushController::class, 'getVapidKey']); // Endpoint público para obtener clave VAPID
Route::post('/web-push/test-simple', [WebPushController::class, 'testSimple']); // Test simple sin autenticación

// Las rutas con autenticación se movieron a web.php para usar sesiones en lugar de Sanctum