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
        Schema::table('historial_puesto', function (Blueprint $table) {
            $table->string('estado',100)->after('descripcion_edicion')->nullable(); // Agrega el campo estado con un valor por defecto
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historial_puesto', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};
