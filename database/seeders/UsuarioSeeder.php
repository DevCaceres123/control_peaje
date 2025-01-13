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
        $rol1 = new Role();
        $rol1->name = 'administrador';
        $rol1->save();

        $rol2 = new Role();
        $rol2->name = 'encargado_puesto';
        $rol2->save();

        //$usuario = new User();
        //$usuario->usuario = '1234567890';
        //$usuario->password = Hash::make('1234567890_1234567');
        //$usuario->ci = '123456789';
        //$usuario->nombres = 'Freddy';
        //$usuario->apellidos = 'Barra';
        //$usuario->estado = 'activo';
        //$usuario->email = 'freddy@gmail.com';
        //$usuario->save();

       // $usuario->syncRoles(['administrador']);

        $usuario2 = new User();
        $usuario2->usuario = '9926137';
        $usuario2->password = Hash::make('9926137_miriam');
        $usuario2->ci = '9926137';
        $usuario2->nombres = 'Miriam';
        $usuario2->apellidos = 'Condori';
        $usuario2->estado = 'activo';
        $usuario2->email = 'miriam@gmail.com';
        $usuario2->save();

        $usuario2->syncRoles(['encargado_puesto']);

        $usuario3 = new User();
        $usuario3->usuario = '10090354';
        $usuario3->password = Hash::make('10090354_margarita');
        $usuario3->ci = '10090354';
        $usuario3->nombres = 'Margarita';
        $usuario3->apellidos = 'Calle';
        $usuario3->estado = 'activo';
        $usuario3->email = 'margarita@gmail.com';
        $usuario3->save();

        $usuario3->syncRoles(['encargado_puesto']);

        $usuario3 = new User();
        $usuario3->usuario = '9139710';
        $usuario3->password = Hash::make('9139710_lizeth');
        $usuario3->ci = '9139710';
        $usuario3->nombres = 'Lizeth';
        $usuario3->apellidos = 'Arteaga';
        $usuario3->estado = 'activo';
        $usuario3->email = 'lizeth@gmail.com';
        $usuario3->save();

        $usuario3->syncRoles(['encargado_puesto']);

        $usuario4 = new User();
        $usuario4->usuario = '10035299';
        $usuario4->password = Hash::make('10035299_abigail');
        $usuario4->ci = '10035299';
        $usuario4->nombres = 'Abigail';
        $usuario4->apellidos = 'Aguilar';
        $usuario4->estado = 'activo';
        $usuario4->email = 'abigail@gmail.com';
        $usuario4->save();

        $usuario4->syncRoles(['encargado_puesto']);

        $usuario5 = new User();
        $usuario5->usuario = '9889426';
        $usuario5->password = Hash::make('9889426_jhaqueline');
        $usuario5->ci = '9889426';
        $usuario5->nombres = 'Jhaqueline';
        $usuario5->apellidos = 'Mendoza';
        $usuario5->estado = 'activo';
        $usuario5->email = 'jhaqueline@gmail.com';
        $usuario5->save();

        $usuario5->syncRoles(['encargado_puesto']);

        $usuario6 = new User();
        $usuario6->usuario = '14168764';
        $usuario6->password = Hash::make('14168764_jhecenia');
        $usuario6->ci = '14168764';
        $usuario6->nombres = 'Jhecenia';
        $usuario6->apellidos = 'Villca Choque';
        $usuario6->estado = 'activo';
        $usuario6->email = 'jhecenia@gmail.com';
        $usuario6->save();

        $usuario6->syncRoles(['encargado_puesto']);

        $usuario7 = new User();
        $usuario7->usuario = '12345678';
        $usuario7->password = Hash::make('12345678_ramiro');
        $usuario7->ci = '12345678';
        $usuario7->nombres = 'Ramiro';
        $usuario7->apellidos = 'Ramiro Ramiro';
        $usuario7->estado = 'activo';
        $usuario7->email = 'ramiro@gmail.com';
        $usuario7->save();

        $usuario7->syncRoles(['administrador']);

        $usuario8 = new User();
        $usuario8->usuario = '123456789';
        $usuario8->password = Hash::make('123456789_freddy');
        $usuario8->ci = '123456789';
        $usuario8->nombres = 'Freddy';
        $usuario8->apellidos = 'Barra';
        $usuario8->estado = 'activo';
        $usuario8->email = 'freddy@gmail.com';
        $usuario8->save();

        $usuario8->syncRoles(['administrador']);

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
