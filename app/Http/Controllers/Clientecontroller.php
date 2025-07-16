<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Clientecontroller extends Controller
{
    public function index()
    {
        // Aquí puedes implementar la lógica para mostrar los clientes
        return view('clientes.index');
    }
}
