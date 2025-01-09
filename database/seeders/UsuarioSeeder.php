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

        $rol2       = new Role();
        $rol2->name = 'encargado_puesto';
        $rol2->save();

        $usuario = new User();
        $usuario->usuario = 'admin';
        $usuario->password = Hash::make('1234');
        $usuario->ci = '10028685';
        $usuario->nombres = 'Michael';
        $usuario->apellidos = 'Caceres Quina';
        $usuario->estado = 'activo';
        $usuario->email = 'rodrigo@gmail.com';
        $usuario->save();

        $usuario->syncRoles(['administrador']);




        $usuario2 = new User();
        $usuario2->usuario = '123456';
        $usuario2->password = Hash::make('1234');
        $usuario2->ci = '123456';
        $usuario2->nombres = 'pepe';
        $usuario2->apellidos = 'casas davalos';
        $usuario2->estado = 'activo';
        $usuario2->email = 'pepe@gmail.com';
        $usuario2->save();

        $usuario2->syncRoles(['encargado_puesto']);




        $usuario3 = new User();
        $usuario3->usuario = '7894561';
        $usuario3->password = Hash::make('1234');
        $usuario3->ci = '7894561';
        $usuario3->nombres = 'Maria';
        $usuario3->apellidos = 'quina davalos';
        $usuario3->estado = 'activo';
        $usuario3->email = 'Maria@gmail.com';
        $usuario3->save();

        $usuario3->syncRoles(['encargado_puesto']);



        $usuario3 = new User();
        $usuario3->usuario = '741852';
        $usuario3->password = Hash::make('1234');
        $usuario3->ci = '741852';
        $usuario3->nombres = 'gloria';
        $usuario3->apellidos = 'ramos davalos';
        $usuario3->estado = 'activo';
        $usuario3->email = 'gloria@gmail.com';
        $usuario3->save();

        $usuario3->syncRoles(['encargado_puesto']);


        // PERMISOS PARA EL SISTEMA

        Permission::create(['name' => 'inicio.index'])->assignRole($rol1);
        Permission::create(['name' => 'inicio.estadistica'])->assignRole($rol1);

        // USUARIO
        Permission::create(['name' => 'admin.index'])->syncRoles([$rol1]);


        Permission::create(['name' => 'admin.usuario.inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.usuario.crear'])->assignRole($rol1);
        Permission::create(['name' => 'admin.usuario.desactivar'])->assignRole($rol1);
        Permission::create(['name' => 'admin.usuario.editarRol'])->assignRole($rol1);
        Permission::create(['name' => 'admin.usuario.eliminarRol'])->assignRole($rol1);


        //ROL
        Permission::create(['name' => 'admin.rol.inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.rol.crear'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.rol.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.rol.eliminar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.rol.visualizar'])->syncRoles([$rol1]);


        //PERMISOS
        Permission::create(['name' => 'admin.permiso.inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.permiso.crear'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.permiso.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.permiso.eliminar'])->syncRoles([$rol1]);


        //CONTROL PEJAE

        // GENERAR REGISTROS
        Permission::create(['name' => 'control.index'])->syncRoles([$rol1, $rol2]);

        Permission::create(['name' => 'control.generar.inicio'])->syncRoles([$rol1, $rol2]);
        Permission::create(['name' => 'control.generar.verificar'])->syncRoles([$rol1, $rol2]);
        Permission::create(['name' => 'control.generar.generar'])->syncRoles([$rol1, $rol2]);
        Permission::create(['name' => 'control.generar.llenar'])->syncRoles([$rol1, $rol2]);

        // LISTAR REGISTROS

        Permission::create(['name' => 'control.listar.inicio'])->syncRoles([$rol1, $rol2]);
        Permission::create(['name' => 'control.listar.fechas'])->syncRoles([$rol1, $rol2]);
        Permission::create(['name' => 'control.listar.encargado'])->syncRoles([$rol1]);
        Permission::create(['name' => 'control.listar.listar_todo'])->syncRoles([$rol1]);
        Permission::create(['name' => 'control.listar.reporte_diario'])->syncRoles([$rol1, $rol2]);
        Permission::create(['name' => 'control.listar.eliminar'])->syncRoles([$rol1, $rol2]);
        Permission::create(['name' => 'control.listar.generar_boleta'])->syncRoles([$rol1, $rol2]);

        // PUESTOS

        // ASIGANAR PUESTO
        Permission::create(['name' => 'puesto.index'])->syncRoles([$rol1]);

        Permission::create(['name' => 'puesto.asignar.inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'puesto.asignar.asignar'])->syncRoles([$rol1]);


        // HISTORIAL PUESTO
        Permission::create(['name' => 'puesto.historial.inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'puesto.historial.fecha'])->syncRoles([$rol1]);
        Permission::create(['name' => 'puesto.historial.listar_todo'])->syncRoles([$rol1]);


        // REPORTES

        Permission::create(['name' => 'reporte.inicio'])->syncRoles([$rol1]);

        // CONFIGURACION
        Permission::create(['name' => 'configuracion.index'])->syncRoles([$rol1]);


        // PUESTO
        Permission::create(['name' => 'configuracion.puesto.inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'configuracion.puesto.crear'])->syncRoles([$rol1]);
        Permission::create(['name' => 'configuracion.puesto.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'configuracion.puesto.eliminar'])->syncRoles([$rol1]);
        

        // TARIFA
        Permission::create(['name' => 'configuracion.tarifa.inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'configuracion.tarifa.crear'])->syncRoles([$rol1]);
        Permission::create(['name' => 'configuracion.tarifa.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'configuracion.tarifa.eliminar'])->syncRoles([$rol1]);
        


        // TIPO DE VEGICULO
        Permission::create(['name' => 'configuracion.tipo_vehi.inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'configuracion.tipo_vehi.crear'])->syncRoles([$rol1]);
        Permission::create(['name' => 'configuracion.tipo_vehi.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'configuracion.tipo_vehi.eliminar'])->syncRoles([$rol1]);
        

        // COLOR
        Permission::create(['name' => 'configuracion.color.inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'configuracion.color.crear'])->syncRoles([$rol1]);
        Permission::create(['name' => 'configuracion.color.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'configuracion.color.eliminar'])->syncRoles([$rol1]);
        
    }
}
