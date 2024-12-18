<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Color\StoreColorRequest;
use App\Http\Requests\Color\UpdateColorRequest;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ColorControlador extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can('configuracion.color.crear')) {
            return redirect()->route('inicio');
        }
        return view('administrador.configuracion.colores');
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
    public function store(StoreColorRequest $request)
    {
        DB::beginTransaction();
        try {
            $color = new Color();
            $color->nombre = $request->nombre;
            $color->color  = $request->color;
            $color->save();
            DB::commit();
            return response()->json(mensaje_mostrar('success', 'Se inserto con éxito'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(mensaje_mostrar('error', 'Ocurrio un erro inesperado'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $color = Color::find($id);
        if($color){
            return response()->json(mensaje_mostrar('success', $color));
        }else{
            return response()->json(mensaje_mostrar('error', 'No existe el registro!'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateColorRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $color = Color::find($id);
            if(!$color){
                DB::rollBack();
                return response()->json(mensaje_mostrar('error', 'No existe el registro'));
            }
            $color->nombre  = $request->nombre;
            $color->color   = $request->color;
            $color->save();
            DB::commit();
            return response()->json(mensaje_mostrar('success', 'El registro se edito con éxito'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(mensaje_mostrar('error', 'Ocurrio un error inesperado'  ));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $color = Color::find($id);
            if($color){
                $color->delete();
                DB::commit();
                return response()->json(mensaje_mostrar('success', 'Se elimino el registro con éxito'));
            }else{
                DB::rollBack();
                return response()->json(mensaje_mostrar('error', 'No existe el registro'));
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(mensaje_mostrar('error', 'Ocurrio un error inesperado'));
        }
    }

    //para listar 
    public function listar() {

        $permissions = [
            'editar' => auth()->user()->can('configuracion.color.editar'),
            'eliminar' => auth()->user()->can('configuracion.color.eliminar'),
            'desactivar' => auth()->user()->can('configuracion.color.desactivar'),
           
        ];
        $color = Color::OrderBy('id', 'desc')->get(); 

        $data=[
            'permissions'=>$permissions,
            'color'=>$color,
        ];
        return response()->json($data);
    }
}
