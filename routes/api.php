<?php

use App\Http\Controllers\Api\ClientesController;
use App\Http\Controllers\Api\IncidenciasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('incidencias', IncidenciasController::class);
Route::apiResource('clientes', ClientesController::class);