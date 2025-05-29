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
use App\Models\Cargo;
use App\Models\Comentarios;
use App\Models\Estadoincidencia;
use App\Models\User;

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

        $data->usuarioincidencia = Auth::user()->name;
        $data->asuntoincidencia = $request->asunto;
        $data->descriincidencia = $request->descripcion;
        $data->contactoincidencia = $request->contacto;
        $data->fechaincidencia = now();
        $data->cliente_idcliente  = 1;
        $data->estadoincidencia_idestadoincidencia = 1;
        $archivos = [];

        if ($request->hasFile('adjunto')) {
            foreach ($request->file('adjunto') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('adjuntos', $filename, 'public');
                $filePath = 'storage/adjuntos/' . $filename;
                $archivos[] = $filePath;
            }
        }

        $data->adjuntoincidencia = json_encode($archivos);

        $data->save(); 

        return redirect()->route('incidencias.show', ['id' => $data->idincidencia])->with('success', 'Incidencia creada correctamente.');
    }

    public function asignar(Request $request, $id)
    {
        $validate = $request->validate([
            'user_cargo_id' => 'nullable|exists:users,id',
            'user_area_id' => 'nullable|exists:users,id',
        ]);

        $incidencia = Incidencias::findOrFail($id);
        $incidencia->usuario_idusuario = $request->filled('user_cargo_id')
            ? $request->user_cargo_id
            : ($request->filled('user_area_id') ? $request->user_area_id : null);

        $original = $incidencia->getOriginal();

        $usuarioNuevo = null;
        if ($request->filled('user_cargo_id')) {
            $usuarioNuevo = User::find($request->user_cargo_id);
        } elseif ($request->filled('user_area_id')) {
            $usuarioNuevo = User::find($request->user_area_id);
        }

        $incidencia->save();
        $usuarioAnterior = $original['usuario_idusuario'] ? User::find($original['usuario_idusuario']) : null;

        

        $cambios = [];

        // Verificar si cambió el cargo
        if ($usuarioAnterior?->id !== $usuarioNuevo?->id) {
            $cambios['cargo'] = [
                'antes' => $usuarioAnterior?->cargo?->nombre_cargo ?? null,
                'despues' => $usuarioNuevo?->cargo?->nombre_cargo ?? null,
            ];
        }

        // Verificar si cambió el area
        if ($usuarioAnterior?->id !== $usuarioNuevo?->id) {
            $cambios['area'] = [
                'antes' => $usuarioAnterior?->area?->area_name ?? null,
                'despues' => $usuarioNuevo?->area?->area_name ?? null,
            ];
        }

        // Si hay cambios, se guarda en la auditoría
        if (!empty($cambios)) {
            Auditoria::create([
                'accion' => 'Asignacion',
                'modelo' => 'Incidencia',
                'modelo_id' => $incidencia->idincidencia,
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
        $datas = Incidencias::with(['cliente', 'usuario', 'estadoincidencia'])->findorfail($id);
        $dataareas = Area::all();
        $datacargos = Cargo::all();
        $estadosincidencias = Estadoincidencia::all();

        $auditorias = Auditoria::where('modelo', 'Incidencia')
            ->where('modelo_id', $id)
            ->with('usuario') 
            ->latest()
            ->get();

        $comentarios = Comentarios::where('incidencia_id', $id)
            ->with('usuario') 
            ->latest()
            ->get();

        return view('incidencias.show', compact(['datas', 'datacargos', 'dataareas', 'comentarios', 'estadosincidencias', 'auditorias']));
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


        // Asignación de los valores a la incidencia
        $data->asuntoincidencia = $request->asunto;
        $data->descriincidencia = $request->descripcion;
        $data->contactoincidencia = $request->contacto;
        $data->fecharesolucionincidencia = null;

        if ($request->has('asunto')) {
            $data->asuntoincidencia = $request->asunto;
        }

        if ($request->has('descripcion')) {
            $data->descriincidencia = $request->descripcion;
        }

        if ($request->has('contacto')) {
            $data->contactoincidencia = $request->contacto;
        }

        $original = $data->getOriginal();

        // Guardar los cambios en la base de datos
        $data->save();
        

        Auditoria::create([
            'accion' => 'actualización',
            'modelo' => 'Incidencia',
            'modelo_id' => $data->idincidencia,
            'cambios' => json_encode([
                'antes' => $original,
                'despues' => $data->getChanges(),
            ]),
            'usuario_id' => Auth::user()->id,
        ]);

        return redirect()->route('incidencias.show', ['id' => $data->idincidencia])->with('success', 'Incidencia actualizada correctamente.');
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

        if ($request->hasFile('adjunto')) {
            foreach ($request->file('adjunto') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('adjuntos', $filename, 'public');
                $filePath = 'storage/adjuntos/' . $filename;
                $archivos[] = $filePath;
            }
        }

        // Recuperar los archivos anteriores
        $archivosAnteriores = json_decode($data->adjuntoincidencia, true) ?? [];

        // Combinar los archivos antiguos con los nuevos
        $archivosFinales = array_merge($archivosAnteriores, $archivos);

        // Asignar la lista final de archivos a la incidencia
        $data->adjuntoincidencia = json_encode($archivosFinales);

        $original = $data->getOriginal();
        // Guardar los cambios en la base de datos
        $data->save();

        

        Auditoria::create([
            'accion' => 'Adjuntos',
            'modelo' => 'Incidencia',
            'modelo_id' => $data->idincidencia,
            'cambios' => json_encode([
                'antes' => $original,
                'despues' => $data->getChanges(),
            ]),
            'usuario_id' => Auth::user()->id,
        ]);

        // Redirigir con un mensaje de éxito
        return redirect()->route('incidencias.show', ['id' => $data->idincidencia])->with('success', 'Incidencia actualizada correctamente.');
    }

    public function getAuditoria($id)
    {
        $datas = Incidencias::findorfail($id);
        $auditorias = Auditoria::where('modelo', 'Incidencia')
            ->where('modelo_id', $id)
            ->with('usuario') 
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
            'estado_id' => 'required|exists:estadoincidencia,idestadoincidencia',
        ]);

        $incidencia = Incidencias::findorfail($id);
        $incidencia->estadoincidencia_idestadoincidencia = $request->estado_id;

        $original = $incidencia->getOriginal();

        $estadoNuevo = Estadoincidencia::find($request->estado_id);
        $estadoAnterior = Estadoincidencia::find($original['estadoincidencia_idestadoincidencia']);

        $incidencia->save();

        Auditoria::create([
            'accion' => 'Cambio de estado',
            'modelo' => 'Incidencia',
            'modelo_id' => $incidencia->idincidencia,
            'cambios' => json_encode([
            'estado' => [
                'antes' => $estadoAnterior ? $estadoAnterior->nombre_estadoincidencia ?? $estadoAnterior->descriestadoincidencia : null,
                'despues' => $estadoNuevo ? $estadoNuevo->nombre_estadoincidencia ?? $estadoNuevo->descriestadoincidencia : null,
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

        $incidencia = Incidencias::findorfail($id);
        $incidencia->fecharesolucionincidencia = now();
        $incidencia->save();

        return redirect()->back()->with('success', 'Estado asignado correctamente.');
    }

    public function justshow(string $id)
    {
        $datas = Incidencias::with(['cliente', 'usuario', 'estadoincidencia'])->findorfail($id);

        return view('incidencias.justshow', compact(['datas']));
    
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
