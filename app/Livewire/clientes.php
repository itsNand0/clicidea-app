<?php

namespace App\Livewire;

use App\Models\Cliente as ModelsClientes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Clientes extends Component
{
    use WithPagination;
    public $search = "";
    public $sortField = 'idcliente';
    public $sortDirection = 'asc';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }


    public function updatingSearch()
    {
        $this->resetPage(); // Reinicia la paginación al buscar
    }

    public function render()
    {
        $user = Auth::user();

        // Alternativa robusta: consulta directa a la tabla de roles de Spatie
        $isAdmin = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('roles.name', 'admin users')
            ->exists();

        if ($isAdmin) {
            // El administrador ve todo los registros

            $datas = ModelsClientes::query()
                ->when($this->search !== '', function ($query) {
                    $search = '%' . $this->search . '%';

                    $query->where(function ($q) use ($search) {
                        $q->where('idcliente', 'like', $search)
                          ->orWhere('nombre', 'like', $search);
                    });
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(10);

            return view('livewire.clientes', compact('datas'));
        } else {

           $datas = ModelsClientes::query()
                ->when($this->search !== '', function ($query) {
                    $search = '%' . $this->search . '%';

                    $query->where(function ($q) use ($search) {
                        $q->where('idcliente', 'like', $search)
                          ->orWhere('nombre', 'like', $search);
                    });
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(10);

            return view('livewire.clientes', compact('datas'));
        }
    }
}
