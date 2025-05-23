<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Livewire\Attributes\Validate;
use App\Models\Incidencias;
use App\Models\Tecnico;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;
use App\Models\Auditoria;
use App\Models\Comentarios;
use App\Models\Estadoincidencia;

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
        $data->EstadoIncidencia_idEstadoIncidencia = 1;
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
            'tecnico_id' => 'nullable|exists:tecnico,idTecnico',
            'area_id' => 'nullable|exists:areas,id',
        ]);

        $incidencia = Incidencias::findOrFail($id);
        $incidencia->Tecnico_idTecnico = $request->filled('tecnico_id') ? $request->tecnico_id : null;
        $incidencia->Area_idArea = $request->filled('area_id') ? $request->area_id : null;

        $original = $incidencia->getOriginal();

        $tecnicoNuevo = $request->filled('tecnico_id') ? Tecnico::find($request->tecnico_id) : null;
        $tecnicoAnterior = $original['Tecnico_idTecnico'] ? Tecnico::find($original['Tecnico_idTecnico']) : null;

        $areaNueva = $request->filled('area_id') ? Area::find($request->area_id) : null;
        $areaAnterior = $original['Area_idArea'] ?? null ? Area::find($original['Area_idArea']) : null;

        $incidencia->save();

        $cambios = [];

        // Verificar si cambió el técnico
        if ($tecnicoAnterior?->idTecnico !== $tecnicoNuevo?->idTecnico) {
            $cambios['tecnico'] = [
                'antes' => $tecnicoAnterior?->nombreTecnico,
                'despues' => $tecnicoNuevo?->nombreTecnico,
            ];
        }

        // Verificar si cambió el área
        if ($areaAnterior?->id !== $areaNueva?->id) {
            $cambios['area'] = [
                'antes' => $areaAnterior?->area_name,
                'despues' => $areaNueva?->area_name,
            ];
        }

        // Si hay cambios, se guarda en la auditoría
        if (!empty($cambios)) {
            Auditoria::create([
                'accion' => 'Asignacion',
                'modelo' => 'Incidencia',
                'modelo_id' => $incidencia->idIncidencia,
                'cambios' => json_encode($cambios),
                'usuario_id' => Auth::id(),
            ]);
        }

        return redirect()->back()->with('success', 'Responsable asignado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $datas = Incidencias::with(['cliente', 'tecnico', 'estadoincidencia', 'area'])->findorfail($id);
        $datatecnicos = Tecnico::all();
        $dataareas = Area::all();

        $auditorias = Auditoria::where('modelo', 'Incidencia')
            ->where('modelo_id', $id)
            ->with('usuario') // si tienes relación con User
            ->latest()
            ->get();

        $comentarios = Comentarios::where('incidencia_id', $id)
            ->with('usuario') // si tienes relación con User
            ->latest()
            ->get();

        $estadosincidencias = Estadoincidencia::all();
        return view('incidencias.show', compact(['datas', 'datatecnicos', 'dataareas', 'auditorias', 'comentarios', 'estadosincidencias']));
    }

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

        $original = $data->getOriginal();

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

        // Auditoría: se ejecuta después de guardar
        Auditoria::create([
            'accion' => 'actualización',
            'modelo' => 'Incidencia',
            'modelo_id' => $data->idIncidencia,
            'cambios' => json_encode([
                'antes' => $original,
                'despues' => $data->getChanges(),
            ]),
            'usuario_id' => Auth::user()->id,
        ]);

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

        $original = $data->getOriginal();

        // Recuperar los archivos anteriores
        $archivosAnteriores = json_decode($data->adjuntoIncidencia, true) ?? [];

        // Combinar los archivos antiguos con los nuevos
        $archivosFinales = array_merge($archivosAnteriores, $archivos);

        // Asignar la lista final de archivos a la incidencia
        $data->adjuntoIncidencia = json_encode($archivosFinales);

        // Guardar los cambios en la base de datos
        $data->save();

        Auditoria::create([
            'accion' => 'Adjuntos',
            'modelo' => 'Incidencia',
            'modelo_id' => $data->idIncidencia,
            'cambios' => json_encode([
                'antes' => $original,
                'despues' => $data->getChanges(),
            ]),
            'usuario_id' => Auth::user()->id,
        ]);

        // Redirigir con un mensaje de éxito
        return redirect()->route('incidencias.show', ['id' => $data->idIncidencia])->with('success', 'Incidencia actualizada correctamente.');
    }

    public function getAuditoria($id)
    {
        $datas = Incidencias::findorfail($id);
        $auditorias = Auditoria::where('modelo', 'Incidencia')
            ->where('modelo_id', $id)
            ->with('usuario') // Asegúrate que la relación esté bien definida
            ->latest()
            ->get();

        return response()->json($auditorias, $datas);
    }

    public function comentarios(Request $request, string $id)
    {

        $request->validate([
            'contenido' => 'required|string|max:1000',
        ]);

        $comentarios = new Comentarios();

        $comentarios->incidencia_id = $id;
        $comentarios->usuario_id = Auth::id();
        $comentarios->contenido = $request->contenido;

        $comentarios->save();

        return redirect()->back()->with('success', 'Comentario agregado correctamente');
    }

    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'estado_id' => 'required|exists:estadoincidencia,idEstadoIncidencia',
        ]);

        $incidencia = Incidencias::findorfail($id);
        $incidencia->EstadoIncidencia_idEstadoIncidencia = $request->estado_id;

        $original = $incidencia->getOriginal();

        $estadoNuevo = Estadoincidencia::find($request->estado_id);
        $estadoAnterior = Estadoincidencia::find($original['EstadoIncidencia_idEstadoIncidencia']);

        $incidencia->save();

        Auditoria::create([
            'accion' => 'Cambio de estado',
            'modelo' => 'Incidencia',
            'modelo_id' => $incidencia->idIncidencia,
            'cambios' => json_encode([
                'estado' => [
                    'antes' => $estadoAnterior ? $estadoAnterior->descriEstadoIncidencia : null,
                    'despues' => $estadoNuevo ? $estadoNuevo->descriEstadoIncidencia : null,
                ],
            ]),
            'usuario_id' => Auth::user()->id,
        ]);

        return redirect()->back()->with('success', 'Estado asignado correctamente.');
    }

    public function resolverIncidencia(Request $request, $id)
    {
        $this->cambiarEstado($request, $id);
        $this->updateFile($request, $id);
        $this->comentarios($request, $id);

        return redirect()->back()->with('success', 'Estado asignado correctamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
