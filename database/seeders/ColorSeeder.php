<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colores = [
            "Blanco", "Negro Gris",  "Plateado", "Azul", "Rojo", "Verde", "Bronce", "Marrón", "Naranja", "Amarillo", "Morado", "Champán", "Rosa", "Verde Lima"
        ];

        foreach ($colores as $color) {
            $new_colores = new Color();
            $new_colores->nombre = $color;
            $new_colores->save();
        }
    }
}
