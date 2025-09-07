<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DebugQueue extends Command
{
    protected $signature = 'debug:queue';
    protected $description = 'Verificar el estado de las colas';

    public function handle()
    {
        $this->info('🔍 Verificando estado de las colas...');
        
        // 1. Verificar configuración de colas
        $this->info("\n⚙️ Configuración de colas:");
        $this->line('Queue driver: ' . config('queue.default'));
        $this->line('Database connection: ' . config('database.default'));
        
        // 2. Verificar tabla jobs
        try {
            $jobsPendientes = DB::table('jobs')->count();
            $this->info("\n📊 Jobs en cola: {$jobsPendientes}");
            
            if ($jobsPendientes > 0) {
                $this->info("📋 Últimos jobs:");
                $jobs = DB::table('jobs')->orderBy('created_at', 'desc')->take(5)->get();
                foreach ($jobs as $job) {
                    $payload = json_decode($job->payload, true);
                    $this->line("- ID: {$job->id}");
                    $this->line("  Tipo: " . ($payload['displayName'] ?? 'Unknown'));
                    $this->line("  Intentos: {$job->attempts}");
                    $this->line("  Creado: " . date('Y-m-d H:i:s', $job->created_at));
                    $this->line("");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error al verificar tabla jobs: " . $e->getMessage());
        }
        
        // 3. Verificar tabla failed_jobs
        try {
            $jobsFallidos = DB::table('failed_jobs')->count();
            $this->info("❌ Jobs fallidos: {$jobsFallidos}");
            
            if ($jobsFallidos > 0) {
                $this->info("📋 Últimos jobs fallidos:");
                $failedJobs = DB::table('failed_jobs')->orderBy('failed_at', 'desc')->take(3)->get();
                foreach ($failedJobs as $job) {
                    $this->line("- ID: {$job->id}");
                    $this->line("  Conexión: {$job->connection}");
                    $this->line("  Queue: {$job->queue}");
                    $this->line("  Falló: {$job->failed_at}");
                    $this->line("  Excepción: " . substr($job->exception, 0, 200) . "...");
                    $this->line("");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error al verificar failed_jobs: " . $e->getMessage());
        }
        
        // 4. Sugerencias
        $this->info("💡 Soluciones:");
        $this->line("1. Ejecutar queue worker: php artisan queue:work");
        $this->line("2. Procesar jobs pendientes: php artisan queue:work --once");
        $this->line("3. Ver jobs fallidos: php artisan queue:failed");
        $this->line("4. Reintentar fallidos: php artisan queue:retry all");
        
        return 0;
    }
}
