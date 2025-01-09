<?php

namespace App\Http\Controllers\Reporte;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reporte\ReporteRequest;
use App\Models\HistorialRegistros;
use App\Models\Puesto;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // Asegúrate de importar esta clase

class Controlador_reporte extends Controller
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
        $encargados_puesto = User::select('id', 'nombres', 'apellidos')->role('encargado_puesto')->get();
        return view('administrador.reporte.reporte', compact('encargados_puesto'));
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
    public function store(ReporteRequest $request)
    {
        try {
            $user = User::select('id', 'nombres', 'apellidos')
                ->where('id', $request->encargado)
                ->role('encargado_puesto')
                ->first();

            if (!$user) {
                throw new Exception('Error el usuario no tiene el rol correspondiente');
            }

            $reporte = $this->generarReporte($user->id, $request->fecha_inicio, $request->fecha_final);
            $this->mensaje('exito', $reporte);
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            $this->mensaje('error', 'Error ' . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }

    //generamos el reporte segun fecha
    public function generarReporte($usuario_actual, $fecha_inicio, $fecha_fin)
    {
        $fecha_actual = Carbon::now()->format('d-m-Y');
        $fecha_inicio = Carbon::parse($fecha_inicio)->format('Y-m-d');
        $fecha_fin = Carbon::parse($fecha_fin)->format('Y-m-d');

        //Si tenemos las misma fecha entonces nos mostrara el puesto del dia seleccionado
        if ($fecha_inicio === $fecha_fin) {
            $puesto = $this->obtenerPuesto($fecha_inicio, $usuario_actual);
        } else {
            $puesto = null;
        }

        $registros = $this->obtenerRegistros($usuario_actual, $fecha_inicio, $fecha_fin);

        $registros_eliminados = $this->obtenerRegistrosEliminados($usuario_actual, $fecha_inicio, $fecha_fin);

        $nombreCompletoUsuario = User::select('id', 'nombres', 'apellidos')->where('id', $usuario_actual)->role('encargado_puesto')->first();

        $pdf = Pdf::loadView('administrador/pdf/reporteRegistros', compact('registros', 'puesto', 'nombreCompletoUsuario', 'registros_eliminados', 'fecha_actual' ,'fecha_inicio','fecha_fin'));
        // Obtener el contenido binario del PDF
        $pdfContent = $pdf->output();

        // Convertir el contenido binario a Base64
        return base64_encode($pdfContent);
    }

    public function obtenerPuesto($fecha_actual, $usuario_actual)
    {
        return Puesto::select('id', 'nombre')
            ->whereHas('users', function ($query) use ($usuario_actual, $fecha_actual) {
                $query->where('historial_puesto.usuario_id', '=', $usuario_actual)->whereDate('historial_puesto.created_at', '=', $fecha_actual);
            })
            ->first();
    }

    //obtenemos los registros del historial de registros
    public function obtenerRegistros($usuario_actual, $fecha_inicio, $fecha_fin)
    {
        $fecha_inicio = Carbon::parse($fecha_inicio)->startOfDay();  // 2025-01-07 00:00:00
        $fecha_fin = Carbon::parse($fecha_fin)->endOfDay();// 2025-01-07 23:59:59

        if ($fecha_inicio === $fecha_fin) {
            $registros = HistorialRegistros::select('precio', 'placa', 'ci', 'num_aprobados','created_at')->where('usuario_id', '=', $usuario_actual)->whereDate('created_at', '=', $fecha_inicio)->get();
        } else {
            $registros = HistorialRegistros::select('precio', 'placa', 'ci', 'num_aprobados','created_at')
                ->where('usuario_id', $usuario_actual)
                ->whereBetween('created_at', [$fecha_inicio, $fecha_fin])
                ->get();
        }

        return $registros;
    }

    public function obtenerRegistrosEliminados($usuario_actual, $fecha_inicio, $fecha_fin)
    {
        $fecha_inicio = Carbon::parse($fecha_inicio)->startOfDay();  // 2025-01-07 00:00:00
        $fecha_fin = Carbon::parse($fecha_fin)->endOfDay();// 2025-01-07 23:59:59
        if ($fecha_inicio === $fecha_fin) {
            $registros_eliminados = DB::table('delete_tarifas')->join('tarifas', 'tarifas.id', '=', 'delete_tarifas.tarifa_id')->select('precio', 'delete_tarifas.created_at')->where('usuario_id', '=', $usuario_actual)->whereDate('delete_tarifas.created_at', '=', $fecha_inicio)->get();
        } else {
            // listar los registros eliminados
            $registros_eliminados = DB::table('delete_tarifas')
                ->join('tarifas', 'tarifas.id', '=', 'delete_tarifas.tarifa_id')
                ->select('precio', 'delete_tarifas.created_at')
                ->where('usuario_id', '=', $usuario_actual)
                ->whereBetween('delete_tarifas.created_at', [$fecha_inicio, $fecha_fin])
                ->get();
        }

        return $registros_eliminados;
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
    }

    // MENSAJES PARA ENVIARLOS DE RESPUESTA
    public function mensaje($titulo, $mensaje)
    {
        $this->mensaje = [
            'tipo' => $titulo,
            'mensaje' => $mensaje,
        ];
    }
}
