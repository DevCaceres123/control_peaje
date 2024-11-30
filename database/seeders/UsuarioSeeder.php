<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $rol1       = new Role();
        $rol1->name = 'administrador';
        $rol1->save();

        $usuario = new User();
        $usuario->usuario = 'admin';
        $usuario->password = Hash::make('1234');
        $usuario->ci = '10028685';
        $usuario->nombres = 'Admin';
        $usuario->apellidos = 'admin admin';
        $usuario->estado = 'activo';
        $usuario->email = 'rodrigo@gmail.com';
        $usuario->save();

        $usuario->syncRoles(['administrador']);



        /* $usuario1 = new User();
        $usuario1->usuario = '10091554';
        $usuario1->password = Hash::make('10091554');
        $usuario1->ci = '10091554';
        $usuario1->nombres = 'Admin';
        $usuario1->apellidos = 'admin admin';
        $usuario1->estado = 'activo';
        $usuario1->email = 'rodrigo1@gmail.com';
        $usuario1->save();

        $usuario2 = new User();
        $usuario2->usuario = '8330023';
        $usuario2->password = Hash::make('8330023');
        $usuario2->ci = '8330023';
        $usuario2->nombres = 'Admin';
        $usuario2->apellidos = 'admin admin';
        $usuario2->estado = 'activo';
        $usuario2->email = 'rodrigo2@gmail.com';
        $usuario2->save();

        $usuario3 = new User();
        $usuario3->usuario = '6015869';
        $usuario3->password = Hash::make('6015869');
        $usuario3->ci = '6015869';
        $usuario3->nombres = 'Admin';
        $usuario3->apellidos = 'admin admin';
        $usuario3->estado = 'activo';
        $usuario3->email = 'rodrigo3@gmail.com';
        $usuario3->save(); */
    }

}
