<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TestWebPushNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return [\App\Notifications\Channels\WebPushChannel::class];
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush(object $notifiable): array
    {
        return [
            'title' => '🧪 Test - ClicIdea',
            'body' => 'Sistema de notificaciones push funcionando correctamente!',
            'icon' => '/images/lateral01.png',
            'badge' => '/images/lateral01.png',
            'tag' => 'test-notification',
            'data' => [
                'test' => true,
                'timestamp' => now()->toISOString(),
                'url' => route('dashboard')
            ],
            'actions' => [
                [
                    'action' => 'view',
                    'title' => 'Ver Dashboard',
                    'icon' => '/images/view-icon.png'
                ]
            ],
            'requireInteraction' => false,
            'vibrate' => [100, 50, 100]
        ];
    }
}
