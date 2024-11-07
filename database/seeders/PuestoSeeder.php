<?php

namespace Database\Seeders;

use App\Models\Puesto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PuestoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $puestos=[
            "PANAMERICANA",
            "PUENTE BATALLON",
            "SALIDA GUANAY",
        ];
        foreach ($puestos as $puesto) {
            $new_puesto = new Puesto();
            $new_puesto->nombre = $puesto;
            $new_puesto->estado = 'activo';
            $new_puesto->save();
        }
    }
}
