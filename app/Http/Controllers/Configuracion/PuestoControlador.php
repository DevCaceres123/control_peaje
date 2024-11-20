<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Puesto\StorePuestoRequest;
use App\Http\Requests\Puesto\UpdatePuestoRequest;
use App\Models\Puesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PuestoControlador extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administrador.configuracion.puesto');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePuestoRequest $request)
    {
        DB::beginTransaction();
        try {
            $puesto         = new Puesto();
            $puesto->nombre = $request->nombre;
            $puesto->estado = 'activo';
            $puesto->save();
            DB::commit();
            return response()->json(
                mensaje_mostrar('success', 'El puesto se creo con éxito')
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                mensaje_mostrar('error', 'Ocurrio un error al insertar')
            );
        }
    }

    /**
     * Display the specified resource.
     * Vamos a utilizar la parte del estado del puesto
     */
    public function show(string $id)
    {
        DB::beginTransaction();
        try {
            $puesto = Puesto::find($id);
            $puesto->estado = ($puesto->estado == 'activo') ? 'inactivo' : 'activo';
            $puesto->save();
            DB::commit();
            return response()->json(
                mensaje_mostrar('success', 'El estado se cambio con éxito')
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                mensaje_mostrar('error', 'Ocurrio un error')
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $puesto = Puesto::find($id);
        if($puesto){
            return response()->json(
                mensaje_mostrar('success', $puesto)
            );
        }else{
            return response()->json(
                mensaje_mostrar('error', 'Ocurrio un error al optener los datos')
            );
        }
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePuestoRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $puesto = Puesto::find($id);
            $puesto->nombre = $request->nombre;
            $puesto->save();
            DB::commit();
            return response()->json(
                mensaje_mostrar('success', 'Se edito con exito el registro')
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                mensaje_mostrar('error', 'Ocurrio un error inesperado')
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $puesto = Puesto::find($id);
            $puesto->delete();
            DB::commit();
            return response()->json(
                mensaje_mostrar('success', "se elimino con éxito")
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                mensaje_mostrar('error', 'Ocurio un error inesperado'.$th)
            );
        }
    }

    //para listar el registro
    public function listar(){
        $puesto = Puesto::OrderBy('id', 'desc')->get();
        return response()->json($puesto);
    }
}
