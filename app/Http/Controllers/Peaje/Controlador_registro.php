<?php

namespace App\Http\Controllers\Peaje;

use App\Http\Controllers\Controller;
use App\Http\Requests\Peaje\RegistroPeajeRequest;
use App\Models\Color;
use App\Models\DeleteTarifas;
use App\Models\HistorialRegistros;
use App\Models\Persona;
use App\Models\Puesto;
use App\Models\Registro;
use App\Models\Tarifa;
use App\Models\TipoVehiculo;
use App\Models\User;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\select;
use function PHPUnit\Framework\isNumeric;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf; // Asegúrate de importar esta clase
use Exception;
use Hamcrest\Type\IsNumeric;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Hashids\Hashids; // libreria para encriptar y descencriptar
use PhpParser\Node\Expr\Cast\String_;

class Controlador_registro extends Controller
{

    public $mensaje = [];
    public $fecha;
    private $hashids;

    public function __construct()
    {
        // Asignar la fecha actual a la propiedad pública
        $this->fecha = Carbon::now();
        $this->hashids = new Hashids(env('HASHIDS_SALT', 'clave-secreta'), 10); // Sal y longitud mínima
    }

    public function index()
    {
        if (!auth()->user()->can('control.generar.inicio')) {
            return redirect()->route('inicio');
        }
        $fecha_actual = $this->fecha->toDateString();
        $usuario_actual = auth()->user()->id;
        $vehiculos = TipoVehiculo::select('id', 'nombre')
            ->where('estado', 'activo')
            ->get();
        $colores = Color::select('id', 'nombre')
            ->get();
        $tarifas = Tarifa::select('id', 'nombre', 'precio')
            ->where('estado', 'activo')
            ->get();



        $puestos_registrado_usuario = $this->obtenerPuesto($fecha_actual, $usuario_actual);

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
    public function generar_qr(Request $request)
    {
        $tarifa = null;
        try {
            $validatedData = $request->validate([
                'id_tarifa' => 'required|exists:tarifas,id',
            ]);
            DB::beginTransaction();
            $fecha_actual = $this->fecha->toDateString();
            $usuario_actual = auth()->user()->id;
            // return $request->all();

            // se obtiene datos del puesto
            $puesto = $this->obtenerPuesto($fecha_actual, $usuario_actual);


            if (!$puesto) {
                throw new Exception(' el usuario no tiene asignado un puesto');
            }

            // Se obtiene datos de la tarifa
            $tarifa = Tarifa::find($request->id_tarifa);

            $registro = new Registro();
            $registro->puesto_id = $puesto->id;
            $registro->tarifa_id = $tarifa->id;
            $registro->usuario_id = $usuario_actual;

            $registro->save();

            $cod_qr_unico = $this->encrypt($registro->id);
           
            $nuevo_Qr = $this->generar_qrReporte($cod_qr_unico);

            // se obtiene el pdf en base 64
            $resultado = $this->generar_ReportePdf($tarifa, $nuevo_Qr, $fecha_actual, $puesto, null);

            // guardar el cod_qr generado
            $registro->codigo_qr = $cod_qr_unico;
            $registro->save();


            // Datos que necesito para generar el reporte
            $data = [
                'tarifa' => $tarifa,
                'nuevo_Qr' => $cod_qr_unico,
                'fecha_actual' => Carbon::now()->format('Y-m-d H:i:s'),
                'puesto' => $puesto,
                'vehiculo' => null,
            ];


            $this->guardarHistorialRegistro($cod_qr_unico, $puesto->nombre, $tarifa->precio, null, null, json_encode($data), $registro->id);


            $respuesta=[
                'cod_qr_unico'=>$cod_qr_unico,
                'resultado'=>$resultado,
            ];


            DB::commit();
            // Retornar el PDF para su visualización
            $this->mensaje("exito", $respuesta);

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();
              // Log de error solo si ocurre una excepción
            Log::error('Error al generar QR', [
                'error_message' => $e->getMessage(),
                'user_id' => auth()->user()->only(['nombres', 'apellidos']), // Usuario que causó el error
                'tarifa_id' => $tarifa ? $tarifa->precio : 'No disponible', // Tarifa asociada al error
          ]);
            $this->mensaje("error", "Error " . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }


    // actualizar la boleta a imprimido

    public function marcarBoletaImpresa($CodigoQr){
       
        try {
    
               
            DB::beginTransaction();
    
            $boleta = HistorialRegistros::where('cod_qr', $CodigoQr)->first();
            if (!$boleta) {

                throw new Exception("Error la Boleta no existe");                
            }
            
             // Actualizar el estado de impresión
            $boleta->estado_impresion = "impreso";  // Cambia según tu lógica de estados
            $boleta->save(); // Guardar cambios en la base de datos
    
            DB::commit();
    

            
            $this->mensaje('exito', "Impreso Correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
    
            DB::rollBack();
            $this->mensaje("error", "Error " . $e->getMessage());
    
            return response()->json($this->mensaje, 200);
        }
        
    }

    // verificamos si existen boletas que no se hayan imprimido
    
    public function verificarBoletasNoimpresas() {
        $usuario_actual = auth()->user()->id;
    
        $boletas = HistorialRegistros::select('id', 'nombre_usuario', 'precio', 'created_at','cod_qr')
            ->where('estado_impresion', 'pendiente')
            ->where('usuario_id', $usuario_actual)
            ->orderBy('created_at', 'desc')
            ->get();

        $boletas = $boletas->map(function ($registro) {
            return [
                'id' =>  $registro->id,
                'cod_qr' => $registro->cod_qr,
                'nombre_usuario' => $registro->nombre_usuario,
                'precio' => $registro->precio,
                'created_at' => Carbon::parse($registro->created_at)
                    ->locale('es')
                    ->translatedFormat('d/m/Y H:i:s'), // Incluye hora y minuto
            ];
        });
    
        return response()->json($boletas);
    }
    
    
    
    

    public function obtenerPuesto($fecha_actual, $usuario_actual)
    {
        return Puesto::select('id', 'nombre')
            ->whereHas('users', function ($query) use ($usuario_actual, $fecha_actual) {
                $query->where('historial_puesto.usuario_id', '=', $usuario_actual)
                    ->where('historial_puesto.estado', '=', 'activo');
            })
            ->first();
    }


    // Nos generara un qr a partir de el id del registro y nos devuelve en base 64
    public function generar_qrReporte($cod_qr_unico)
    {
        // Generar el QR como imagen base64
        $qrCode = QrCode::format('svg')->size(150)->generate($cod_qr_unico);
        return  base64_encode($qrCode);
    }


    // Nos generara la boleta de pago

    public function generar_ReportePdf($tarifa, $qrCodeBase64, $fecha_actual, $puesto, $vehiculo)
    {

        if (Carbon::parse($fecha_actual)->format('H:i:s') === '00:00:00') {

            $fecha_actual = Carbon::now();
        }

        // si el vehiculo es distinto de nullo o vacio
        $color = "";
        $tipo_auto = "";


        if ($vehiculo) {
            $color = Color::select('nombre')->where('id', $vehiculo->color_id)->first();
            $tipo_auto = TipoVehiculo::select('nombre')->where('id', $vehiculo->tipovehiculo_id)->first();

            $data = [
                'tarifa' => $tarifa,
                'qrCodeBase64' => $qrCodeBase64,
                'fecha_finalizacion' => Carbon::parse($fecha_actual)->endOfDay(), // Obtener la fecha actual con hora 23:59:59
                'fecha_generada' => $fecha_actual,
                'usuario' => auth()->user()->only(['nombres', 'apellidos']),
                'puesto' => $puesto,
                'color' => $color->nombre ?? null,
                'tipo_auto' => $tipo_auto->nombre ?? null,
                'placa' => $vehiculo->placa ?? null,
            ];
        } else {
            $data = [
                'tarifa' => $tarifa,
                'qrCodeBase64' => $qrCodeBase64,
                'fecha_finalizacion' => Carbon::parse($fecha_actual)->endOfDay(), // Obtener la fecha actual con hora 23:59:59
                'fecha_generada' => $fecha_actual,
                'usuario' => auth()->user()->only(['nombres', 'apellidos']),
                'puesto' => $puesto,
                'color' => null,
                'tipo_auto' => null,
                'placa' =>  null,
            ];
        }


        // Crear instancia


        $pdf = Pdf::loadView('administrador/pdf/boletaPago', $data)
            ->setPaper([0, 0, 226.77, 841.89]); // 80 mm tamaño de papel

        // Obtener el contenido binario del PDF
        $pdfContent = $pdf->output();

        // Convertir el contenido binario a Base64
        return  base64_encode($pdfContent);
    }


    // Encriptar el ID
    public function encrypt($id)
    {
        return $this->hashids->encode($id);
    }

    // Desencriptar el ID
    public function decrypt($hashedId)
    {
        $decoded = $this->hashids->decode($hashedId);
        return count($decoded) > 0 ? $decoded[0] : null;
    }



    //GENERAR QR CON DATOS DEL VEHICULO O PERSONA
    public function store(RegistroPeajeRequest $request)
    {
        $tarifa = null;
        try {
            $validatedData = $request->validate([
                'id_tarifa' => 'required|exists:tarifas,id',
            ]);


            $fecha_actual = $this->fecha->toDateString();
            $usuario_actual = auth()->user()->id;
            // return $request->all();

            // se obtiene datos del puesto
            $puesto = $this->obtenerPuesto($fecha_actual, $usuario_actual);


            if (!$puesto) {
                throw new Exception(' el usuario no tiene asignado un puesto');
            }
            DB::beginTransaction();

            // Se obtiene datos de la tarifa
            $tarifa = Tarifa::find($request->id_tarifa);

            // se genran los datos de la persona
            $persona = $this->guardarPersona($request->ci, $request->nombres, $request->ap_materno, $request->ap_paterno);


            // se genran los datos del vehiculo
            $vehiculo = $this->guardarVehiculo($request->placa, $persona->id, $request->id_color, $request->id_tipo_veh);


            //  SE REGISTRA LOS DATOS EN LA BASE DE DATOS SI TODO ESTA CORRECTO
            $id_registro = $this->nuevoRegistro($puesto->id, $tarifa->id, $usuario_actual, $vehiculo);

            // GENERAMOS EL QR ENCRIPTADO Y EL REPORTE 


            // se encripta el el id del registro
            $cod_qr_unico = $this->encrypt($id_registro);

            // Se genera el qr apartir de la encriptacion del registro
            $nuevo_Qr = $this->generar_qrReporte($cod_qr_unico);

            // se obtiene el pdf en base 64
            $resultado = $this->generar_ReportePdf($tarifa, $nuevo_Qr, $fecha_actual, $puesto, $vehiculo);

            // guardar el cod_qr generado en la base de datos

            $this->registrarQr($cod_qr_unico, $id_registro);

            $data = [
                'tarifa' => $tarifa,
                'nuevo_Qr' => $cod_qr_unico,
                'fecha_actual' => Carbon::now()->format('Y-m-d H:i:s'),
                'puesto' => $puesto,
                'vehiculo' => $vehiculo->id ? $vehiculo : null,
            ];

            $this->guardarHistorialRegistro($cod_qr_unico, $puesto->nombre, $tarifa->precio, $vehiculo->placa, $persona->ci,  json_encode($data), $id_registro);


            DB::commit();


            $respuesta=[
                'cod_qr_unico'=>$cod_qr_unico,
                'resultado'=>$resultado,
            ];
            $this->mensaje('exito', $respuesta);
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {

            DB::rollBack();
                  // Log de error solo si ocurre una excepción
                Log::error('Error al generar QR', [
                    'error_message' => $e->getMessage(),
                    'user_id' => auth()->user()->only(['nombres', 'apellidos']), // Usuario que causó el error
                    'tarifa_id' => $tarifa ? $tarifa->precio : 'No disponible', // Tarifa asociada al error
                ]);
            $this->mensaje("error", "Error " . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }

    public function guardarPersona($ci, $nombre, $paterno, $materno)
    {

        // Si el usuario quiere registrar a una nueva persona necesita poner el ci
        // caso contrario no registramos nada y solo enviamos null
        if ($nombre != null | $paterno != null | $materno != null) {
            if ($ci == null) {
                throw new Exception('para registrar los datos de una persona se necsita el documento de identidad');
            }
        }

        if ($ci != null) {
            // Si encentra un ci registrado obtiene su id caso contrario se le asignara el ci registrado
            $datos_ci = Persona::select('id', 'ci', 'nombres')->where('ci', $ci)->first();

            // Si encentra un ci ya regitrado con ese dato entonces no es necesario crear una nueva persona
            if ($datos_ci) {
                return $datos_ci;
            }

            $datos_ci = $ci;

            $persona = new Persona();
            $persona->ci = $datos_ci;
            $persona->nombres = $nombre;
            $persona->ap_paterno = $paterno;
            $persona->ap_materno = $materno;

            $persona->save();

            return $persona;
        }

        $persona = [
            'id' => null,
            'ci' => null,
        ];
        return  $personaObject = json_decode(json_encode($persona));
    }

    // guardamos datos del vehiculo

    public function guardarVehiculo($placa, $persona, $color, $vehiculo)
    {


        // Si el usuario quiere registrar a una nueva persona necesita poner el ci
        // caso contrario no registramos nada y solo enviamos null
        if ($color != null | $vehiculo != null) {
            if ($placa == null) {
                throw new Exception('para registrar los datos del vehiculo se necesita el numero de placa');
            }
        }

        if ($placa != null) {

            $vehiculo_nuevo = new Vehiculo();
            $vehiculo_nuevo->placa = $placa;
            $vehiculo_nuevo->persona_id = $persona;
            $vehiculo_nuevo->color_id = $color;
            $vehiculo_nuevo->tipovehiculo_id  = $vehiculo;
            $vehiculo_nuevo->save();

            return $vehiculo_nuevo;
        }

        $vehiculo = [
            'id' => null,
            'placa' => null,
            'color_id' => null,
            'tipovehiculo_id' => null,
        ];
        return  $personaObject = json_decode(json_encode($vehiculo));
    }

    public function nuevoRegistro($puesto, $tarifa, $usuario_actual, $vehiculo)
    {


        $registro = new Registro();
        $registro->puesto_id = $puesto;
        $registro->tarifa_id = $tarifa;
        $registro->usuario_id  = $usuario_actual;
        $registro->vehiculo_id   = $vehiculo->id;

        $registro->save();

        return $registro->id;
    }

    public function registrarQr($qr, $id_registro)
    {

        $registro = Registro::find($id_registro);

        $registro->codigo_qr = $qr;

        $registro->save();
    }


    // Se guardaron todos los datos generados para poder relizar una mejor busqueda y poder generar el reporte de forma mas rapida
    public function guardarHistorialRegistro($codqr, $puestoNombre, $precio, $placa, $ciPersona, $reporte, $registroId)
    {

        $nombres = auth()->user()->only(['nombres', 'apellidos']);

        $registro_historial = new HistorialRegistros();

        $registro_historial->cod_qr = $codqr;
        $registro_historial->puesto = $puestoNombre;
        $registro_historial->nombre_usuario =  $nombres['nombres'] . " " . $nombres['apellidos'];
        $registro_historial->precio = $precio;
        $registro_historial->placa = $placa;
        $registro_historial->ci = $ciPersona;
        $registro_historial->reporte_json = $reporte;

        $registro_historial->usuario_id = auth()->user()->id;
        $registro_historial->registro_id = $registroId;
        $registro_historial->estado_impresion = 'pendiente';

        $registro_historial->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $ci_persona)
    {
        try {

            $persona = Persona::select('ci', 'nombres', 'ap_paterno', 'ap_materno')
                ->where('ci', $ci_persona)
                ->first();

            if (!$persona) {
                throw new Exception('sin resultados');
            }
            $this->mensaje("exito", $persona);

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            $this->mensaje("error", "Error " . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }

    // verificamos que el qr enviado haya sido generado por nosotros y que este en rango de fecha
    public function verificarQr(string $qr)
    {
        try {

            DB::beginTransaction();

            $registro = Registro::select('id', 'codigo_qr', 'created_at')
                ->where('codigo_qr', $qr)
                ->first();


            if (!$registro) {
                throw new Exception('el codigo escaneado no  fue generado en ningun puesto de peaje');
            }


            $fecha_actual = $this->fecha;
            $fecha_vencimiento = Carbon::parse($registro->created_at->endOfDay());
            $registro_fecha = Carbon::parse($registro->created_at); //feccha de registro del qr

            if ($fecha_actual->isAfter($fecha_vencimiento)) {

                throw new Exception('el QR ya no es valido...!! ' . $registro_fecha);
            }

            // Se aumenta el conteo de las veces que paso el qr
            $registro_historial = HistorialRegistros::where('registro_id', $registro->id)->first();
            $registro_historial->num_aprobados =  $registro_historial->num_aprobados + 1;
            $registro_historial->save();

            DB::commit();

            $this->mensaje('exito', "El QR escaneado es valido" . " " . $registro_fecha);
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {

            DB::rollBack();
            $this->mensaje("error", "Error " . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }


    // Se redirigue a los registros generados por los puestos
    public function ver_registros()
    {

        $encargados_puesto = User::select('id', 'nombres', 'apellidos')
            ->role('encargado_puesto')
            ->get();

        return view("administrador.control_peaje.listar_registro", compact('encargados_puesto'));
    }


    public function listar_registro(Request $request)
    {


        if (!auth()->user()->can('control.listar.inicio')) {
            return redirect()->route('inicio');
        }


        $fecha_actual = $request->input('fecha') ? Carbon::parse($request->input('fecha'))->toDateString() : null;
        $encargado = $request->input('encargado') ?? null;

        // Inicia la consulta con los usuarios y sus puestos
        $registroQuery = HistorialRegistros::select('id', 'puesto', 'nombre_usuario', 'precio', 'placa', 'ci', 'created_at', 'num_aprobados','cod_qr');



        // Se aplica este friltro si la fecha y encargado no son campos vacios
        if ($fecha_actual && $encargado) {
            $registroQuery->where('usuario_id', $encargado);
            $registroQuery->whereDate('created_at', '=', $fecha_actual);
        }


        // Aplica el filtro de fecha solo si se proporciona una fecha
        if ($fecha_actual  && $encargado == null) {
            $registroQuery->where('usuario_id', auth()->user()->id);
            $registroQuery->whereDate('created_at', '=', $fecha_actual);
        }




        // Ordena los registros
        $registroQuery->orderBy('created_at', 'desc');


        // Filtro de búsqueda: Aplica filtros solo si hay valor de búsqueda
        if (!empty($request->search['value'])) {
            $registroQuery->where(function ($query) use ($request) {
                $query->where('puesto', 'like', '%' . $request->search['value'] . '%')
                    ->orWhere('nombre_usuario', 'like', '%' . $request->search['value'] . '%')
                    ->orWhere('precio', 'like', '%' . $request->search['value'] . '%')
                    ->orWhere('placa', 'like', '%' . $request->search['value'] . '%')
                    ->orWhere('cod_qr',$request->search['value'])
                    ->orWhere('ci', 'like', '%' . $request->search['value'] . '%');
            });
        }

        // Total de registros antes del filtrado
        $recordsTotal = HistorialRegistros::where('usuario_id', auth()->user()->id)->count();

        // Total de registros filtrados
        $recordsFilter = $registroQuery->count();

        // Paginación: Aplicar `skip` y `take` sobre la consulta
        $datos_registros = $registroQuery
            ->skip($request->start)
            ->take($request->length)
            ->get();

        // Transformar los datos para formatear `created_at`
        $datos_registros = $datos_registros->map(function ($registro) {
            return [
                'id' =>  $registro->id,
                'puesto' => $registro->puesto,
                'nombre_usuario' => $registro->nombre_usuario,
                'precio' => $registro->precio,
                'placa' => $registro->placa,
                'ci' => $registro->ci,
                'num_aprobados' => $registro->num_aprobados,
                'created_at' => Carbon::parse($registro->created_at)
                    ->locale('es')
                    ->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i:s'), // Incluye hora y minuto
            ];
        });


        // Permisos (para el frontend, si es necesario)
        $permissions = [
            'eliminar' => auth()->user()->can('control.listar.eliminar'),
            'reporte' => auth()->user()->can('control.listar.generar_boleta'),

        ];

        // Retorna el JSON con los datos y los totales
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFilter,
            'registros' => $datos_registros,
            'permissions' => $permissions,
        ]);
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

        try {

            $registro_historial = HistorialRegistros::where('id', $id)->first();

            if (!$registro_historial) {
                throw new Exception("no existe el historial de registro");
            }
            $registro = Registro::where('id', $registro_historial->registro_id)->first();

            if (!$registro) {
                throw new Exception("no existe el registro");
            }

            DB::beginTransaction();

            $registro_historial->delete();
            $registro->delete();


            $registro_eliminado = new DeleteTarifas();
            $registro_eliminado->usuario_id = auth()->user()->id;
            $registro_eliminado->registro_id = $registro_historial->registro_id;
            $registro_eliminado->tarifa_id = $registro->tarifa_id;

            $registro_eliminado->save();

            DB::commit();

            $this->mensaje('exito', "El registro fue eliminado correctamente");
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {

            DB::rollBack();
            $this->mensaje("error", "Error " . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }



    // Funcion para genearar reportes diarios
    public function reporteDiario(Request $request)
    {



        $usuario_actual = auth()->user()->id;
        $fecha_actual = $request->input('fecha', now()->toDateString());

        //$puesto = $this->obtenerPuesto($fecha_actual, $usuario_actual);

        $turnos = $this->obtenerTurno($usuario_actual, $fecha_actual);
        $nombreCompletoUsuario = auth()->user()->only(['nombres', 'apellidos']);

        // Arreglo para almacenar los registros por turno
        $registros_por_turno = [];
        $registros_eliminados_por_turno = [];


        foreach ($turnos as $turno) {
            $entrada = $turno->created_at;
            $salida = $turno->updated_at;

            // Obtener los registros del historial para este turno
            $registros_turno = HistorialRegistros::select('id','precio', 'placa', 'ci', 'num_aprobados', 'created_at')
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

            $registros_eliminados=DB::table('historial_registros')
              ->select('precio', 'deleted_at')
              ->where('usuario_id', $usuario_actual)
               ->whereBetween('created_at', [$entrada, $salida])
               ->whereNotNull('deleted_at') // Filtrar solo los eliminados
              ->get();
            
            // Almacenar los registros por turno
            $registros_por_turno[] = [
                'puesto' => Puesto::find($turno->puesto_id),
                'entrada' => Carbon::parse($turno->created_at)->translatedFormat('l, d \d\e F \d\e Y H:i:s '),
                'salida' => Carbon::parse($turno->updated_at)->translatedFormat('l, d \d\e F \d\e Y H:i:s '),
                'registros' => $registros_turno,  // Registros de historial
                'registros_agrupados' => $registros_agrupados,
                'registros_eliminados' => $registros_eliminados,
            ];
        }


        // listar los registros eliminados
        $registros_eliminados = DB::table('delete_tarifas')
            ->join('tarifas', 'tarifas.id', '=', 'delete_tarifas.tarifa_id')
            ->select('precio', 'delete_tarifas.created_at')
            ->where('usuario_id', "=", $usuario_actual)
            ->whereDate('delete_tarifas.created_at', "=", $fecha_actual)
            ->get();

        

      

        $pdf = Pdf::loadView('administrador/pdf/reporteRegistroDiario', compact('registros_por_turno', 'nombreCompletoUsuario', 'registros_eliminados'));
        return $pdf->stream();
    }



    public function obtenerTurno($id_usuario, $fecha_actual)
    {

        return DB::table('historial_puesto')
            ->where('usuario_id', $id_usuario)
            ->whereDate('created_at', $fecha_actual)
            ->get();
    }



    public function generar_boleta(String $id)
    {
        try {
            $reporte = HistorialRegistros::find($id);

            if (!$reporte) {
                throw new Exception("Reporte no encontrado");
            }

            // Decodifica el campo JSON como objeto
            $reporteJson = json_decode($reporte->reporte_json);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("El campo reporte_json no contiene un JSON válido.");
            }

            // Acceso a datos del JSON como objeto
            $nuevoQr = $reporteJson->nuevo_Qr ?? null;
            $tarifa = $reporteJson->tarifa ?? null;
            $fechaActual = $reporteJson->fecha_actual ?? null;
            $puesto = $reporteJson->puesto ?? null;

            // Decodifica 'vehiculo' como objeto
            $vehiculo = $reporteJson->vehiculo ? json_decode(json_encode($reporteJson->vehiculo)) : null;



            // Genera el QR
            $qr_unico = $this->generar_qrReporte($nuevoQr);

            // Genera el reporte en PDF 
            $reporteBase64 = $this->generar_ReportePdf($tarifa, $qr_unico, $fechaActual, $puesto, $vehiculo);

            // Responde con éxito
            $this->mensaje('exito', $reporteBase64);
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            // Manejo de excepciones
            $this->mensaje("error", "Error: " . $e->getMessage());
            return response()->json($this->mensaje, 500);
        }
    }


    public function generar_varias_boletas(Request $request)
    {

        try {

            $validatedData = $request->validate([
                'id_precios' => 'required|exists:tarifas,id',
                'cantidad' => 'required|numeric|min:2|max:20',
            ]);

            $fecha_actual = $this->fecha->toDateString();
            $usuario_actual = auth()->user()->id;
            // return $request->all();

            // se obtiene datos del puesto
            $puesto = $this->obtenerPuesto($fecha_actual, $usuario_actual);


            if (!$puesto) {
                throw new Exception(' el usuario no tiene asignado un puesto');
            }

            $arrayBoletas = [];

            for ($i = 0; $i < $request->cantidad; $i++) {
                $resultado = $this->generar_boletas_masivamente($request->id_precios);

                if ($resultado['tipo'] === "exito") {
                    $arrayBoletas[$i] = [
                        $resultado['mensaje'],
                        $resultado['qrcod'],
                    ];
                   
                }
            }


        
               $this->mensaje("exito", $arrayBoletas);
               return response()->json($this->mensaje, 200);

        } catch (Exception $e) {
            DB::rollBack();
                  // Log de error solo si ocurre una excepción
                Log::error('Error al generar QR', [
                    'error_message' => $e->getMessage(),
                    'user_id' => auth()->user()->only(['nombres', 'apellidos']), // Usuario que causó el error
                    'tarifa_id' => $request->id_precios, // Tarifa asociada al error
                ]);
            $this->mensaje("error", "Error " . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }

    // registra y devuelve las boletas en base 64
    public function generar_boletas_masivamente($id_precio)
    {
        try {

            DB::beginTransaction();
            $fecha_actual = $this->fecha->toDateString();
            $usuario_actual = auth()->user()->id;
            // return $request->all();

            // se obtiene datos del puesto
            $puesto = $this->obtenerPuesto($fecha_actual, $usuario_actual);


            if (!$puesto) {
                throw new Exception(' el usuario no tiene asignado un puesto');
            }

            // Se obtiene datos de la tarifa
            $tarifa = Tarifa::find($id_precio);

            $registro = new Registro();
            $registro->puesto_id = $puesto->id;
            $registro->tarifa_id = $tarifa->id;
            $registro->usuario_id = $usuario_actual;

            $registro->save();

            $cod_qr_unico = $this->encrypt($registro->id);

            $nuevo_Qr = $this->generar_qrReporte($cod_qr_unico);

            // se obtiene el pdf en base 64
            $resultado = $this->generar_ReportePdf($tarifa, $nuevo_Qr, $fecha_actual, $puesto, null);

            // guardar el cod_qr generado
            $registro->codigo_qr = $cod_qr_unico;
            $registro->save();


            // Datos que necesito para generar el reporte
            $data = [
                'tarifa' => $tarifa,
                'nuevo_Qr' => $cod_qr_unico,
                'fecha_actual' => Carbon::now()->format('Y-m-d H:i:s'),
                'puesto' => $puesto,
                'vehiculo' => null,
            ];


            $this->guardarHistorialRegistro($cod_qr_unico, $puesto->nombre, $tarifa->precio, null, null, json_encode($data), $registro->id);

            DB::commit();
            // Retornar el PDF para su visualización
            return   [
                'tipo' => "exito",
                'mensaje' => $resultado,
                'qrcod'=>$cod_qr_unico,
            ];
        } catch (Exception $e) {
            DB::rollBack();
                  // Log de error solo si ocurre una excepción
                Log::error('Error al generar QR', [
                    'error_message' => $e->getMessage(),
                    'user_id' => auth()->user()->only(['nombres', 'apellidos']), // Usuario que causó el error
                    'tarifa_id' => $tarifa ? $tarifa->precio : 'No disponible', // Tarifa asociada al error
                ]);
            return   [
                'tipo' => "error",
                'mensaje' => $e->getMessage()
            ];
        }
    }

    public function mensaje($titulo, $mensaje)
    {

        $this->mensaje = [
            'tipo' => $titulo,
            'mensaje' => $mensaje
        ];
    }
}
