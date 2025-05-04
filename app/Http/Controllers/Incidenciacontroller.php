<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Livewire\Attributes\Validate;
use App\Models\Incidencias;
use App\Models\Tecnico;
use Illuminate\Support\Facades\Auth;

class Incidenciacontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('incidencias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $data = new Incidencias();
        $validate = $request->validate([
            'contrato' => 'required|string|max:255',
            'asunto' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'contacto' => 'required|string|max:255',
            'adjunto.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data->usuarioIncidencia = Auth::user()->name;
        $data->asuntoIncidencia = $request->asunto;
        $data->descriIncidencia = $request->descripcion;
        $data->contactoIncidencia = $request->contacto;
        $data->fechaIncidencia = now();
        $data->Tecnico_idTecnico = 1;
        $data->cliente_idCliente  = 1;
        $data->EstadoIncidencia_idEstadoIncidencia  = 1;
        $archivos = [];

        if ($request->hasFile('adjunto')) {
            foreach ($request->file('adjunto') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('adjuntos', $filename, 'public');
                $archivos[] = $filename;
            }
        }

        $data->adjuntoIncidencia = json_encode($archivos);

        $data->save(); // Guardar la incidencia en la base de datos
        // Redirigir o mostrar un mensaje de éxito
        return redirect()->route('incidencias.show', ['id' => $data->idIncidencia])->with('success', 'Incidencia creada correctamente.');
    }

    public function asignar(Request $request, $id)
    {
        $request->validate([
            'tecnico_id' => 'required|exists:tecnico,idTecnico',
        ]);

        $incidencia = Incidencias::findOrFail($id);
        $incidencia->Tecnico_idTecnico = $request->tecnico_id;
        $incidencia->save();

        return redirect()->back()->with('success', 'Técnico asignado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $datas = Incidencias::with(['cliente', 'tecnico', 'estadoincidencia'])->findorfail($id);
        $datatecnicos = Tecnico::all();
        return view('incidencias.show', compact(['datas', 'datatecnicos']));  // Pasar la lista de técnicos a la vista  
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {   
        
        // Encuentra la incidencia
        $data = Incidencias::findOrFail($id);
        
        // Validación de los campos de texto
        $request->validate([
            'asunto' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'contacto' => 'nullable|string|max:255',
        ]);

        // Asignación de los valores a la incidencia
        $data->asuntoIncidencia = $request->asunto;
        $data->descriIncidencia = $request->descripcion;
        $data->contactoIncidencia = $request->contacto;

        if ($request->has('asunto')) {
            $data->asuntoIncidencia = $request->asunto;
        }
        
        if ($request->has('descripcion')) {
            $data->descriIncidencia = $request->descripcion;
        }
        
        if ($request->has('contacto')) {
            $data->contactoIncidencia = $request->contacto;
        }

        // Guardar los cambios en la base de datos
        $data->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('incidencias.show', ['id' => $data->idIncidencia])->with('success', 'Incidencia actualizada correctamente.');
    }

    public function updateFile(Request $request, string $id)
    {   
        
        // Encuentra la incidencia
        $data = Incidencias::findOrFail($id);
        
        // Validación de los campos de texto
        $request->validate([
            'adjunto.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Inicializamos un array para los nuevos archivos
        $archivos = [];
        
        // Si se adjuntan archivos, procesarlos
        if ($request->hasFile('adjunto')) {
            foreach ($request->file('adjunto') as $file) {
                // Generar un nombre único para el archivo
                $filename = time() . '_' . $file->getClientOriginalName();
                // Almacenar el archivo en la carpeta pública
                $file->storeAs('adjuntos', $filename, 'public');
                // Añadir el nombre del archivo al array
                $archivos[] = $filename;
            }
        }
        // Recuperar los archivos anteriores
        $archivosAnteriores = json_decode($data->adjuntoIncidencia, true) ?? [];

        // Combinar los archivos antiguos con los nuevos
        $archivosFinales = array_merge($archivosAnteriores, $archivos);

        // Asignar la lista final de archivos a la incidencia
        $data->adjuntoIncidencia = json_encode($archivosFinales);

        // Guardar los cambios en la base de datos
        $data->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('incidencias.show', ['id' => $data->idIncidencia])->with('success', 'Incidencia actualizada correctamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
