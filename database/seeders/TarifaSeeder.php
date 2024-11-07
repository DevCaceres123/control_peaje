<?php

namespace Database\Seeders;

use App\Models\Tarifa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TarifaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $tarifas = [
            [
                "nombre"        => "LIVIANOS",
                "precio"        => "2",
                "descripcion"   => "C-1",
            ],
            [
                "nombre"        => "BUSES Y CAMIONES DE 2 EJES",
                "precio"        => "4",
                "descripcion"   => "C-2",
            ],
            [
                "nombre"        => "BUSES Y CAMIONES DE 3 EJES",
                "precio"        => "6",
                "descripcion"   => "C-3",
            ],
            [
                "nombre"        => "CAMIONES DE 4 EJES",
                "precio"        => "8",
                "descripcion"   => "C-4",
            ],[
                "nombre"        => "CAMIONES DE 5 EJES",
                "precio"        => "10",
                "descripcion"   => "C-5",
            ],
            [
                "nombre"        => "CAMIONES DE 6 EJES",
                "precio"        => "12",
                "descripcion"   => "C-6",
            ],
            [
                "nombre"        => "CAMIONES DE 7 EJES",
                "precio"        => "14",
                "descripcion"   => "C-7",
            ],
            [
                "nombre"        => "MAQUINARIA",
                "precio"        => "0",
                "descripcion"   => "C-8",
            ],
        ];
        foreach ($tarifas as $tarifa) {
            $new_tarifas                = new Tarifa();
            $new_tarifas->nombre        = $tarifa['nombre'];
            $new_tarifas->precio        = $tarifa['precio'];
            $new_tarifas->descripcion   = $tarifa['descripcion'];
            $new_tarifas->estado        = 'activo';
            $new_tarifas->save();
        }


    }
}
