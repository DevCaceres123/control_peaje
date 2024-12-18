<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tarifa\StoreTarifaRequest;
use App\Http\Requests\Tarifa\UpdateTarifaRequest;
use App\Models\Tarifa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TarifaControlador extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can('configuracion.tarifa.inicio')) {
            return redirect()->route('inicio');
        }
        return view('administrador.configuracion.tarifa');
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
    public function store(StoreTarifaRequest $request)
    {
        DB::beginTransaction();
        try {
            $tarifa                 = new Tarifa();
            $tarifa->nombre         = $request->nombre;
            $tarifa->precio         = $request->precio;
            $tarifa->descripcion    = $request->descripcion;
            $tarifa->estado         = 'activo'; 
            $tarifa->save();
            DB::commit();
            return response()->json(
                mensaje_mostrar('success', 'La tarifa se creo con éxito')
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                mensaje_mostrar('error', 'Ocurrio un error inesperado!')
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        DB::beginTransaction();
        try {
            $tarifa = Tarifa::find($id);
            $tarifa->estado = ($tarifa->estado == 'activo') ? 'inactivo' : 'activo';
            $tarifa->save();
            DB::commit();
            return response()->json(mensaje_mostrar('success', 'El estado se cambio con éxito'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(mensaje_mostrar('error', 'Ocurrio un error'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tarifa = Tarifa::find($id);
        if($tarifa){
            return response()->json(
                mensaje_mostrar('success', $tarifa)
            );
        }else{
            return response()->json(
                mensaje_mostrar('error', 'No se econtro el registro')
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTarifaRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $tarifa = Tarifa::find($id);
            $tarifa->nombre = $request->nombre;
            $tarifa->precio = $request->precio;
            $tarifa->descripcion = $request->descripcion;
            $tarifa->save();
            DB::commit();
            return response()->json(
                mensaje_mostrar('success', 'La tarifa se edito con éxito')
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
            $tarifa = Tarifa::find($id);
            $tarifa->delete();
            DB::commit();
            return response()->json(
                mensaje_mostrar('success', 'El registro se elimino con éxito')
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                mensaje_mostrar('error', 'Ocurrio un error inesperado al eliminar')
            );
        }
    }


    public function listar(){

        $permissions = [
            'editar' => auth()->user()->can('configuracion.tarifa.editar'),
            'eliminar' => auth()->user()->can('configuracion.tarifa.eliminar'),
            'desactivar' => auth()->user()->can('configuracion.tarifa.desactivar'),
           
        ];

        $tarifa = Tarifa::OrderBy('id', 'desc')->get();

        $data=[
            'permissions'=>$permissions,
            'tarifa'=>$tarifa,
        ];
        return response()->json($data);
    }
}
