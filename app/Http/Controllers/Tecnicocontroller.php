<?php

namespace App\Http\Controllers;

use App\Models\Tecnico;
use Illuminate\Http\Request;

class Tecnicocontroller extends Controller
{
    public function mostrarForm()
    {
        // Obtener todos los técnicos desde la base de datos
        $datatecnicos = Tecnico::all();  // O cualquier consulta que necesites

        return view('incidencias.show', compact('datatecnicos'));  // Pasar la lista de técnicos a la vista
    }
}
