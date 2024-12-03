<?php

namespace App\Http\Controllers\Peaje;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Puesto;
use App\Models\Tarifa;
use App\Models\TipoVehiculo;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Http\Request;

use function Laravel\Prompts\select;

class Controlador_registro extends Controller
{

    public $mensaje = [];
    public $fecha;

    public function __construct()
    {
        // Asignar la fecha actual a la propiedad pública
        $this->fecha = Carbon::now();
    }

    public function index()
    {

        $fecha_actual = $this->fecha->toDateString();
        $usuario_actual = auth()->user()->id;
        $vehiculos = TipoVehiculo::select('id', 'nombre')->get();
        $colores = Color::select('id', 'nombre')->get();
        $tarifas = Tarifa::select('id', 'nombre', 'precio')
            ->where('estado', 'activo')
            ->get();



        $puestos_registrado_usuario = Puesto::select('id', 'nombre')
            ->whereHas('users', function ($query) use ($usuario_actual, $fecha_actual) {
                $query->where('historial_puesto.usuario_id', '=', $usuario_actual)
                    ->whereDate('historial_puesto.created_at', '=', $fecha_actual);
            })
            ->first();

        return view("administrador.control_peaje.generar_registro", compact('tarifas', 'vehiculos', 'colores', 'puestos_registrado_usuario'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    // Generar QR para resivo
    public function generar_qr(Request $request) {
        return $request->all();
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
