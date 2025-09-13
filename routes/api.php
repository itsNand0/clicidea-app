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
Route::post('/web-push/subscribe-public', [WebPushController::class, 'subscribePublic']); // Test de suscripción sin auth
Route::post('/web-push/test-incidencia', [WebPushController::class, 'testIncidenciaNotification']); // Test notificación incidencia

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/web-push/subscribe', [WebPushController::class, 'subscribe']);
    Route::post('/web-push/unsubscribe', [WebPushController::class, 'unsubscribe']);
    Route::post('/web-push/test', [WebPushController::class, 'testPush']);
    Route::get('/web-push/status', [WebPushController::class, 'status']);
});