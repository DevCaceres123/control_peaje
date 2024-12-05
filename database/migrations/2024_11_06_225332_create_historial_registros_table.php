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
            $table->string('cod_qr',255);
            $table->string('puesto',100);
            $table->string('nombre_usuario',100)->nullable();
            $table->double('precio',10,2);
            $table->string('placa',50)->nullable();
            $table->string('ci',25)->nullable();
            $table->json('reporte_json');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->unsignedBigInteger('registro_id')->nullable();
            $table->timestamps();

            
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
