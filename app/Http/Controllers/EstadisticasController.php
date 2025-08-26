<?php

namespace App\Http\Controllers;

use App\Models\Incidencias;
use App\Models\Estadoincidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstadisticasController extends Controller
{
    public function index()
    {
        // Estadísticas por estado
        $estadisticasPorEstado = Incidencias::with('estadoincidencia')
            ->get()
            ->groupBy('estadoincidencia.descriestadoincidencia')
            ->map(function ($group, $estado) {
                return [
                    'estado' => $estado,
                    'total' => $group->count()
                ];
            })
            ->values();

        // Estadísticas por mes (últimos 6 meses)
        $estadisticasPorMes = Incidencias::select(
                DB::raw('DATE_TRUNC(\'month\', fechaincidencia) as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->where('fechaincidencia', '>=', now()->subMonths(6))
            ->groupBy(DB::raw('DATE_TRUNC(\'month\', fechaincidencia)'))
            ->orderBy(DB::raw('DATE_TRUNC(\'month\', fechaincidencia)'))
            ->get();

        // Estadísticas por usuario (top 5)
        $estadisticasPorUsuario = Incidencias::with('usuario')
            ->whereHas('usuario')
            ->get()
            ->groupBy('usuario.name')
            ->map(function ($group, $usuario) {
            return [
                'usuario' => $usuario,
                'total' => $group->count()
            ];
            })
            ->sortByDesc('total')
            ->take(5)
            ->values();

        // Total de incidencias
        $totalIncidencias = Incidencias::count();

        // Incidencias abiertas (no cerradas)
        $incidenciasAbiertas = Incidencias::whereHas('estadoincidencia', function($query) {
            $query->where('descriestadoincidencia', 'NOT ILIKE', '%cerrado%')
                  ->where('descriestadoincidencia', 'NOT ILIKE', '%cerrada%');
        })->count();

        // Incidencias del mes actual
        $incidenciasMesActual = Incidencias::whereMonth('fechaincidencia', now()->month)
            ->whereYear('fechaincidencia', now()->year)
            ->count();

        return view('estadisticas.index', compact(
            'estadisticasPorEstado',
            'estadisticasPorMes', 
            'estadisticasPorUsuario',
            'totalIncidencias',
            'incidenciasAbiertas',
            'incidenciasMesActual'
        ));
    }
}
