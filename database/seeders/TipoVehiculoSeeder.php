<?php

namespace Database\Seeders;

use App\Models\TipoVehiculo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoVehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipo_vehiculo = ["AUTOMOVIL", "MINIBUS", "CAMIONETA"];
        foreach ($tipo_vehiculo as $tipovehiculo) {
            $new_tipovehiculo = new TipoVehiculo();
            $new_tipovehiculo->nombre = $tipovehiculo;
            $new_tipovehiculo->estado = "activo";
            $new_tipovehiculo->save();
        }
    }
}
