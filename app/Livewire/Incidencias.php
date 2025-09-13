<?php

namespace App\Livewire;

use App\Models\Incidencias as ModelsIncidencias;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Incidencias extends Component
{
    use WithPagination;
    public $search = "";
    public $sortField = 'fechaincidencia';
    public $sortDirection = 'desc';
    public $showClosed = false; // Filtro para mostrar estados cerrados
    public $showTodayOnly = false; // Filtro para mostrar solo incidencias del día
    public $showActiveOnly = true; // Filtro para mostrar solo pendientes/en proceso
    public $dateFrom = ''; // Fecha desde para filtro personalizado
    public $dateTo = ''; // Fecha hasta para filtro personalizado

    public $visibleColumns = [
        'usuario' => true,
        'usuarioincidencia' => true,
        'estado' => true,
        'contrato' => true,
        'asunto' => true,
        'descripcion' => true,
        'contacto' => true,
        'fecha' => true,
        'resolucion' => true,
        'acciones' => true,
    ];

    public function toggleColumn($column)
    {
        if (array_key_exists($column, $this->visibleColumns)) {
            $this->visibleColumns[$column] = !$this->visibleColumns[$column];
        }
    }

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

    public function updatingShowClosed()
    {
        $this->resetPage(); // Reinicia la paginación al cambiar filtro de cerrados
    }

    public function updatingShowTodayOnly()
    {
        if ($this->showTodayOnly) {
            // Limpiar filtros de fecha personalizada cuando se activa "Solo hoy"
            $this->dateFrom = '';
            $this->dateTo = '';
        }
        $this->resetPage(); // Reinicia la paginación al cambiar filtro del día
    }

    public function updatingShowActiveOnly()
    {
        $this->resetPage(); // Reinicia la paginación al cambiar filtro de activos
    }

    public function updatingDateFrom()
    {
        if ($this->dateFrom) {
            // Desactivar "Solo hoy" cuando se establece una fecha personalizada
            $this->showTodayOnly = false;
        }
        $this->resetPage(); // Reinicia la paginación al cambiar fecha desde
    }

    public function updatingDateTo()
    {
        if ($this->dateTo) {
            // Desactivar "Solo hoy" cuando se establece una fecha personalizada
            $this->showTodayOnly = false;
        }
        $this->resetPage(); // Reinicia la paginación al cambiar fecha hasta
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->showClosed = false;
        $this->showTodayOnly = false;
        $this->showActiveOnly = true;
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
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
            ->where('roles.name', 'admin')
            ->exists();

        if ($isAdmin) {
            // El administrador ve todo los registros

            $datas = ModelsIncidencias::with(['cliente', 'estadoincidencia', 'usuario'])
                ->when($this->search !== '', function ($query) {
                    $searchTerm = $this->search;

                    $query->where(function ($q) use ($searchTerm) {
                        $q->whereHas('estadoincidencia', function ($q) use ($searchTerm) {
                            $q->where('descriestadoincidencia', 'ILIKE', '%' . $searchTerm . '%');
                        })
                            ->orWhereHas('cliente', function ($q) use ($searchTerm) {
                                $q->where('nombre', 'ILIKE', '%' . $searchTerm . '%');
                            })
                            ->orWhereHas('usuario', function ($q) use ($searchTerm) {
                                $q->where('name', 'ILIKE', '%' . $searchTerm . '%')
                                    ->orWhereHas('cargo', function ($q) use ($searchTerm) {
                                        $q->where('nombre_cargo', 'ILIKE', '%' . $searchTerm . '%');
                                    })
                                    ->orWhereHas('area', function ($q) use ($searchTerm) {
                                        $q->where('area_name', 'ILIKE', '%' . $searchTerm . '%');
                                    });
                            })
                            ->orWhere('usuarioincidencia', 'ILIKE', '%' . $searchTerm . '%')
                            ->orWhere('idincidencia', 'LIKE', '%' . $searchTerm . '%');
                    });
                })
                ->when($this->showTodayOnly, function ($query) {
                    // Filtrar solo incidencias del día actual
                    $query->whereDate('fechaincidencia', today());
                })
                ->when($this->showActiveOnly && !$this->showClosed, function ($query) {
                    // Filtrar solo estados pendientes o en proceso (solo si no se muestran cerrados)
                    $query->whereHas('estadoincidencia', function ($q) {
                        $q->where('descriestadoincidencia', 'ILIKE', '%Pendiente%')
                          ->orWhere('descriestadoincidencia', 'ILIKE', '%En proceso%');
                    });
                })
                ->when($this->dateFrom && $this->dateTo, function ($query) {
                    // Filtrar por rango de fechas personalizado
                    if ($this->dateFrom === $this->dateTo) {
                        // Si las fechas son iguales, filtrar solo por ese día
                        $query->whereDate('fechaincidencia', $this->dateFrom);
                    } else {
                        // Si son diferentes, usar rango between
                        $query->whereBetween('fechaincidencia', [$this->dateFrom, $this->dateTo]);
                    }
                })
                ->when($this->dateFrom && !$this->dateTo, function ($query) {
                    // Solo fecha desde
                    $query->whereDate('fechaincidencia', '>=', $this->dateFrom);
                })
                ->when(!$this->dateFrom && $this->dateTo, function ($query) {
                    // Solo fecha hasta
                    $query->whereDate('fechaincidencia', '<=', $this->dateTo);
                })
                ->when(!$this->showClosed, function ($query) {
                    // Filtrar estados cerrados cuando showClosed es false
                    $query->whereHas('estadoincidencia', function ($q) {
                        $q->where('descriestadoincidencia', 'NOT ILIKE', '%Cerrado%');
                    });
                })
                ->orderBy($this->sortField, $this->sortDirection)
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
                        $query->where('usuario_idusuario', $user->id);
                    }
                )

                ->when($this->search != '', function ($query) {
                    $searchTerm = $this->search;

                    $query->where(function ($q) use ($searchTerm) {
                        $q->whereHas('estadoincidencia', function ($q) use ($searchTerm) {
                            $q->where('descriestadoincidencia', 'ILIKE', '%' . $searchTerm . '%');
                        })
                            ->orWhereHas('cliente', function ($q) use ($searchTerm) {
                                $q->where('nombre', 'ILIKE', '%' . $searchTerm . '%');
                            })

                            ->orWhereHas('usuario', function ($q) use ($searchTerm) {
                                $q->where('name', 'ILIKE', '%' . $searchTerm . '%')
                                    ->orWhereHas('cargo', function ($q) use ($searchTerm) {
                                        $q->where('nombre_cargo', 'ILIKE', '%' . $searchTerm . '%');
                                    })
                                    ->orWhereHas('area', function ($q) use ($searchTerm) {
                                        $q->where('area_name', 'ILIKE', '%' . $searchTerm . '%');
                                    });
                            })
                            ->orWhere('usuarioincidencia', 'ILIKE', '%' . $searchTerm . '%');
                    });
                })
                ->when($this->showTodayOnly, function ($query) {
                    // Filtrar solo incidencias del día actual
                    $query->whereDate('fechaincidencia', today());
                })
                ->when($this->showActiveOnly && !$this->showClosed, function ($query) {
                    // Filtrar solo estados pendientes o en proceso (solo si no se muestran cerrados)
                    $query->whereHas('estadoincidencia', function ($q) {
                        $q->where('descriestadoincidencia', 'ILIKE', '%Pendiente%')
                          ->orWhere('descriestadoincidencia', 'ILIKE', '%En proceso%');
                    });
                })
                ->when($this->dateFrom && $this->dateTo, function ($query) {
                    // Filtrar por rango de fechas personalizado
                    if ($this->dateFrom === $this->dateTo) {
                        // Si las fechas son iguales, filtrar solo por ese día
                        $query->whereDate('fechaincidencia', $this->dateFrom);
                    } else {
                        // Si son diferentes, usar rango between
                        $query->whereBetween('fechaincidencia', [$this->dateFrom, $this->dateTo]);
                    }
                })
                ->when($this->dateFrom && !$this->dateTo, function ($query) {
                    // Solo fecha desde
                    $query->whereDate('fechaincidencia', '>=', $this->dateFrom);
                })
                ->when(!$this->dateFrom && $this->dateTo, function ($query) {
                    // Solo fecha hasta
                    $query->whereDate('fechaincidencia', '<=', $this->dateTo);
                })
                ->when(!$this->showClosed, function ($query) {
                    // Filtrar estados cerrados cuando showClosed es false
                    $query->whereHas('estadoincidencia', function ($q) {
                        $q->where('descriestadoincidencia', 'NOT ILIKE', '%Cerrado%');
                    });
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(10);

            return view('livewire.incidencias', compact('datas'));
        }
    }
}
