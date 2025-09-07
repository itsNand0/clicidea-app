<?php

namespace App\Console\Commands;

use App\Models\Incidencias;
use App\Models\User;
use App\Notifications\IncidenciaAsignada;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestNotificacionSinCola extends Command
{
    protected $signature = 'test:notificacion-sin-cola {usuario?}';
    protected $description = 'Probar notificación SIN usar colas (inmediata)';

    public function handle()
    {
        $nombreUsuario = $this->argument('usuario') ?? 'Aldo Echauri';
        
        $this->info("🧪 Probando notificación SIN colas para: {$nombreUsuario}");
        
        // Buscar usuario
        $usuario = User::where('name', 'LIKE', "%{$nombreUsuario}%")->first();
        if (!$usuario) {
            $this->error("❌ Usuario '{$nombreUsuario}' no encontrado");
            return 1;
        }
        
        // Buscar incidencia
        $incidencia = Incidencias::first();
        if (!$incidencia) {
            $this->error("❌ No hay incidencias disponibles");
            return 1;
        }
        
        $this->info("👤 Usuario: {$usuario->name} (ID: {$usuario->id})");
        $this->info("📋 Incidencia: {$incidencia->asuntoincidencia}");
        
        // Contar notificaciones antes
        $notificacionesAntes = DB::table('notifications')
            ->where('notifiable_id', $usuario->id)
            ->count();
        $this->info("📧 Notificaciones antes: {$notificacionesAntes}");
        
        try {
            // Enviar notificación SIN cola (inmediata)
            $this->info("📤 Enviando notificación inmediata...");
            
            $usuario->notify(new IncidenciaAsignada($incidencia, $usuario));
            
            $this->info("✅ Notificación enviada");
            
            // Verificar inmediatamente
            $notificacionesDespues = DB::table('notifications')
                ->where('notifiable_id', $usuario->id)
                ->count();
            $this->info("📧 Notificaciones después: {$notificacionesDespues}");
            
            if ($notificacionesDespues > $notificacionesAntes) {
                $this->info("🎉 ¡ÉXITO! La notificación se guardó en BD");
                
                // Mostrar la última notificación
                $ultimaNotificacion = DB::table('notifications')
                    ->where('notifiable_id', $usuario->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                    
                if ($ultimaNotificacion) {
                    $data = json_decode($ultimaNotificacion->data, true);
                    $this->info("📋 Detalles:");
                    $this->line("- ID: {$ultimaNotificacion->id}");
                    $this->line("- Título: " . ($data['titulo'] ?? 'Sin título'));
                    $this->line("- Mensaje: " . ($data['mensaje'] ?? 'Sin mensaje'));
                    $this->line("- Creada: {$ultimaNotificacion->created_at}");
                }
                
                $this->info("\n💡 Conclusión: El problema era las COLAS");
                $this->info("🔧 Solución: Ejecutar 'php artisan queue:work' en otra terminal");
                
            } else {
                $this->error("❌ La notificación NO se guardó en BD");
                $this->info("💡 Puede ser un problema con la clase de notificación");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }
        
        return 0;
    }
}
