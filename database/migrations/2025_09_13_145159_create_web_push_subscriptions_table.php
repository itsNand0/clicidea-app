<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('web_push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('endpoint', 500); // URL del endpoint de push
            $table->text('public_key'); // Clave pública del cliente
            $table->text('auth_token'); // Token de autenticación
            $table->string('content_encoding')->default('aesgcm'); // Encoding (aesgcm o aes128gcm)
            $table->json('subscription_data')->nullable(); // Datos completos de la suscripción
            $table->string('user_agent')->nullable(); // Navegador/dispositivo
            $table->boolean('is_active')->default(true); // Si está activa
            $table->timestamp('last_used_at')->nullable(); // Última vez que se usó
            $table->timestamps();
            
            // Índices para rendimiento
            $table->index(['user_id', 'is_active']);
            $table->unique(['user_id', 'endpoint']); // Un usuario no puede tener la misma suscripción duplicada
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_push_subscriptions');
    }
};
