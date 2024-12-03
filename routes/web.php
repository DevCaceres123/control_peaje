<?php

use App\Http\Controllers\Configuracion\ColorControlador;
use App\Http\Controllers\Configuracion\PuestoControlador;
use App\Http\Controllers\Configuracion\TarifaControlador;
use App\Http\Controllers\Configuracion\TipoVehiculoControlador;
use App\Http\Controllers\Peaje\Controlador_registro;
use App\Http\Controllers\Puesto\Controlador_puesto;
use App\Http\Controllers\Usuario\Controlador_login;
use App\Http\Controllers\Usuario\Controlador_permisos;
use App\Http\Controllers\Usuario\Controlador_rol;
use App\Http\Controllers\Usuario\Controlador_user;
use App\Http\Controllers\Usuario\Controlador_usuario;
use App\Http\Middleware\Autenticados;
use App\Http\Middleware\No_autenticados;
use Illuminate\Support\Facades\Route;



Route::prefix('/')->middleware([No_autenticados::class])->group(function () {
    Route::get('/', function () {
        return view('login');
    })->name('login');

    Route::get('/login', function () {
        return view('login', ['fromHome' => true]);
    })->name('login_home');

    Route::controller(Controlador_login::class)->group(function () {
        Route::post('ingresar', 'ingresar')->name('log_ingresar');
    });
});


Route::prefix('/admin')->middleware([Autenticados::class])->group(function () {
    Route::controller(Controlador_login::class)->group(function () {
        Route::get('inicio', 'inicio')->name('inicio');
        Route::post('cerrar_session', 'cerrar_session')->name('salir');
    });

    Route::controller(Controlador_usuario::class)->group(function () {
        Route::get('perfil', 'perfil')->name('perfil');
        Route::post('pwd_guardar', 'password_guardar')->name('pwd_guardar');
    });

    //PARA LOS PERMISOS
    Route::resource('permisos', Controlador_permisos::class);
    Route::post('/permisos/listar', [Controlador_permisos::class, 'listar'])->name('permisos.listar');

    //PARA EL ROL
    Route::resource('roles', Controlador_rol::class);

    //para la administracion de usuarios
    Route::resource('user', Controlador_user::class);
    Route::post('/user/listar', [Controlador_user::class, 'listar'])->name('user.listar');


    // PARA LA ADMINISTRACION DE CONTROL PEAJE
    Route::controller(Controlador_registro::class)->group(function () {
        Route::resource('peaje', Controlador_registro::class);
        Route::post('generar_qr', "generar_qr");


    });

    // PARA LA ADMINISTRACION DE PUESTOS EN ADICIONAR A UN USUARIO 
    Route::controller(Controlador_puesto::class)->group(function () {
        Route::resource('puesto_asignar', Controlador_puesto::class);
        Route::get('/historial','historial')->name('puesto_asignar.historial');
        Route::get('/listar_historial','listar_historial');


        // Route::get('/buscar_encargado/{id_encargado}','buscar_encargado')->name('peaje.buscar_ci');
      
        
    });




    //PARA LA ADMINISTRACION DE PUESTO
    Route::resource('puesto', PuestoControlador::class);
    Route::post('/puesto/listar', [PuestoControlador::class, 'listar'])->name('puesto.listar');


    //PARA LA ADMINISTRACION DE TARIFAS
    Route::resource('tarifas', TarifaControlador::class);
    Route::post('/tarifas/listar', [TarifaControlador::class, 'listar'])->name('tarifas.listar');

    //PARA LA ADMINISTRACION DE TIPOS DE BEHICULOS
    Route::resource('/tipoVehiculos', TipoVehiculoControlador::class);
    Route::post('/tipoVehiculos/listar', [TipoVehiculoControlador::class, 'listar'])->name('tipoVehiculos.listar');

    //PARA LA ADMINISTRACION DE LOS COLORES
    Route::resource('/color', ColorControlador::class);
    Route::post('/color/listar', [ColorControlador::class, 'listar'])->name('color.listar');
});
