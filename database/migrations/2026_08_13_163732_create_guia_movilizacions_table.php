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
        Schema::create('guia_movilizacions', function (Blueprint $table) {
            $table->id();
            $table->string('nro_guia')->unique();
            $table->dateTime('fecha_emision');
            $table->dateTime('fecha_vencimiento');
            $table->foreignId('empresa_origen_id')->constrained('empresas')->restrictOnDelete();
            $table->foreignId('empresa_destino_id')->constrained('empresas')->restrictOnDelete();
            $table->foreignId('conductor_id')->constrained('conductors')->restrictOnDelete();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->restrictOnDelete();
            $table->string('estado')->default('Borrador'); // Borrador, Emitida, En Tránsito, Anulada, Completada
            $table->json('trazabilidad')->nullable(); // Guardar sellos/firmas/alcabalas
            $table->string('qr_hash')->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guia_movilizacions');
    }
};
