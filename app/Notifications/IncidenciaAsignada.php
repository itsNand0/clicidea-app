<?php

namespace App\Notifications;

use App\Models\Incidencias;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncidenciaAsignada extends Notification implements ShouldQueue
{
    use Queueable;

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
        $url = route('incidencias.show', $this->incidencia->idincidencias);
        
        return (new MailMessage)
            ->subject('🎯 Nueva Incidencia Asignada #' . $this->incidencia->idincidencias)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Se te ha asignado una nueva incidencia.')
            ->line('**Detalles de la incidencia:**')
            ->line('📋 **Asunto:** ' . $this->incidencia->asunto)
            ->line('🏢 **Cliente:** ' . $this->incidencia->cliente->nombre ?? 'No especificado')
            ->line('📅 **Fecha:** ' . $this->incidencia->fechareporte->format('d/m/Y H:i'))
            ->line('👤 **Asignado por:** ' . $this->asignadoPor->name)
            ->line('📝 **Descripción:** ' . substr($this->incidencia->descripcion, 0, 100) . '...')
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
            'mensaje' => 'Se te ha asignado la incidencia: ' . $this->incidencia->asunto,
            'asunto' => $this->incidencia->asunto,
            'asignado_por' => $this->asignadoPor->name,
            'tipo' => 'asignacion',
            'icono' => 'fa-solid fa-user-tag',
            'color' => 'blue'
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
