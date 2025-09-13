<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Incidencias;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SendPushNotifications extends Command
{
    protected $signature = 'notifications:push {--test : Modo de prueba}';
    protected $description = 'Enviar notificaciones push para incidencias pendientes';

    public function handle()
    {
        $isTest = $this->option('test');
        
        if ($isTest) {
            $this->info('🧪 Modo de prueba activado');
        }

        // 1. Buscar incidencias que necesiten notificación
        $incidenciasPendientes = $this->getIncidenciasPendientes();
        
        if ($incidenciasPendientes->isEmpty()) {
            $this->info('📭 No hay incidencias pendientes para notificar');
            return;
        }

        $this->info("📧 Encontradas {$incidenciasPendientes->count()} incidencias para notificar");

        // 2. Procesar cada incidencia
        foreach ($incidenciasPendientes as $incidencia) {
            $this->procesarNotificacion($incidencia, $isTest);
        }

        $this->info('✅ Proceso de notificaciones completado');
    }

    private function getIncidenciasPendientes()
    {
        return Incidencias::with(['usuario', 'cliente', 'estadoincidencia'])
            ->whereHas('estadoincidencia', function ($q) {
                $q->where('descriestadoincidencia', 'ILIKE', '%Pendiente%')
                  ->orWhere('descriestadoincidencia', 'ILIKE', '%Proceso%');
            })
            // Incidencias creadas hace más de 30 minutos sin actualizar
            ->where('fechaincidencia', '<=', now()->subMinutes(30))
            ->whereNull('fecharesolucionincidencia')
            ->get();
    }

    private function procesarNotificacion($incidencia, $isTest = false)
    {
        try {
            $usuario = $incidencia->usuario;
            
            if (!$usuario) {
                $this->warn("⚠️  Incidencia #{$incidencia->idincidencia} sin usuario asignado");
                return;
            }

            $mensaje = $this->construirMensaje($incidencia);
            
            if ($isTest) {
                $this->line("📱 [TEST] Notificación para {$usuario->name}: {$mensaje}");
            } else {
                // Aquí iría la lógica real de push notification
                $this->enviarPushNotification($usuario, $mensaje, $incidencia);
                $this->info("📱 Notificación enviada a {$usuario->name}");
            }

        } catch (\Exception $e) {
            $this->error("❌ Error al procesar incidencia #{$incidencia->idincidencia}: " . $e->getMessage());
            Log::error('Error en push notification', [
                'incidencia_id' => $incidencia->idincidencia,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function construirMensaje($incidencia)
    {
        return "📋 Incidencia #{$incidencia->idincidencia} pendiente: {$incidencia->asuntoincidencia} - Cliente: {$incidencia->cliente->nombre}";
    }

    private function enviarPushNotification($usuario, $mensaje, $incidencia)
    {
        // TODO: Implementar con Web Push API o Firebase
        // Por ahora, registrar en log
        Log::info('Push notification enviada', [
            'usuario_id' => $usuario->id,
            'incidencia_id' => $incidencia->idincidencia,
            'mensaje' => $mensaje
        ]);
    }
}
