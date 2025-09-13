<?php

namespace App\Notifications\Channels;

use App\Models\WebPushSubscription;
use Illuminate\Notifications\Notification;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class WebPushChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        // Obtener datos de la notificación
        $data = $notification->toWebPush($notifiable);
        
        // Obtener todas las suscripciones activas del usuario
        $subscriptions = $this->getUserSubscriptions($notifiable);
        
        if (empty($subscriptions)) {
            return;
        }
        
        // Enviar a cada suscripción
        foreach ($subscriptions as $subscription) {
            $this->sendPushNotification($subscription, $data);
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
            // Configurar WebPush con claves VAPID
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => config('app.url'),
                    'publicKey' => config('webpush.vapid.public_key'),
                    'privateKey' => config('webpush.vapid.private_key'),
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

            // Enviar notificación
            $result = $webPush->sendOneNotification(
                $subscription,
                $payload
            );

            if (!$result->isSuccess() && $result->isSubscriptionExpired()) {
                // Si la suscripción expiró, marcarla como inactiva
                WebPushSubscription::where('endpoint', $subscriptionData['endpoint'])
                    ->update(['is_active' => false]);
            }
            
        } catch (\Exception $e) {
            // Error silenciado para producción
        }
    }
}
