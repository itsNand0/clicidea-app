<?php

namespace App\Http\Controllers;

use App\Models\Incidencias;
use App\Models\Estadoincidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EstadisticasController extends Controller
{
    public function index()
    {
        try {
            // Estadísticas por estado
            $estadisticasPorEstado = Incidencias::with('estadoIncidencia')
                ->get()
                ->filter(function($incidencia) {
                    return $incidencia->estadoIncidencia !== null;
                })
                ->groupBy('estadoIncidencia.descriestadoincidencia')
                ->map(function ($group, $estado) {
                    return [
                        'estado' => $estado ?? 'Sin estado',
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
                ->whereNotNull('fechaincidencia')
                ->groupBy(DB::raw('DATE_TRUNC(\'month\', fechaincidencia)'))
                ->orderBy(DB::raw('DATE_TRUNC(\'month\', fechaincidencia)'))
                ->get();

            // Estadísticas por usuario (top 5)
            $estadisticasPorUsuario = Incidencias::with('usuario')
                ->whereHas('usuario')
                ->get()
                ->filter(function($incidencia) {
                    return $incidencia->usuario !== null;
                })
                ->groupBy('usuario.name')
                ->map(function ($group, $usuario) {
                    return [
                        'usuario' => $usuario ?? 'Usuario desconocido',
                        'total' => $group->count()
                    ];
                })
                ->sortByDesc('total')
                ->take(5)
                ->values();

            // Total de incidencias
            $totalIncidencias = Incidencias::count();

            // Incidencias abiertas (no cerradas)
            $incidenciasAbiertas = Incidencias::whereHas('estadoIncidencia', function($query) {
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

        } catch (\Exception $e) {
            // Log del error para debugging
            Log::error('Error en estadísticas: ' . $e->getMessage());
            
            // Valores por defecto en caso de error
            return view('estadisticas.index', [
                'estadisticasPorEstado' => collect([]),
                'estadisticasPorMes' => collect([]), 
                'estadisticasPorUsuario' => collect([]),
                'totalIncidencias' => 0,
                'incidenciasAbiertas' => 0,
                'incidenciasMesActual' => 0
            ]);
        }
    }
}
