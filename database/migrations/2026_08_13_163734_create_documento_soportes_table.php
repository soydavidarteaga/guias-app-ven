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
        Schema::create('documento_soportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guia_movilizacion_id')->constrained()->cascadeOnDelete();
            $table->string('tipo_documento'); // Factura, Nota de Entrega, Precinto
            $table->string('numero_documento');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_soportes');
    }
};
