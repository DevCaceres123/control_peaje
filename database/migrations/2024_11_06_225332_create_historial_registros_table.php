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
        Schema::create('historial_registros', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha_hora');
            $table->date("fecha");
            $table->time("hora");
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('registro_id');
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('registro_id')->references('id')->on('registros')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_registros');
    }
};
