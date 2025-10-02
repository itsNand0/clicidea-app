<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WebPushSubscription;

class CheckWebPushConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webpush:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Web Push configuration and status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando configuración de Web Push...');
        $this->newLine();
        
        // Verificar configuración VAPID
        $vapidSubject = config('webpush.vapid.subject');
        $vapidPublic = config('webpush.vapid.public_key');
        $vapidPrivate = config('webpush.vapid.private_key');
        
        $this->line('📋 CONFIGURACIÓN VAPID:');
        
        if ($vapidSubject) {
            $this->line('✅ VAPID_SUBJECT: ' . $vapidSubject);
        } else {
            $this->error('❌ VAPID_SUBJECT: No configurado');
        }
        
        if ($vapidPublic) {
            $this->line('✅ VAPID_PUBLIC_KEY: ' . substr($vapidPublic, 0, 20) . '...');
        } else {
            $this->error('❌ VAPID_PUBLIC_KEY: No configurado');
        }
        
        if ($vapidPrivate) {
            $this->line('✅ VAPID_PRIVATE_KEY: ' . substr($vapidPrivate, 0, 20) . '...');
        } else {
            $this->error('❌ VAPID_PRIVATE_KEY: No configurado');
        }
        
        $this->newLine();
        
        // Verificar base de datos
        try {
            $totalSubscriptions = WebPushSubscription::count();
            $activeSubscriptions = WebPushSubscription::where('is_active', true)->count();
            
            $this->line('📊 SUSCRIPCIONES:');
            $this->line("✅ Total: {$totalSubscriptions}");
            $this->line("✅ Activas: {$activeSubscriptions}");
            
        } catch (\Exception $e) {
            $this->error('❌ Error accediendo a la base de datos: ' . $e->getMessage());
        }
        
        $this->newLine();
        
        // Verificar configuración general
        $appUrl = config('app.url');
        $this->line('🌐 CONFIGURACIÓN GENERAL:');
        $this->line("✅ APP_URL: {$appUrl}");
        
        // Estado general
        $allConfigured = $vapidSubject && $vapidPublic && $vapidPrivate;
        
        $this->newLine();
        
        if ($allConfigured) {
            $this->info('🎉 ¡Configuración completa! Web Push debería funcionar.');
        } else {
            $this->warn('⚠️  Configuración incompleta. Ejecuta:');
            $this->line('   php artisan webpush:vapid');
            $this->line('   # Luego agrega las claves al .env');
            $this->line('   php artisan config:cache');
        }
        
        return 0;
    }
}