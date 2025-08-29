<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); 
    }

    public function login(Request $request)
    {
        $credentials = $request->only('usuario', 'password');

        if (Auth::attempt(['usuario' => $credentials['usuario'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            return redirect()->intended(route('view.dashboard')); 
        }

        return back()->withErrors([
            'usuario' => 'Las credenciales no coinciden.',
        ])->withInput();
    }
}
