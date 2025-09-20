<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ClienteController extends Controller
{
    public function index()
    {
        // Aquí puedes implementar la lógica para mostrar los clientes
        return view('clientes.index');
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        try {
            // Validar los datos del formulario
            $validated = $request->validate([
                'atm_id' => 'required|integer|min:1|unique:cliente,atm_id',
                'nombre' => 'required|string|max:255',
                'zona' => 'nullable|string|max:255',
            ], [
                'atm_id.required' => 'El ATM ID es obligatorio.',
                'atm_id.integer' => 'El ATM ID debe ser un número entero.',
                'atm_id.min' => 'El ATM ID debe ser mayor a 0.',
                'atm_id.unique' => 'Ya existe un cliente con este ATM ID.',
                'nombre.required' => 'El nombre del cliente es obligatorio.',
                'nombre.string' => 'El nombre debe ser texto válido.',
                'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
                'zona.string' => 'La zona debe ser texto válido.',
                'zona.max' => 'La zona no puede exceder 255 caracteres.',
            ]);

            // Crear el nuevo cliente
            $cliente = Cliente::create([
                'atm_id' => $validated['atm_id'],
                'nombre' => $validated['nombre'],
                'zona' => $validated['zona'] ?? null,
            ]);

            // Redireccionar con mensaje de éxito
            return redirect()->route('clientes.index')
                ->with('success', "Cliente '{$cliente->nombre}' creado exitosamente con ATM ID: {$cliente->atm_id}");

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Los errores de validación se manejan automáticamente
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            // Error inesperado
            return redirect()->back()
                ->with('error', 'Ocurrió un error al crear el cliente: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.edit', compact('cliente'));
    }

    public function destroy($id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            $cliente->delete();

            return redirect()->route('clientes.index')
                ->with('success', "Cliente '{$cliente->nombre}' eliminado exitosamente.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Ocurrió un error al eliminar el cliente: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validar los datos del formulario
            $validated = $request->validate([
                'atm_id' => 'required|integer|min:1|unique:cliente,atm_id,' . $id . ',idcliente',
                'nombre' => 'required|string|max:255',
                'zona' => 'nullable|string|max:255',
            ], [
                'atm_id.required' => 'El ATM ID es obligatorio.',
                'atm_id.integer' => 'El ATM ID debe ser un número entero.',
                'atm_id.min' => 'El ATM ID debe ser mayor a 0.',
                'atm_id.unique' => 'Ya existe un cliente con este ATM ID.',
                'nombre.required' => 'El nombre del cliente es obligatorio.',
                'nombre.string' => 'El nombre debe ser texto válido.',
                'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
                'zona.string' => 'La zona debe ser texto válido.',
                'zona.max' => 'La zona no puede exceder 255 caracteres.',
            ]);

            // Actualizar el cliente
            $cliente = Cliente::findOrFail($id);
            $cliente->update([
                'atm_id' => $validated['atm_id'],
                'nombre' => $validated['nombre'],
                'zona' => $validated['zona'] ?? null,
            ]);

            // Redireccionar con mensaje de éxito
            return redirect()->route('clientes.index')
                ->with('success', "Cliente '{$cliente->nombre}' actualizado exitosamente con ATM ID: {$cliente->atm_id}");

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Los errores de validación se manejan automáticamente
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            // Error inesperado
            return redirect()->back()
                ->with('error', 'Ocurrió un error al actualizar el cliente: ' . $e->getMessage())
                ->withInput();
        }
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
