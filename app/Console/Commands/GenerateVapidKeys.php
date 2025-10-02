<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webpush:vapid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate VAPID keys for Web Push notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔑 Generando claves VAPID para Web Push...');
        
        try {
            // Generar claves VAPID
            $vapidKeys = VAPID::createVapidKeys();
            
            $this->newLine();
            $this->info('✅ Claves VAPID generadas exitosamente:');
            $this->newLine();
            
            $this->line('📋 Agrega estas líneas a tu archivo .env:');
            $this->newLine();
            
            $this->line('# === WEB PUSH VAPID KEYS ===');
            $this->line('VAPID_SUBJECT="' . config('app.url') . '"');
            $this->line('VAPID_PUBLIC_KEY="' . $vapidKeys['publicKey'] . '"');
            $this->line('VAPID_PRIVATE_KEY="' . $vapidKeys['privateKey'] . '"');
            $this->newLine();
            
            $this->warn('⚠️  IMPORTANTE:');
            $this->line('1. Copia estas claves al archivo .env de tu servidor');
            $this->line('2. Ejecuta: php artisan config:cache');
            $this->line('3. Reinicia el servidor web si es necesario');
            $this->newLine();
            
            $this->info('🔍 Para verificar la configuración:');
            $this->line('   php artisan webpush:check');
            
        } catch (\Exception $e) {
            $this->error('❌ Error generando claves VAPID: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}