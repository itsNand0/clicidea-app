<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DiagnosticarNotificaciones extends Command
{
    protected $signature = 'debug:notificaciones-usuario {nombre?}';
    protected $description = 'Diagnosticar notificaciones de un usuario específico';

    public function handle()
    {
        $nombre = $this->argument('nombre') ?? 'Aldo Echauri';
        
        $this->info("🔍 Diagnosticando notificaciones para: {$nombre}");
        
        // 1. Buscar usuario
        $usuario = User::where('name', 'LIKE', "%{$nombre}%")->first();
        
        if (!$usuario) {
            $this->error("❌ Usuario '{$nombre}' no encontrado");
            $this->info("💡 Usuarios disponibles:");
            User::take(10)->get()->each(function($user) {
                $this->line("- {$user->name} (ID: {$user->id})");
            });
            return 1;
        }
        
        $this->info("👤 Usuario encontrado: {$usuario->name} (ID: {$usuario->id})");
        
        // 2. Verificar notificaciones en BD directamente
        $this->info("\n📧 Verificando tabla notifications:");
        $notificacionesDB = DB::table('notifications')
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $usuario->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $this->info("Total en BD: " . $notificacionesDB->count());
        
        if ($notificacionesDB->count() > 0) {
            $this->info("\n📋 Últimas notificaciones en BD:");
            foreach ($notificacionesDB->take(5) as $notif) {
                $data = json_decode($notif->data, true);
                $this->line("- ID: {$notif->id}");
                $this->line("  Tipo: {$notif->type}");
                $this->line("  Título: " . ($data['titulo'] ?? 'Sin título'));
                $this->line("  Mensaje: " . ($data['mensaje'] ?? 'Sin mensaje'));
                $this->line("  Creada: {$notif->created_at}");
                $this->line("  Leída: " . ($notif->read_at ? 'Sí (' . $notif->read_at . ')' : 'No'));
                $this->line("");
            }
        }
        
        // 3. Verificar vía modelo Eloquent
        $this->info("🔄 Verificando vía modelo User:");
        $notificacionesModelo = $usuario->notifications()->count();
        $noLeidas = $usuario->unreadNotifications()->count();
        
        $this->info("Total vía modelo: {$notificacionesModelo}");
        $this->info("No leídas: {$noLeidas}");
        
        // 4. Verificar última notificación del log
        $this->info("\n🕒 Verificando notificación reciente (incidencia_id: 437709):");
        $notificacionReciente = DB::table('notifications')
            ->where('notifiable_id', $usuario->id)
            ->where('data', 'LIKE', '%437709%')
            ->first();
            
        if ($notificacionReciente) {
            $this->info("✅ Notificación encontrada en BD:");
            $data = json_decode($notificacionReciente->data, true);
            $this->line("- Creada: {$notificacionReciente->created_at}");
            $this->line("- Datos: " . json_encode($data, JSON_PRETTY_PRINT));
        } else {
            $this->error("❌ Notificación de incidencia 437709 NO encontrada en BD");
        }
        
        // 5. Sugerencias
        if ($notificacionesDB->count() === 0) {
            $this->error("\n❌ PROBLEMA: No hay notificaciones en BD para este usuario");
            $this->info("💡 Posibles causas:");
            $this->line("- La notificación no se guardó en BD");
            $this->line("- Usuario ID incorrecto en el envío");
            $this->line("- Error en el proceso de queue");
        } elseif ($notificacionesModelo === 0) {
            $this->error("\n❌ PROBLEMA: Hay notificaciones en BD pero el modelo no las encuentra");
            $this->info("💡 Posibles causas:");
            $this->line("- Problema con las relaciones del modelo User");
            $this->line("- notifiable_type incorrecto");
        } else {
            $this->info("\n✅ Las notificaciones están en BD y el modelo las encuentra");
            $this->info("💡 El problema puede estar en:");
            $this->line("- Cache del navegador");
            $this->line("- Componente Livewire no actualizado");
            $this->line("- Sesión de usuario incorrecta");
        }
        
        return 0;
    }
}
