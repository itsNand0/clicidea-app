<?php

namespace App\Http\Controllers;
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // Asegúrate de tener la vista en resources/views/auth/login.blade.php
    }

    public function login(Request $request)
    {
        $credentials = $request->only('name', 'password');

        if (Auth::attempt(['name' => $credentials['name'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            return redirect()->intended('users'); // Cambia a donde quieras redirigir tras login
        }

        return back()->withErrors([
            'name' => 'Las credenciales no coinciden.',
        ])->withInput();
    }
}
