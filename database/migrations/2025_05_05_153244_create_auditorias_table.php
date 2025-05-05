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
        Schema::create('auditorias', function (Blueprint $table) {
            $table->idAuditoria();
            $table->string('accion'); // Por ejemplo: "actualización", "creación", "eliminación"
            $table->string('modelo'); // Por ejemplo: "Incidencia"
            $table->unsignedBigInteger('modelo_id'); // El ID del modelo afectado
            $table->text('cambios')->nullable(); // Cambios realizados (puedes usar JSON)
            $table->unsignedBigInteger('usuario_id')->nullable(); // Usuario que hizo el cambio
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};
