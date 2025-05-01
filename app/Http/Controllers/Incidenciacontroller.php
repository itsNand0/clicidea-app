<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Livewire\Attributes\Validate;
use App\Models\Incidencias;
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
            'adjunto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);       
        
        $data->usuarioIncidencia = Auth::user()->name;
        $data->asuntoIncidencia = $request->asunto; 
        $data->descriIncidencia = $request->descripcion;
        $data->contactoIncidencia = $request->contacto;
        $data->fechaIncidencia = now();
        $data->Tecnico_idTecnico = 1;
        $data->cliente_idCliente  = 1;
        $data->EstadoIncidencia_idEstadoIncidencia  = 1;

        if ($request->hasFile('adjunto')) {
            $file = $request->file('adjunto');
            $filename = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs('adjuntos', $filename, 'public'); // Guarda en storage/app/public/adjuntos
        
            $data->adjuntoIncidencia = $filename;
        }
        dd($file->storeAs('public/adjuntos', $filename));
        $data->save(); // Guardar la incidencia en la base de datos
        // Redirigir o mostrar un mensaje de éxito
        return redirect()->route('incidencias.create')->with('success', 'Incidencia creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
