<?php

namespace App\Notifications;

use App\Models\Incidencias;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncidenciaAsignada extends Notification
{
    protected $incidencia;
    protected $asignadoPor;

    /**
     * Create a new notification instance.
     */
    public function __construct(Incidencias $incidencia, User $asignadoPor)
    {
        $this->incidencia = $incidencia;
        $this->asignadoPor = $asignadoPor;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // Agregar email si está configurado
        if (config('mail.default') !== 'log') {
            $channels[] = 'mail';
        }
        
        // 🔔 AGREGAR WEB PUSH CHANNEL
        $channels[] = \App\Notifications\Channels\WebPushChannel::class;
        
        // Agregar push si el usuario tiene token FCM
        if ($notifiable->fcm_token) {
            $channels[] = 'fcm'; // Requiere paquete FCM
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('incidencias.show', $this->incidencia->idincidencia);
        
        return (new MailMessage)
            ->subject('🎯 Nueva Incidencia Asignada #' . $this->incidencia->idincidencia)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Se te ha asignado una nueva incidencia.')
            ->line('**Detalles de la incidencia:**')
            ->line('📋 **Asunto:** ' . $this->incidencia->asuntoincidencia)
            ->line('🏢 **Cliente:** ' . ($this->incidencia->cliente->nombrecliente ?? 'No especificado'))
            ->line('📅 **Fecha:** ' . \Carbon\Carbon::parse($this->incidencia->fechaincidencia)->format('d/m/Y H:i'))
            ->line('👤 **Asignado por:** ' . $this->asignadoPor->name)
            ->line('📝 **Descripción:** ' . substr($this->incidencia->descrincidencia, 0, 100) . '...')
            ->action('🔍 Ver Incidencia Completa', $url)
            ->line('Te recomendamos revisar y actualizar el estado de la incidencia lo antes posible.')
            ->line('¡Gracias por tu dedicación al equipo!')
            ->salutation('Saludos,  
El equipo de ' . config('app.name'));
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'incidencia_id' => $this->incidencia->idincidencia,
            'titulo' => 'Nueva incidencia asignada',
            'mensaje' => 'Se te ha asignado la incidencia: ' . $this->incidencia->asuntoincidencia,
            'asunto' => $this->incidencia->asuntoincidencia,
            'cliente' => $this->incidencia->cliente->nombrecliente ?? 'No especificado',
            'asignado_por' => $this->asignadoPor->name,
            'asignado_por_id' => $this->asignadoPor->id,
            'fecha_asignacion' => now()->toDateTimeString(),
            'tipo' => 'asignacion',
            'icono' => 'fa-solid fa-user-tag',
            'color' => 'blue',
            'url' => route('incidencias.show', $this->incidencia->idincidencia)
        ];
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush(object $notifiable): array
    {
        return [
            'title' => '🎯 Nueva Incidencia Asignada',
            'body' => "#{$this->incidencia->idincidencia}: {$this->incidencia->asuntoincidencia}",
            'icon' => '/images/lateral01.png',
            'badge' => '/images/lateral01.png',
            'tag' => 'incidencia-' . $this->incidencia->idincidencia,
            'data' => [
                'incidencia_id' => $this->incidencia->idincidencia,
                'url' => route('incidencias.show', $this->incidencia->idincidencia),
                'cliente' => $this->incidencia->cliente->nombre ?? 'No especificado',
                'asignado_por' => $this->asignadoPor->name,
                'timestamp' => now()->toISOString()
            ],
            'actions' => [
                [
                    'action' => 'view',
                    'title' => 'Ver Incidencia',
                    'icon' => '/images/view-icon.png'
                ],
                [
                    'action' => 'close',
                    'title' => 'Cerrar',
                    'icon' => '/images/close-icon.png'
                ]
            ],
            'requireInteraction' => true, // Requiere interacción del usuario
            'vibrate' => [200, 100, 200] // Patrón de vibración
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
