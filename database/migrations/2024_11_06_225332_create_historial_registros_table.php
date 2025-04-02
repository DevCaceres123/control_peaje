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
        Schema::create('historial_registros', function (Blueprint $table) {
            $table->id();
            $table->string('cod_qr')->nullable()->unique();
            $table->string('puesto',100);
            $table->string('nombre_usuario',100)->nullable();
            $table->double('precio',10,2);
            $table->string('placa',50)->nullable();
            $table->string('ci',25)->nullable();
            $table->json('reporte_json');
            $table->string('num_aprobados')->nullable();
            $table->string('estado_impresion',40)->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->unsignedBigInteger('registro_id')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            
        });

           // Aplicar COLLATE en MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE historial_registros MODIFY cod_qr VARCHAR(255) COLLATE utf8mb4_bin;");
        }

        // Aplicar extensión citext en PostgreSQL para case-sensitive
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("CREATE EXTENSION IF NOT EXISTS citext;");
            DB::statement("ALTER TABLE historial_registros ALTER COLUMN cod_qr TYPE CITEXT;");
        }

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_registros');
    }
};
