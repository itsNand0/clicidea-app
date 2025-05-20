<?php

namespace App\Livewire;

use App\Models\Incidencias as ModelsIncidencias;
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
        $datas = ModelsIncidencias::with(['cliente', 'tecnico', 'estadoincidencia', 'area'])
            ->when($this->search != '', function ($query) {
                $search = '%' . $this->search . '%';
                $query->whereHas('estadoincidencia', function ($q) use ($search) {
                    $q->where('descriEstadoIncidencia', 'like', $search);
                })
                ->orWhereHas('cliente', function ($q) use ($search) {
                    $q->where('nombre', 'like', $search);
                })
                ->orWhereHas('tecnico', function ($q) use ($search) {
                    $q->where('nombreTecnico', 'like', $search);
                })
                ->orWhereHas('area', function ($q) use ($search) {
                    $q->where('area_name', 'like', $search);
                })
                ->orWhere('usuarioIncidencia', 'like', $search);
            })
            ->paginate(10);

        return view('livewire.incidencias', compact('datas'));
    }
}

