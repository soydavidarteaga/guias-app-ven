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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social');
            $table->string('rif')->unique();
            $table->string('codigo_sica')->unique()->nullable();
            $table->string('persona_autorizada')->nullable();
            $table->string('telefonos')->nullable();
            $table->string('estado')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('parroquia')->nullable();
            $table->text('direccion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
