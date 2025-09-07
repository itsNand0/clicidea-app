<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class QueueMonitor extends Command
{
    protected $signature = 'queue:monitor {--auto : Procesar automáticamente}';
    protected $description = 'Monitorear y procesar colas cuando sea necesario';

    public function handle()
    {
        $auto = $this->option('auto');
        
        if ($auto) {
            $this->info('🔄 Modo automático activado. Presiona Ctrl+C para detener.');
            
            while (true) {
                $this->verificarYProcesar();
                sleep(30); // Esperar 30 segundos
            }
        } else {
            $this->verificarYProcesar();
        }
    }
    
    private function verificarYProcesar()
    {
        try {
            $jobsPendientes = DB::table('jobs')->count();
            $timestamp = now()->format('Y-m-d H:i:s');
            
            if ($jobsPendientes > 0) {
                $this->info("[{$timestamp}] 📧 {$jobsPendientes} jobs pendientes. Procesando...");
                
                // Procesar con límites seguros
                Artisan::call('queue:work', [
                    '--stop-when-empty' => true,
                    '--memory' => 512,
                    '--timeout' => 60,
                    '--tries' => 3
                ]);
                
                $jobsRestantes = DB::table('jobs')->count();
                $procesados = $jobsPendientes - $jobsRestantes;
                
                $this->info("✅ {$procesados} jobs procesados. {$jobsRestantes} restantes.");
                
            } else {
                $this->line("[{$timestamp}] ✨ No hay jobs pendientes.");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
        }
    }
}
