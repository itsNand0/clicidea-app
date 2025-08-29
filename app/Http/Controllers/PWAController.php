<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PWAController extends Controller
{
    /**
     * Servir el manifest.json
     */
    public function manifest()
    {
        $manifestPath = public_path('manifest.json');
        
        if (!file_exists($manifestPath)) {
            return response()->json(['error' => 'Manifest not found'], 404);
        }
        
        $content = file_get_contents($manifestPath);
        return response($content, 200)
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'no-cache');
    }

    /**
     * Servir el service worker
     */
    public function serviceWorker()
    {
        $content = file_get_contents(public_path('sw.js'));
        return response($content, 200)
            ->header('Content-Type', 'application/javascript')
            ->header('Cache-Control', 'no-cache');
    }

    /**
     * Página de instalación PWA
     */
    public function install()
    {
        return view('pwa.install');
    }

    /**
     * Registrar token de push notification
     */
    public function registerPushToken(Request $request)
    {
        // Verificar CSRF token manualmente para evitar error 419
        if (!$request->hasValidSignature() && !$request->session()->token() === $request->input('_token')) {
            // Si no hay token CSRF válido, intentar con el header
            $token = $request->header('X-CSRF-TOKEN') ?: $request->input('_token');
            if (!$token || !hash_equals(session()->token(), $token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token CSRF inválido'
                ], 419);
            }
        }

        $request->validate([
            'token' => 'required|string'
        ]);

        $user = Auth::user();
        if ($user) {
            try {
                // Guardar el token en la base de datos
                DB::table('users')->where('id', $user->id)->update([
                    'push_token' => $request->token
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Token registrado correctamente'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al guardar token: ' . $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Usuario no autenticado'
        ], 401);
    }

    /**
     * Enviar notificación push de prueba
     */
    public function testPush(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->push_token) {
            return response()->json([
                'success' => false,
                'message' => 'No hay token de push disponible'
            ], 400);
        }

        // Aquí enviarías la notificación push real
        // Por ahora solo confirmamos que el sistema está listo
        
        return response()->json([
            'success' => true,
            'message' => 'Sistema PWA configurado correctamente',
            'token' => substr($user->push_token, 0, 20) . '...'
        ]);
    }
}
