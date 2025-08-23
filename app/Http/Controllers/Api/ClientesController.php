<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClientesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /*try {
            $clientes = Cliente::all();
            
            return response()->json([
                'success' => true,
                'message' => 'Lista de clientes obtenida exitosamente',
                'data' => $clientes,
                'count' => $clientes->count()
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la lista de clientes',
                'error' => $e->getMessage()
            ], 500);
        }*/
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos del formulario
            $validated = $request->validate([
                'atm_id' => 'required|integer|min:1|unique:cliente,atm_id',
                'nombre' => 'required|string|max:255',
                'zona' => 'required|string|max:255',
            ], [
                'atm_id.required' => 'El ATM ID es obligatorio.',
                'atm_id.integer' => 'El ATM ID debe ser un número entero.',
                'atm_id.min' => 'El ATM ID debe ser mayor a 0.',
                'atm_id.unique' => 'Ya existe un cliente con este ATM ID.',
                'nombre.required' => 'El nombre del cliente es obligatorio.',
                'nombre.string' => 'El nombre debe ser texto válido.',
                'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
                'zona.string' => 'La zona debe ser texto válido.',
                'zona.max' => 'La zona no puede exceder 255 caracteres.',
            ]);

            // Crear el nuevo cliente
            $cliente = Cliente::create([
                'atm_id' => $validated['atm_id'],
                'nombre' => $validated['nombre'],
                'zona' => $validated['zona'] ?? null,
            ]);

            // Retornar respuesta JSON para la API
            return response()->json([
                'success' => true,
                'message' => "Cliente '{$cliente->nombre}' creado exitosamente con ATM ID: {$cliente->atm_id}",
                'data' => $cliente
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        /*try {
            $cliente = Cliente::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Cliente encontrado exitosamente',
                'data' => $cliente
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado',
                'error' => "No se encontró un cliente con ID: {$id}"
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el cliente',
                'error' => $e->getMessage()
            ], 500);
        }*/
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        /*try {
            $cliente = Cliente::findOrFail($id);
            
            // Validar los datos, excluyendo el cliente actual para unique validation
            $validated = $request->validate([
                'atm_id' => 'sometimes|required|integer|min:1|unique:cliente,atm_id,' . $id . ',idcliente',
                'nombre' => 'sometimes|required|string|max:255',
                'zona' => 'sometimes|string|max:255',
            ], [
                'atm_id.required' => 'El ATM ID es obligatorio.',
                'atm_id.integer' => 'El ATM ID debe ser un número entero.',
                'atm_id.min' => 'El ATM ID debe ser mayor a 0.',
                'atm_id.unique' => 'Ya existe otro cliente con este ATM ID.',
                'nombre.required' => 'El nombre del cliente es obligatorio.',
                'nombre.string' => 'El nombre debe ser texto válido.',
                'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
                'zona.string' => 'La zona debe ser texto válido.',
                'zona.max' => 'La zona no puede exceder 255 caracteres.',
            ]);

            // Actualizar solo los campos que se enviaron
            $cliente->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => "Cliente '{$cliente->nombre}' actualizado exitosamente",
                'data' => $cliente->fresh()
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado',
                'error' => "No se encontró un cliente con ID: {$id}"
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el cliente',
                'error' => $e->getMessage()
            ], 500);
        }*/
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        /*try {
            $cliente = Cliente::findOrFail($id);
            $nombreCliente = $cliente->nombre;
            $atmId = $cliente->atm_id;
            
            $cliente->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Cliente '{$nombreCliente}' (ATM ID: {$atmId}) eliminado exitosamente",
                'data' => null
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado',
                'error' => "No se encontró un cliente con ID: {$id}"
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el cliente',
                'error' => $e->getMessage()
            ], 500);
        }*/
    }
}
