<?php

namespace App\Livewire;

use App\Http\Controllers\Incidenciacontroller;
use App\Models\Cargo;
use App\Models\Incidencias as ModelsIncidencias;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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

        // Obtener los cargos relacionados al área del usuario (por si es encargado de área)
        $cargoIdsRelacionados = $user->area?->cargos()->pluck('cargos.id') ?? collect();

        $datas = ModelsIncidencias::with(['cliente', 'estadoincidencia', 'usuario'])
            ->when(
                $cargoIdsRelacionados->isNotEmpty(), // Si tiene cargos relacionados => encargado de área
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
                        })
                        ->orWhere('usuarioIncidencia', 'like', $search);
                });
            })
            ->paginate(10);

        return view('livewire.incidencias', compact('datas'));
    }
}
