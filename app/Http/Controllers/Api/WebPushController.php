<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WebPushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WebPushController extends Controller
{
    /**
     * Guardar nueva suscripción web push
     */
    public function subscribe(Request $request)
    {
        try {
            Log::info('🔔 Iniciando suscripción web push', [
                'user_agent' => $request->header('User-Agent'),
                'ip' => $request->ip(),
                'data_received' => $request->all()
            ]);

            $request->validate([
                'endpoint' => 'required|url',
                'keys.p256dh' => 'required|string',
                'keys.auth' => 'required|string',
            ]);

            Log::info('✅ Validación pasada correctamente');

            $user = Auth::user();
            
            Log::info('🔐 Usuario autenticado', [
                'user_id' => $user?->id,
                'user_email' => $user?->email
            ]);
            
            if (!$user) {
                Log::warning('❌ Usuario no autenticado');
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }

            // Crear o actualizar suscripción
            $subscription = WebPushSubscription::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'endpoint' => $request->endpoint
                ],
                [
                    'public_key' => $request->input('keys.p256dh'),
                    'auth_token' => $request->input('keys.auth'),
                    'content_encoding' => $request->input('contentEncoding', 'aesgcm'),
                    'subscription_data' => $request->all(),
                    'user_agent' => $request->header('User-Agent'),
                    'is_active' => true,
                    'last_used_at' => now()
                ]
            );

            Log::info('Suscripción web push creada/actualizada', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'endpoint' => substr($request->endpoint, 0, 50) . '...'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Suscripción registrada exitosamente',
                'subscription_id' => $subscription->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error al crear suscripción web push: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al registrar suscripción'
            ], 500);
        }
    }

    /**
     * Eliminar suscripción web push
     */
    public function unsubscribe(Request $request)
    {
        try {
            $request->validate([
                'endpoint' => 'required|url'
            ]);

            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }

            $deleted = WebPushSubscription::where('user_id', $user->id)
                ->where('endpoint', $request->endpoint)
                ->delete();

            if ($deleted) {
                Log::info('Suscripción web push eliminada', [
                    'user_id' => $user->id,
                    'endpoint' => substr($request->endpoint, 0, 50) . '...'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Suscripción eliminada exitosamente'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Suscripción no encontrada'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error al eliminar suscripción web push: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar suscripción'
            ], 500);
        }
    }

    /**
     * Probar notificación push
     */
    public function testPush(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }

            $subscriptions = WebPushSubscription::where('user_id', $user->id)
                ->active()
                ->count();

            if ($subscriptions === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suscripciones activas para este usuario'
                ]);
            }

            // Crear notificación simple para prueba
            return response()->json([
                'success' => true,
                'message' => "Usuario tiene {$subscriptions} suscripción(es) activa(s)",
                'subscriptions_count' => $subscriptions
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar notificación de prueba: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al enviar notificación de prueba'
            ], 500);
        }
    }

    /**
     * Obtener estado de suscripciones del usuario
     */
    public function status()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }

            $subscriptions = WebPushSubscription::where('user_id', $user->id)
                ->select('id', 'endpoint', 'is_active', 'last_used_at', 'created_at')
                ->get()
                ->map(function ($sub) {
                    return [
                        'id' => $sub->id,
                        'endpoint_preview' => substr($sub->endpoint, 0, 50) . '...',
                        'is_active' => $sub->is_active,
                        'last_used' => $sub->last_used_at?->diffForHumans(),
                        'created' => $sub->created_at->diffForHumans()
                    ];
                });

            return response()->json([
                'success' => true,
                'subscriptions' => $subscriptions,
                'total' => $subscriptions->count(),
                'active' => $subscriptions->where('is_active', true)->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener estado de suscripciones: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener estado'
            ], 500);
        }
    }

    /**
     * Obtener clave pública VAPID
     */
    public function getVapidKey()
    {
        try {
            $publicKey = config('webpush.vapid.public_key');
            
            if (!$publicKey) {
                return response()->json([
                    'success' => false,
                    'error' => 'Clave VAPID no configurada'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'public_key' => $publicKey
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener clave VAPID: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener clave VAPID'
            ], 500);
        }
    }

    /**
     * Test simple para debuggear
     */
    public function testSimple()
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Controlador funcionando correctamente',
                'timestamp' => now()->toISOString(),
                'webpush_config' => [
                    'public_key_exists' => !empty(config('webpush.vapid.public_key')),
                    'table_name' => config('webpush.table_name'),
                    'model_class' => config('webpush.model')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error en test simple: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error en test simple: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Suscripción pública para testing (sin autenticación)
     */
    public function subscribePublic(Request $request)
    {
        try {
            Log::info('🔔 TEST: Iniciando suscripción pública', [
                'data_received' => $request->all()
            ]);

            $request->validate([
                'endpoint' => 'required|url',
                'keys.p256dh' => 'required|string',
                'keys.auth' => 'required|string',
            ]);

            Log::info('✅ TEST: Validación pasada');

            // Para testing, usar específicamente al usuario con ID 2
            $targetUser = \App\Models\User::find(2);
            
            if (!$targetUser) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario con ID 2 no encontrado en la base de datos'
                ], 400);
            }

            Log::info('👤 TEST: Usando usuario ID 2 para testing', [
                'user_id' => $targetUser->id,
                'user_name' => $targetUser->name,
                'user_email' => $targetUser->email
            ]);

            // Crear o actualizar suscripción
            $subscription = WebPushSubscription::updateOrCreate(
                [
                    'user_id' => $targetUser->id,
                    'endpoint' => $request->endpoint
                ],
                [
                    'public_key' => $request->input('keys.p256dh'),
                    'auth_token' => $request->input('keys.auth'),
                    'content_encoding' => $request->input('contentEncoding', 'aesgcm'),
                    'subscription_data' => $request->all(),
                    'user_agent' => $request->header('User-Agent'),
                    'is_active' => true,
                    'last_used_at' => now()
                ]
            );

            Log::info('✅ TEST: Suscripción creada/actualizada', [
                'subscription_id' => $subscription->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Suscripción de prueba creada exitosamente para usuario ID 2',
                'subscription_id' => $subscription->id,
                'user_id' => $targetUser->id,
                'user_name' => $targetUser->name,
                'user_email' => $targetUser->email
            ]);

        } catch (\Exception $e) {
            Log::error('❌ TEST: Error en suscripción pública: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error en suscripción: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Probar notificación de incidencia asignada
     */
    public function testIncidenciaNotification(Request $request)
    {
        try {
            // Obtener específicamente al usuario con ID 2
            $user = \App\Models\User::find(2);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario con ID 2 no encontrado'
                ], 400);
            }

            // Verificar si el usuario tiene suscripciones activas
            $subscriptionsCount = WebPushSubscription::where('user_id', $user->id)
                ->where('is_active', true)
                ->count();

            if ($subscriptionsCount === 0) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario ID 2 no tiene suscripciones push activas'
                ], 400);
            }

            // Obtener una incidencia de ejemplo
            $incidencia = \App\Models\Incidencias::first();
            
            if (!$incidencia) {
                return response()->json([
                    'success' => false,
                    'error' => 'No hay incidencias en la base de datos'
                ], 400);
            }

            Log::info('🧪 Enviando notificación de prueba al usuario ID 2', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'incidencia_id' => $incidencia->idincidencia,
                'subscriptions_count' => $subscriptionsCount
            ]);

            // Enviar notificación
            $user->notify(new \App\Notifications\IncidenciaAsignada($incidencia, $user));

            return response()->json([
                'success' => true,
                'message' => 'Notificación de incidencia enviada exitosamente al usuario ID 2',
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'incidencia_id' => $incidencia->idincidencia,
                'subscriptions_count' => $subscriptionsCount
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error enviando notificación de prueba: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error enviando notificación: ' . $e->getMessage()
            ], 500);
        }
    }
}
