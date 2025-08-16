<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
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
}
