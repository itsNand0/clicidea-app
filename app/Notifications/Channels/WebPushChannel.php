<?php

namespace App\Notifications\Channels;

use App\Models\WebPushSubscription;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class WebPushChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        try {
            Log::info('WebPush: Iniciando envío de notificación', [
                'user_id' => $notifiable->id,
                'notification_class' => get_class($notification)
            ]);

            // Obtener datos de la notificación
            $data = $notification->toWebPush($notifiable);
            
            Log::info('WebPush: Datos de notificación obtenidos', [
                'title' => $data['title'] ?? 'N/A'
            ]);
            
            // Obtener todas las suscripciones activas del usuario
            $subscriptions = $this->getUserSubscriptions($notifiable);
            
            Log::info('WebPush: Suscripciones encontradas', [
                'user_id' => $notifiable->id,
                'count' => count($subscriptions)
            ]);
            
            if (empty($subscriptions)) {
                Log::warning('WebPush: Usuario sin suscripciones activas', [
                    'user_id' => $notifiable->id
                ]);
                return;
            }
            
            // Enviar a cada suscripción
            foreach ($subscriptions as $subscription) {
                $this->sendPushNotification($subscription, $data);
            }
            
            Log::info('WebPush: Notificaciones enviadas exitosamente', [
                'user_id' => $notifiable->id,
                'subscriptions_sent' => count($subscriptions)
            ]);
            
        } catch (\Exception $e) {
            Log::error('WebPush: Error al enviar notificación', [
                'user_id' => $notifiable->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Obtener suscripciones del usuario
     */
    private function getUserSubscriptions($notifiable)
    {
        return WebPushSubscription::where('user_id', $notifiable->id)
            ->where('is_active', true)
            ->get()
            ->map(function ($subscription) {
                return [
                    'endpoint' => $subscription->endpoint,
                    'public_key' => $subscription->public_key,
                    'auth_token' => $subscription->auth_token,
                    'content_encoding' => $subscription->content_encoding ?? 'aesgcm'
                ];
            })
            ->toArray();
    }

    /**
     * Enviar notificación push a una suscripción específica
     */
    private function sendPushNotification($subscriptionData, $data)
    {
        try {
            Log::info('WebPush: Enviando notificación individual', [
                'endpoint' => substr($subscriptionData['endpoint'], 0, 50) . '...',
                'title' => $data['title'] ?? 'N/A'
            ]);

            // Verificar configuración VAPID
            $vapidPublic = config('webpush.vapid.public_key');
            $vapidPrivate = config('webpush.vapid.private_key');
            
            if (!$vapidPublic || !$vapidPrivate) {
                Log::error('WebPush: Claves VAPID no configuradas correctamente');
                return false;
            }

            // Configurar WebPush con claves VAPID
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => config('app.url'),
                    'publicKey' => $vapidPublic,
                    'privateKey' => $vapidPrivate,
                ]
            ]);

            // Crear suscripción para Minishlink/WebPush
            $subscription = Subscription::create([
                'endpoint' => $subscriptionData['endpoint'],
                'keys' => [
                    'p256dh' => $subscriptionData['public_key'],
                    'auth' => $subscriptionData['auth_token']
                ]
            ]);

            // Preparar payload
            $payload = json_encode([
                'title' => $data['title'],
                'body' => $data['body'],
                'icon' => $data['icon'] ?? '/images/lateral01.png',
                'badge' => $data['badge'] ?? '/images/lateral01.png',
                'tag' => $data['tag'] ?? 'default',
                'data' => $data['data'] ?? [],
                'actions' => $data['actions'] ?? [],
                'requireInteraction' => $data['requireInteraction'] ?? false,
                'vibrate' => $data['vibrate'] ?? [200, 100, 200]
            ]);

            Log::info('WebPush: Payload preparado', [
                'payload_size' => strlen($payload),
                'title' => $data['title']
            ]);

            // Enviar notificación
            $result = $webPush->sendOneNotification(
                $subscription,
                $payload
            );

            Log::info('WebPush: Resultado del envío', [
                'success' => $result->isSuccess(),
                'reason' => $result->getReason()
            ]);

            if (!$result->isSuccess()) {
                Log::error('WebPush: Error en el envío', [
                    'reason' => $result->getReason(),
                    'endpoint' => substr($subscriptionData['endpoint'], 0, 50) . '...'
                ]);
                
                if ($result->isSubscriptionExpired()) {
                    Log::warning('WebPush: Suscripción expirada, marcando como inactiva');
                    WebPushSubscription::where('endpoint', $subscriptionData['endpoint'])
                        ->update(['is_active' => false]);
                }
                
                return false;
            }
            
            Log::info('WebPush: Notificación enviada exitosamente');
            return true;
            
        } catch (\Exception $e) {
            Log::error('WebPush: Excepción al enviar notificación', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'endpoint' => substr($subscriptionData['endpoint'] ?? 'unknown', 0, 50) . '...'
            ]);
            return false;
        }
    }
}
