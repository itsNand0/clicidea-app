<?php

namespace App\Console\Commands;

use App\Models\Incidencias;
use App\Models\User;
use App\Notifications\IncidenciaAsignada;
use Illuminate\Console\Command;

class TestNotificacionAsignacion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notificacion-asignacion {--user_id=} {--incidencia_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba el envío de notificaciones de asignación de incidencias';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user_id');
        $incidenciaId = $this->option('incidencia_id');

        // Si no se proporcionan IDs, usar los primeros disponibles
        if (!$userId) {
            $usuario = User::first();
            if (!$usuario) {
                $this->error('No hay usuarios en la base de datos');
                return 1;
            }
        } else {
            $usuario = User::find($userId);
            if (!$usuario) {
                $this->error("Usuario con ID {$userId} no encontrado");
                return 1;
            }
        }

        if (!$incidenciaId) {
            $incidencia = Incidencias::first();
            if (!$incidencia) {
                $this->error('No hay incidencias en la base de datos');
                return 1;
            }
        } else {
            $incidencia = Incidencias::find($incidenciaId);
            if (!$incidencia) {
                $this->error("Incidencia con ID {$incidenciaId} no encontrada");
                return 1;
            }
        }

        $this->info("Enviando notificación de prueba...");
        $this->info("Usuario: {$usuario->name} (ID: {$usuario->id})");
        $this->info("Incidencia: {$incidencia->asuntoincidencia} (ID: {$incidencia->idincidencia})");

        try {
            // Crear un usuario "asignador" ficticio (el usuario actual)
            $asignadoPor = User::where('id', '!=', $usuario->id)->first() ?? $usuario;
            
            $usuario->notify(new IncidenciaAsignada($incidencia, $asignadoPor));
            
            $this->info('✅ Notificación enviada exitosamente!');
            $this->info('Revisa:');
            $this->line('- La campana de notificaciones en la interfaz');
            $this->line('- Tu bandeja de email (si está configurado)');
            $this->line('- Los logs en storage/logs/laravel.log');
            
        } catch (\Exception $e) {
            $this->error('❌ Error al enviar notificación: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
