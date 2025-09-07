<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerNotificacionesUsuario extends Command
{
    protected $signature = 'ver:notificaciones {user_id?}';
    protected $description = 'Ver las notificaciones de un usuario específico';

    public function handle()
    {
        $userId = $this->argument('user_id') ?? 41; // Usuario de la prueba anterior
        
        $usuario = User::find($userId);
        if (!$usuario) {
            $this->error("Usuario con ID {$userId} no encontrado");
            return 1;
        }
        
        $this->info("👤 Usuario: {$usuario->name} (ID: {$usuario->id})");
        
        // Verificar notificaciones directamente en BD
        $notificacionesDB = DB::table('notifications')
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $userId)
            ->get();
            
        $this->info("📧 Notificaciones en BD: " . $notificacionesDB->count());
        
        // Verificar notificaciones via modelo
        $notificacionesModelo = $usuario->notifications()->count();
        $noLeidas = $usuario->unreadNotifications()->count();
        
        $this->info("📱 Notificaciones via modelo: {$notificacionesModelo}");
        $this->info("🔔 No leídas: {$noLeidas}");
        
        // Mostrar detalles de las notificaciones
        if ($notificacionesDB->count() > 0) {
            $this->info("\n📋 Detalles de notificaciones:");
            foreach ($notificacionesDB as $notif) {
                $data = json_decode($notif->data, true);
                $this->line("- ID: {$notif->id}");
                $this->line("  Título: {$data['titulo']}");
                $this->line("  Mensaje: {$data['mensaje']}");
                $this->line("  Leída: " . ($notif->read_at ? 'Sí' : 'No'));
                $this->line("  Fecha: {$notif->created_at}");
                $this->line("");
            }
        }
        
        return 0;
    }
}
