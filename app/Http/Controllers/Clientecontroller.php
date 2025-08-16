<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Clientecontroller extends Controller
{
    public function index()
    {
        // Aquí puedes implementar la lógica para mostrar los clientes
        return view('clientes.index');
    }

    public function exportarExcelCliente()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados en negrita
        $sheet->fromArray(['idcliente', 'nombre', 'zona', 'atm_id'], NULL, 'A1');
        // Aplicar negrita a la fila de encabezados
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        $clientes = Cliente::all();
        $row = 2;

        foreach ($clientes as $cliente) {
            $sheet->fromArray([
                $cliente->idcliente,
                $cliente->nombre,
                $cliente->zona,
                $cliente->atm_id,
            ], NULL, 'A' . $row);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        // Descargar el archivo
        $filename = 'clientes.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $writer->save("php://output");
        exit;
    }
}
