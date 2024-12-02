<?php

namespace App\Http\Controllers\Peaje;

use App\Http\Controllers\Controller;
use App\Models\Puesto;
use Carbon\Carbon;
use Illuminate\Http\Request;

use function Laravel\Prompts\select;

class Controlador_registro extends Controller
{

    public function index()
    {
        $fecha_actual= new Carbon();
        $puestos = Puesto::with(['users' => function ($query,$fecha_actual) {
            $query->wherePivot('created_at', '=', $fecha_actual);
        }])->with(['users' => function ($query) {

            $query->select('users.id', 'nombres','apellidos');
        }])
        ->where('estado','activo')
        ->get();

     
        return view("administrador.puestos.asignar_puesto", compact('puestos'));
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
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
