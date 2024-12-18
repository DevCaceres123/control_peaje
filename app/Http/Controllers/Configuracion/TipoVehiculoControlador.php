<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoVehiculo\StoreTipoVehiculoRequest;
use App\Http\Requests\TipoVehiculo\UpdateTipoVehiculoRequest;
use App\Models\TipoVehiculo;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoVehiculoControlador extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can('configuracion.tipo_vehi.crear')) {
            return redirect()->route('inicio');
        }
        return view('administrador.configuracion.tipoVehiculo');
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
    public function store(StoreTipoVehiculoRequest $request)
    {
        DB::beginTransaction();
        try {
            $tipoVehiculo           = new TipoVehiculo();
            $tipoVehiculo->nombre   = $request->nombre;
            $tipoVehiculo->estado   = 'activo';
            $tipoVehiculo->save();
            DB::commit();
            return response()->json(
                mensaje_mostrar('success', 'El registro se proceso con éxito')
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                mensaje_mostrar('error', 'Ocurrio un error al guardar')
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
            $tipoVehiculo = TipoVehiculo::find($id);
            $tipoVehiculo->estado = ($tipoVehiculo->estado == 'activo') ? 'inactivo' : 'activo';
            $tipoVehiculo->save();
            DB::commit();
            return response()->json(
                mensaje_mostrar('success', 'El estado fue procesada con éxito')
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                mensaje_mostrar('error', 'Ocurrio un error ')
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $tipoVehiculo = TipoVehiculo::find($id);
            if($tipoVehiculo){
                return response()->json(mensaje_mostrar('success', $tipoVehiculo));
            }else{
                return response()->json(mensaje_mostrar('error', 'No se encontró el registro'), 404);
            }
        } catch (\Exception $e) {
            return response()->json(mensaje_mostrar('error', 'Ocurrió un error inesperado'), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTipoVehiculoRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $tipoVehiculo = TipoVehiculo::find($id);
            if (!$tipoVehiculo) {
                DB::rollBack();
                return response()->json(
                    mensaje_mostrar('error', 'No se encontró el registro'),
                    404
                );
            }
            $tipoVehiculo->nombre = $request->nombre;
            $tipoVehiculo->save();
            DB::commit();
            return response()->json(
                mensaje_mostrar('success', 'Se editó con éxito'),
                200
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                mensaje_mostrar('error', 'Ocurrió un error inesperado'),
                500
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
            $tipoVehiculo = TipoVehiculo::find($id);
            if($tipoVehiculo){
                $tipoVehiculo->delete();
                DB::commit();
                return response()->json(
                    mensaje_mostrar('success', 'Se eliminó el registro'),
                    200
                );
            }else{
                DB::rollBack();
                return response()->json(
                    mensaje_mostrar('error', 'No se encontró el registro'),
                    404
                );
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                mensaje_mostrar('error', 'Ocurrió un error inesperado'),
                500
            );
        }
    }

    //para listar el registro
    public function listar() {

        $permissions = [
            'editar' => auth()->user()->can('configuracion.tarifa.editar'),
            'eliminar' => auth()->user()->can('configuracion.tarifa.eliminar'),
            'desactivar' => auth()->user()->can('configuracion.tarifa.desactivar'),
           
        ];
        $tiposVehiculos = TipoVehiculo::OrderBy('id', 'desc')->get();

        $data=[
            'permissions'=>$permissions,
            'tiposVehiculos'=>$tiposVehiculos,
        ];
        return response()->json($data);
    }

}
