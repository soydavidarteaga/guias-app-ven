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
        Schema::create('guia_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guia_movilizacion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rubro_id')->constrained()->restrictOnDelete();
            $table->decimal('cantidad', 10, 2);
            $table->decimal('precio_unitario', 12, 2);
            $table->string('observacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guia_items');
    }
};
