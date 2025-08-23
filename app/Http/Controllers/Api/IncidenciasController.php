<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Incidencias;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidenciasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /*try {
            $incidencias = Incidencias::with(['cliente', 'estadoIncidencia', 'usuario'])->get();
            
            return response()->json([
                'success' => true,
                'data' => $incidencias
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las incidencias',
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
            $validate = $request->validate([
                'atm_id' => 'required|integer',
                'asunto' => 'required|string',
                'descripcion' => 'required|string',
                'contacto' => 'required|string',
            ]);

            $data = new Incidencias();
            $data->usuarioincidencia = User::find(26)->name; //usuario: Automatizacion de sistema
            $data->usuario_idusuario = 0; // Responsable de la incidencia
            $data->asuntoincidencia = $request->asunto;
            $data->descriincidencia = $request->descripcion;
            $data->contactoincidencia = $request->contacto;
            $data->fechaincidencia = now();
            // Verificar si existe el cliente con ese atm_id
            $cliente = Cliente::where('atm_id', $request->atm_id)->first();

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'No existe un cliente con el atm_id proporcionado'
                ], 404);
            }

            // Asignar el idcliente autoincrementable del cliente recién creado o existente
            $data->cliente_idcliente = $cliente->idcliente;
            $data->estadoincidencia_idestadoincidencia = 1;
            
            $data->save();

            return response()->json([
                'success' => true,
                'message' => 'Incidencia creada correctamente',
                'data' => $data->load(['cliente', 'estadoIncidencia', 'usuario'])
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
                'message' => 'Error al crear la incidencia',
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
            $incidencia = Incidencias::with(['cliente', 'estadoIncidencia', 'usuario', 'comentarios'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $incidencia
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Incidencia no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la incidencia',
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
            $incidencia = Incidencias::findOrFail($id);
            
            $validate = $request->validate([
                'contrato' => 'sometimes|required|string|max:255',
                'asunto' => 'sometimes|required|string|max:255',
                'descripcion' => 'sometimes|required|string|max:255',
                'contacto' => 'sometimes|required|string|max:255',
                'usuario_idusuario' => 'sometimes|nullable|integer',
                'estadoincidencia_idestadoincidencia' => 'sometimes|required|integer',
                'adjunto.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            // Actualizar solo los campos enviados
            if ($request->has('asunto')) {
                $incidencia->asuntoincidencia = $request->asunto;
            }
            if ($request->has('descripcion')) {
                $incidencia->descriincidencia = $request->descripcion;
            }
            if ($request->has('contacto')) {
                $incidencia->contactoincidencia = $request->contacto;
            }
            if ($request->has('contrato')) {
                $incidencia->cliente_idcliente = $request->contrato;
            }
            if ($request->has('usuario_idusuario')) {
                $incidencia->usuario_idusuario = $request->usuario_idusuario;
            }
            if ($request->has('estadoincidencia_idestadoincidencia')) {
                $incidencia->estadoincidencia_idestadoincidencia = $request->estadoincidencia_idestadoincidencia;
                
                // Si se está resolviendo la incidencia, agregar fecha de resolución
                if ($request->estadoincidencia_idestadoincidencia == 3) { // Asumiendo que 3 es "Resuelto"
                    $incidencia->fecharesolucionincidencia = now();
                }
            }

            // Manejar archivos adjuntos si se envían
            if ($request->hasFile('adjunto')) {
                $archivos = [];
                foreach ($request->file('adjunto') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('adjuntos', $filename, 'public');
                    $filePath = 'storage/adjuntos/' . $filename;
                    $archivos[] = $filePath;
                }
                $incidencia->adjuntoincidencia = json_encode($archivos);
            }

            $incidencia->save();

            return response()->json([
                'success' => true,
                'message' => 'Incidencia actualizada correctamente',
                'data' => $incidencia->load(['cliente', 'estadoIncidencia', 'usuario'])
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Incidencia no encontrada'
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
                'message' => 'Error al actualizar la incidencia',
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
            $incidencia = Incidencias::findOrFail($id);
            $incidencia->delete();

            return response()->json([
                'success' => true,
                'message' => 'Incidencia eliminada correctamente'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Incidencia no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la incidencia',
                'error' => $e->getMessage()
            ], 500);
        }*/
    }
}
