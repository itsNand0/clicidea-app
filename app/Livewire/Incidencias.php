<?php

namespace App\Livewire;

use App\Models\Incidencias as ModelsIncidencias;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Incidencias extends Component
{
    use WithPagination; // Habilita la paginación en Livewire
    public $search = ""; // Variable para el campo de búsqueda

    public function updatingSearch()
    {
        $this->resetPage(); // Reinicia la paginación al buscar
    }

    public function render()
    {
        $user = Auth::user();
        $userAreaId = $user->area_id;
        $userCargoId = $user->cargo_id;

        // Alternativa robusta: consulta directa a la tabla de roles de Spatie
        $isAdmin = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('roles.name', 'admin users')
            ->exists();

        if ($isAdmin) {
            // El administrador ve todo los registros
            $datas = ModelsIncidencias::with(['cliente', 'estadoincidencia', 'usuario'])
                ->when($this->search !== '', function ($query) {
                    $search = '%' . $this->search . '%';

                    $query->where(function ($q) use ($search) {
                        $q->whereHas('estadoincidencia', function ($q) use ($search) {
                            $q->where('descriEstadoIncidencia', 'like', $search);
                        })
                            ->orWhereHas('cliente', function ($q) use ($search) {
                                $q->where('nombre', 'like', $search);
                            })
                            ->orWhereHas('usuario', function ($q) use ($search) {
                                $q->where('name', 'like', $search);
                                $q->orWhereHas('cargo', function ($q) use ($search) {
                                    $q->where('nombre_cargo', 'like', $search);
                                });
                                $q->orWhereHas('area', function ($q) use ($search) {
                                    $q->where('area_name', 'like', $search);
                                });
                            })
                            ->orWhere('usuarioIncidencia', 'like', $search);
                    });
                })
                ->paginate(10);

            return view('livewire.incidencias', compact('datas'));
        } else {

            // Obtener los cargos relacionados al área del usuario (por si es encargado de área)
            $cargoIdsRelacionados = $user->area?->cargos()->pluck('cargos.id') ?? collect();

            // Definir los nombres de cargo y área del usuario actual
            $userAreaId = $user->area_id;
            $userCargoId = $user->cargo_id;

            $datas = ModelsIncidencias::with(['cliente', 'estadoincidencia', 'usuario'])
                ->when(
                    $cargoIdsRelacionados->isNotEmpty(),
                    function ($query) use ($userAreaId, $cargoIdsRelacionados) {
                        $query->whereHas('usuario', function ($q) use ($userAreaId, $cargoIdsRelacionados) {
                            $q->where('area_id', $userAreaId)
                                ->orWhereIn('cargo_id', $cargoIdsRelacionados);
                        });
                    },
                    // Si NO tiene cargos relacionados => usuario normal
                    function ($query) use ($user) {
                        $query->where('Usuario_idUsuario', $user->id);
                    }
                )

                ->when($this->search != '', function ($query) {
                    $search = '%' . $this->search . '%';

                    $query->where(function ($q) use ($search) {
                        $q->whereHas('estadoincidencia', function ($q) use ($search) {
                            $q->where('descriEstadoIncidencia', 'like', $search);
                        })
                            ->orWhereHas('cliente', function ($q) use ($search) {
                                $q->where('nombre', 'like', $search);
                            })

                            ->orWhereHas('usuario', function ($q) use ($search) {
                                $q->where('name', 'like', $search);
                                $q->orWhereHas('cargo', function ($q) use ($search) {
                                    $q->where('nombre_cargo', 'like', $search);
                                });
                                $q->orWhereHas('area', function ($q) use ($search) {
                                    $q->where('area_name', 'like', $search);
                                });
                            })
                            ->orWhere('usuarioIncidencia', 'like', $search);
                    });
                })
                ->paginate(10);

            return view('livewire.incidencias', compact('datas'));
        }
    }
}
