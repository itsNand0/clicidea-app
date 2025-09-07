<?php

namespace App\Notifications;

use App\Models\Incidencias;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncidenciaResuelta extends Notification implements ShouldQueue
{
    use Queueable;

    protected $incidencia;
    protected $resueltaPor;

    public function __construct(Incidencias $incidencia, User $resueltaPor)
    {
        $this->incidencia = $incidencia;
        $this->resueltaPor = $resueltaPor;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('incidencias.show', $this->incidencia->idincidencia);
        
        return (new MailMessage)
            ->subject('✅ Incidencia Resuelta #' . $this->incidencia->idincidencia)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('¡Excelente noticia! Se ha resuelto una incidencia.')
            ->line('**Detalles de la incidencia:**')
            ->line('📋 **Asunto:** ' . $this->incidencia->asuntoincidencia)
            ->line('🏢 **Cliente:** ' . ($this->incidencia->cliente->nombrecliente ?? 'No especificado'))
            ->line('👤 **Resuelta por:** ' . $this->resueltaPor->name)
            ->line('📅 **Fecha de resolución:** ' . Carbon::parse($this->incidencia->fecharesolucionincidencia)->format('d/m/Y H:i'))
            ->action('🔍 Ver Incidencia Resuelta', $url)
            ->line('¡Gracias por tu excelente trabajo!')
            ->salutation('Saludos,  
El equipo de ' . config('app.name'));
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'incidencia_id' => $this->incidencia->idincidencia,
            'titulo' => 'Incidencia resuelta',
            'mensaje' => 'Se resolvió la incidencia: ' . $this->incidencia->asuntoincidencia,
            'asunto' => $this->incidencia->asuntoincidencia,
            'cliente' => $this->incidencia->cliente->nombrecliente ?? 'No especificado',
            'resuelto_por' => $this->resueltaPor->name,
            'resuelto_por_id' => $this->resueltaPor->id,
            'fecha_resolucion' => $this->incidencia->fecharesolucionincidencia,
            'tipo' => 'resolucion',
            'icono' => 'fa-solid fa-check-circle',
            'color' => 'green',
            'url' => route('incidencias.show', $this->incidencia->idincidencia)
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
