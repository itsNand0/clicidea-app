<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Incidencias;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function importSedes(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        try {
            foreach ($sheetData as $index => $row) {
                if ($index === 0) {
                    continue;
                }
                //$atm_id = $row[1] ?? null; // Asignar null si no hay valor
                $nombresede = $row[1] ?? null;
                $zona = $row[0] ?? null;

                Cliente::create([
                    //'atm_id' => $atm_id,
                    'nombre' => $nombresede,
                    'zona' => $zona,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error al procesar el archivo: ' . $e->getMessage());
            return back()->withErrors('Ocurrió un error al procesar el archivo.');
        }

        return back()->with('success', 'Datos de sedes importados correctamente.');
    }

    public function importincidencias(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        try {
            foreach ($sheetData as $index => $row) {
                if ($index === 0) {
                    continue;
                }
                $incidenciaid = $row[0];
                $estadoid = $row[1] ?? null;
                $clienteid = $row[2] ?? null;
                $usuarioid = $row[3] ?? null;
                $creadorid = $row[4] ?? null;
                $asunto = $row[5] ?? null;
                $contacto = $row[6] ?? null;
                $descripcion = $row[7] ?? null;
                $fechacreacion = $row[8] ?? null;
                $fecharesolucion = $row[9] ?? null;

                if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}/', $fechacreacion)) {
                    // Formato MM/DD/YYYY
                    $fechacreacion = Carbon::createFromFormat('m/d/Y h:i A', $fechacreacion)->toDateTimeString();
                } else {
                    // Formato DD/MM/YYYY
                    $fechacreacion = Carbon::createFromFormat('d/m/Y h:i A', $fechacreacion)->toDateTimeString();
                }

                if (empty($fecharesolucion)) {
                    $fecharesolucion = null; // O asignar un valor predeterminado si es necesario
                } else if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}/', $fecharesolucion)) {
                    // Formato MM/DD/YYYY
                    $fecharesolucion = Carbon::createFromFormat('m/d/Y h:i A', $fecharesolucion)->toDateTimeString();
                } else {
                    // Formato DD/MM/YYYY
                    $fecharesolucion = Carbon::createFromFormat('d/m/Y h:i A', $fecharesolucion)->toDateTimeString();
                }

                Incidencias::create([
                    'idincidencia' => $incidenciaid,
                    'estadoincidencia_idestadoincidencia' => $estadoid,
                    'cliente_idcliente' => $clienteid,
                    'usuario_idusuario' => $usuarioid,
                    'usuarioincidencia' => $creadorid,
                    'asuntoincidencia' => $asunto,
                    'contactoincidencia' => $contacto,
                    'descriincidencia' => $descripcion,
                    'fechaincidencia' => $fechacreacion,
                    'fecharesolucionincidencia' => $fecharesolucion,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error al procesar el archivo: ' . $e->getMessage());
            return back()->withErrors('Ocurrió un error al procesar el archivo.');
        }

        return back()->with('success', 'Datos de incidencias importados correctamente.');
    }
}
