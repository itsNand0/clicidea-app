<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Incidencias;
use App\Notifications\IncidenciaAsignada;

class TestNotificacion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notificacion {user_id} {incidencia_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía una notificación de prueba para probar el sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $incidenciaId = $this->argument('incidencia_id');

        $user = User::find($userId);
        $incidencia = Incidencias::where('idincidencia', $incidenciaId)->first();

        if (!$user) {
            $this->error("Usuario con ID {$userId} no encontrado");
            return;
        }

        if (!$incidencia) {
            $this->error("Incidencia con ID {$incidenciaId} no encontrada");
            return;
        }

        $this->info("Enviando notificación de prueba...");
        
        try {
            $user->notify(new IncidenciaAsignada($incidencia, User::first()));
            $this->info("✅ Notificación enviada exitosamente a {$user->name}");
        } catch (\Exception $e) {
            $this->error("❌ Error al enviar notificación: " . $e->getMessage());
        }
    }
}
