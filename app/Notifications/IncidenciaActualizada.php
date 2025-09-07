<?php

namespace App\Notifications;

use App\Models\Incidencias;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncidenciaActualizada extends Notification implements ShouldQueue
{
    use Queueable;

    protected $incidencia;
    protected $actualizadoPor;
    protected $cambios;

    public function __construct(Incidencias $incidencia, User $actualizadoPor, array $cambios = [])
    {
        $this->incidencia = $incidencia;
        $this->actualizadoPor = $actualizadoPor;
        $this->cambios = $cambios;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('incidencias.show', $this->incidencia->idincidencia);
        
        return (new MailMessage)
            ->subject('📝 Incidencia Actualizada #' . $this->incidencia->idincidencia)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Se ha actualizado una incidencia asignada a ti.')
            ->line('**Detalles de la incidencia:**')
            ->line('📋 **Asunto:** ' . $this->incidencia->asuntoincidencia)
            ->line('🏢 **Cliente:** ' . ($this->incidencia->cliente->nombrecliente ?? 'No especificado'))
            ->line('👤 **Actualizado por:** ' . $this->actualizadoPor->name)
            ->when(!empty($this->cambios), function($mail) {
                $mail->line('**Cambios realizados:**');
                foreach ($this->cambios as $campo => $valor) {
                    $mail->line("• **{$campo}:** {$valor}");
                }
            })
            ->action('🔍 Ver Incidencia', $url)
            ->line('Te recomendamos revisar los cambios realizados.')
            ->salutation('Saludos,  
El equipo de ' . config('app.name'));
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'incidencia_id' => $this->incidencia->idincidencia,
            'titulo' => 'Incidencia actualizada',
            'mensaje' => 'Se actualizó la incidencia: ' . $this->incidencia->asuntoincidencia,
            'asunto' => $this->incidencia->asuntoincidencia,
            'cliente' => $this->incidencia->cliente->nombrecliente ?? 'No especificado',
            'actualizado_por' => $this->actualizadoPor->name,
            'actualizado_por_id' => $this->actualizadoPor->id,
            'cambios' => $this->cambios,
            'fecha_actualizacion' => now()->toDateTimeString(),
            'tipo' => 'actualizacion',
            'icono' => 'fa-solid fa-edit',
            'color' => 'yellow',
            'url' => route('incidencias.show', $this->incidencia->idincidencia)
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
