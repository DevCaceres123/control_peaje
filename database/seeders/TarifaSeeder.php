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
                "nombre"        => "PALA",
                "precio"        => "500",
                "descripcion"   => "C-1",
            ],
            [
                "nombre"        => "MOTONIVELADORA",
                "precio"        => "500",
                "descripcion"   => "C-2",
            ],
            [
                "nombre"        => "RETRO EXCABADORA",
                "precio"        => "1000",
                "descripcion"   => "C-3",
            ],
            [
                "nombre"        => "TOPADORA A ORUGA",
                "precio"        => "1000",
                "descripcion"   => "C-4",
            ],[
                "nombre"        => "LOBOY CON CARGA",
                "precio"        => "1000",
                "descripcion"   => "C-5",
            ],
            [
                "nombre"        => "SISTERNA DE 24M LT",
                "precio"        => "50",
                "descripcion"   => "C-6",
            ],
            [
                "nombre"        => "SISTERNA DE 48M LT",
                "precio"        => "100",
                "descripcion"   => "C-7",
            ],
            [
                "nombre"        => "CAMION DE GANADERIA",
                "precio"        => "100",
                "descripcion"   => "C-8",
            ],

            [
                "nombre"        => "CAMION DE ENLATADOS",
                "precio"        => "100",
                "descripcion"   => "C-9",
            ],

            [
                "nombre"        => "CAMION DE CERVEZA",
                "precio"        => "100",
                "descripcion"   => "C-10",
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
