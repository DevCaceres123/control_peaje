<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registros', function (Blueprint $table) {
            $table->id();
            $table->string('destino', 100)->nullable();
            $table->string('descripcion', 200)->nullable();
            $table->string('codigo_qr')->nullable()->unique();      
            $table->unsignedBigInteger('puesto_id');
            $table->unsignedBigInteger('tarifa_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('vehiculo_id')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();


            $table->foreign('puesto_id')->references('id')->on('puestos')->onDelete('restrict');
            $table->foreign('tarifa_id')->references('id')->on('tarifas')->onDelete('restrict');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('vehiculo_id')->references('id')->on('vehiculos')->onDelete('restrict');
        });

         // Aplicar COLLATE en MySQL
         if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE registros MODIFY codigo_qr VARCHAR(255) COLLATE utf8mb4_bin;");
        }

        // Aplicar extensión citext en PostgreSQL para case-sensitive
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("CREATE EXTENSION IF NOT EXISTS citext;");
            DB::statement("ALTER TABLE registros ALTER COLUMN codigo_qr TYPE CITEXT;");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros');
    }
};
