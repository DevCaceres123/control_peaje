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
use Illuminate\Support\Facades\Log;
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
        $encargados_puesto = User::select('id', 'nombres', 'apellidos')
            ->role('encargado_puesto')
            ->where('estado','activo')
            ->get();


        $puestos = Puesto::select('id', 'nombre')->where('estado', 'activo')->get();
        return view('administrador.reporte.reporte', compact('encargados_puesto', 'puestos'));
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
            $user = User::select('id', 'nombres', 'apellidos')->where('id', $request->encargado)->role('encargado_puesto')->first();

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
        try {
            // Configurar el límite de memoria (opcional)
            ini_set('memory_limit', env('PHP_MEMORY_LIMIT', '2G'));

            // Formatear las fechas
            $fecha_actual = Carbon::now()->format('d-m-Y');
            $fecha_inicio = Carbon::parse($fecha_inicio)->format('Y-m-d');
            $fecha_fin = Carbon::parse($fecha_fin)->format('Y-m-d');

            // Obtener el puesto si las fechas son iguales
            $puesto = $fecha_inicio === $fecha_fin ? $this->obtenerPuesto($fecha_inicio, $usuario_actual) : null;

            // Obtener los registros y convertirlos en colecciones
            $registros = collect($this->obtenerRegistros($usuario_actual, $fecha_inicio, $fecha_fin));
            $registros_eliminados = collect($this->obtenerRegistrosEliminados($usuario_actual, $fecha_inicio, $fecha_fin));

            // Obtener los datos del usuario
            $nombreCompletoUsuario = User::select('id', 'nombres', 'apellidos')->where('id', $usuario_actual)->role('encargado_puesto')->first();

            // Generar el PDF
            $pdf = Pdf::loadView('administrador/pdf/reporteRegistros', compact('registros', 'puesto', 'nombreCompletoUsuario', 'registros_eliminados', 'fecha_actual', 'fecha_inicio', 'fecha_fin'));

            // Obtener el contenido binario del PDF y convertirlo a Base64
            return base64_encode($pdf->output());
        } catch (\Exception $e) {
            Log::error('Error al generar el reporte: ' . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al generar el reporte.'], 500);
        }
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
        $fecha_inicio = Carbon::parse($fecha_inicio)->startOfDay(); // 2025-01-07 00:00:00
        $fecha_fin = Carbon::parse($fecha_fin)->endOfDay(); // 2025-01-07 23:59:59

        if ($fecha_inicio === $fecha_fin) {
            $registros = HistorialRegistros::select('precio', 'placa', 'ci', 'num_aprobados', 'created_at')->where('usuario_id', '=', $usuario_actual)->whereDate('created_at', '=', $fecha_inicio)->get();
        } else {
            $registros = HistorialRegistros::select('precio', 'placa', 'ci', 'num_aprobados', 'created_at')
                ->where('usuario_id', $usuario_actual)
                ->whereBetween('created_at', [$fecha_inicio, $fecha_fin])
                ->get();
        }

        return $registros;
    }

    public function obtenerRegistrosEliminados($usuario_actual, $fecha_inicio, $fecha_fin)
    {
        $fecha_inicio = Carbon::parse($fecha_inicio)->startOfDay(); // 2025-01-07 00:00:00
        $fecha_fin = Carbon::parse($fecha_fin)->endOfDay(); // 2025-01-07 23:59:59
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

    public function reportes_fecha(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'fecha_inicio' => 'required|date',
                'fecha_final' => 'required|date',
                'puestos' => 'required|exists:puestos,id',
            ]);

            $puestos_ultimoRegistro = [];
            $registrospuesto = [];
            // obtener los datos del puesto
            foreach ($request->puestos as $puesto) {
                $puestos[] = Puesto::select('id', 'nombre')->where('id', $puesto)->first();
                $puestos_ultimoRegistro[] = $this->verificarFechasTurno($request->fecha_inicio, $request->fecha_final, $puesto);
            }

            foreach ($puestos_ultimoRegistro as $value) {
                $registros = $this->obtenerRegistrosPuesto($value['fecha_mas_corta'], $value['fecha_mas_alta'], $value['puesto_id']);

                // Combinar los registros de cada puesto en un único array
                foreach ($registros as $registro) {
                    $precio = $registro->precio;

                    if (isset($registrospuesto[$precio])) {
                        // Si el precio ya existe, sumar cantidad y total
                        $registrospuesto[$precio]['cantidad'] += $registro->cantidad;
                        $registrospuesto[$precio]['total'] += $registro->total;
                    } else {
                        // Si no existe, agregarlo como nuevo
                        $registrospuesto[$precio] = [
                            'precio' => $precio,
                            'cantidad' => $registro->cantidad,
                            'total' => $registro->total,
                        ];
                    }
                }
            }

            // Reorganizar los resultados como un array numérico
            $registrospuesto = array_values($registrospuesto);

            $reportebase64 = $this->generarReporteFecha($request->fecha_inicio, $request->fecha_final, $registrospuesto, $puestos);

            $this->mensaje('exito', $reportebase64);
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            $this->mensaje('error', 'Error ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    public function generarReporteFecha($fecha_inicio, $fecha_fin, $registros, $puestos)
    {
        try {
            // Configurar el límite de memoria (opcional)
            ini_set('memory_limit', env('PHP_MEMORY_LIMIT', '2G'));

            // Formatear las fechas

            $fecha_inicio = Carbon::parse($fecha_inicio)->format('Y-m-d H:i:s'); // 00:00:00
            $fecha_fin = Carbon::parse($fecha_fin); // Sin formatear aún

            // Si la hora y los minutos están definidos, pero no los segundos, ajustamos los segundos a 59
            if ($fecha_fin->second === 0) {
                $fecha_fin = $fecha_fin->seconds(59); // Establece los segundos a 59
            }

            $fecha_fin = $fecha_fin->format('Y-m-d H:i:s'); // 23:59:59

            $nombreCompletoUsuario = auth()
                ->user()
                ->only(['nombres', 'apellidos']);

            // cambiamos la fecha a espaniol para que sea mas entendible
            $fecha_inicio = Carbon::parse($fecha_inicio)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
            $fecha_fin = Carbon::parse($fecha_fin)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');

            // Generar el PDF
            $pdf = Pdf::loadView('administrador/pdf/reporteRegistros', compact('registros', 'nombreCompletoUsuario', 'fecha_inicio', 'fecha_fin', 'puestos'));

            // Obtener el contenido binario del PDF y convertirlo a Base64
            return base64_encode($pdf->output());
        } catch (\Exception $e) {
            Log::error('Error al generar el reporte: ' . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al generar el reporte.'], 500);
        }
    }

    public function verificarFechasTurno($fecha_inicio, $fecha_final, $puesto)
    {
        // Asegurarnos de que la fecha esté en el formato correcto
        $fecha_inicio = Carbon::parse($fecha_inicio)->format('Y-m-d');
        $fecha_final = Carbon::parse($fecha_final)->format('Y-m-d');

        // Realizar la consulta para obtener la fecha más alta y la más corta
        // Realizar la consulta para obtener la fecha más alta
        $fecha_mas_corta = DB::table('historial_puesto')
            ->where('puesto_id', $puesto) // Filtrar por el puesto dinámico
            ->whereDate('created_at', $fecha_inicio) // Filtrar por la fecha sin hora
            ->min('created_at'); // Obtener la fecha más alta de updated_at

        $fecha_mas_alta = DB::table('historial_puesto')
            ->where('puesto_id', $puesto) // Filtrar por el puesto dinámico
            ->whereDate('created_at', $fecha_final) // Filtrar por la fecha sin hora
            ->max('updated_at'); // Obtener la fecha más alta de updated_at

        return [
            'puesto_id' => $puesto,
            'fecha_mas_alta' => $fecha_mas_alta,
            'fecha_mas_corta' => $fecha_mas_corta,
        ];
    }

    // Se obtiene los registros de un puestos en un rango de fechas
    public function obtenerRegistrosPuesto($fecha_inicio, $fecha_fin = null, $puesto_id)
    {
        // Procesar las fechas de inicio y fin

        if ($fecha_inicio != null && $fecha_fin != null) {
            $fecha_inicio = Carbon::parse($fecha_inicio)->format('Y-m-d H:i:s');
            $fecha_fin = Carbon::parse($fecha_fin)->format('Y-m-d H:i:s');
        }

        return $registros = DB::table('registros')
            ->join('tarifas', 'registros.tarifa_id', '=', 'tarifas.id')
            ->select('tarifas.precio', DB::raw('COUNT(registros.id) as cantidad'), DB::raw('SUM(tarifas.precio) as total'))
            ->where('puesto_id', $puesto_id)
            ->whereBetween('registros.created_at', [$fecha_inicio, $fecha_fin])
            ->whereNull('registros.deleted_at') // Excluye registros eliminados
            ->groupBy('tarifas.precio')
            ->get();
    }

    // GENERAR REPORTEN POR USUARIO
    public function reportes_usuario(Request $request){

        
        try {
            $validatedData = $request->validate([
                'fecha_inicio_usuario' => 'required|date',             
                'encargados_puesto' => 'required|exists:users,id',
            ]);

            $puestos_ultimoRegistro = [];
            $registros=[];
            foreach ($request->encargados_puesto as $key => $value) {
                $registros[] = $this->generarReporteUsuario($value,$request->fecha_inicio_usuario);
            }

            
            $nombreCompletoUsuario = auth()->user()->only(['nombres', 'apellidos']);
        

            $pdf = Pdf::loadView('administrador/pdf/reporteRegistrosUsuario', compact('registros','nombreCompletoUsuario'));

            // Obtener el contenido binario del PDF y convertirlo a Base64
            $reportebase64= base64_encode($pdf->output());

            $this->mensaje('exito', $reportebase64);
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            $this->mensaje('error', 'Error ' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }


    public function generarReporteUsuario($usuario_actual,$fecha){

        
        $fecha_actual=Carbon::parse($fecha);
        $turnos = $this->obtenerTurno($usuario_actual, $fecha_actual);
        $nombreCompletoUsuario = User::select('id','nombres','apellidos')->where('id',$usuario_actual)->first();

        // Arreglo para almacenar los registros por turno
        $registros_por_turno = [];
        
        foreach ($turnos as $turno) {
            $entrada = $turno->created_at;
            $salida = $turno->updated_at;

            // Obtener los registros del historial para este turno
            $registros_turno = HistorialRegistros::select('precio')
                ->where('usuario_id', '=', $usuario_actual)
                ->whereBetween('created_at', [$entrada, $salida])
                ->get();


            // Agrupar registros por precio
            $registros_agrupados = $registros_turno->groupBy('precio')->map(function ($grupo) {
                return [
                    'cantidad' => $grupo->count(),  // Número de transacciones
                    'total' => $grupo->sum('precio'),  // Suma total de ese precio
                ];
            });

            // Almacenar los registros por turno
            $registros_por_turno[] = [
                'nombreEncargado'=>$nombreCompletoUsuario,
                'puesto' => Puesto::select('id','nombre')->where('id',$turno->puesto_id)->first(),
                'entrada' => Carbon::parse($turno->created_at)->translatedFormat('d \d\e F \d\e Y H:i:s '),
                'salida' => Carbon::parse($turno->updated_at)->translatedFormat('d \d\e F \d\e Y H:i:s '),
                'registros' => $registros_turno,  // Registros de historial
                'registros_agrupados' => $registros_agrupados,

            ];
        }


        return $registros_por_turno;
      
    }


    public function obtenerTurno($id_usuario, $fecha_actual)
    {

        return DB::table('historial_puesto')
                ->where('usuario_id', $id_usuario)
                ->whereDate('created_at', $fecha_actual)
                ->get();
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {}

    // MENSAJES PARA ENVIARLOS DE RESPUESTA
    public function mensaje($titulo, $mensaje)
    {
        $this->mensaje = [
            'tipo' => $titulo,
            'mensaje' => $mensaje,
        ];
    }
}
