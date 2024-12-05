<?php

namespace App\Http\Controllers\Peaje;

use App\Http\Controllers\Controller;
use App\Http\Requests\peaje\RegistroPeajeRequest;
use App\Models\Color;
use App\Models\HistorialRegistros;
use App\Models\Persona;
use App\Models\Puesto;
use App\Models\Registro;
use App\Models\Tarifa;
use App\Models\TipoVehiculo;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\select;
use Barryvdh\DomPDF\Facade\Pdf; // Asegúrate de importar esta clase
use Exception;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Hashids\Hashids; // libreria para encriptar y descencriptar

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
    public function generar_qr(Request $request)
    {
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
            $resultado = $this->generar_ReportePdf($tarifa, $nuevo_Qr, $fecha_actual, $puesto);

            // guardar el cod_qr generado
            $registro->codigo_qr = $cod_qr_unico;
            $registro->save();


            // Datos que necesito para generar el reporte
            $data = [
                'tarifa' => $tarifa,
                'nuevo_Qr' => $cod_qr_unico,
                'fecha_actual' => $fecha_actual,
                'puesto' => $puesto,
            ];


            $this->guardarHistorialRegistro($cod_qr_unico, $puesto->nombre, $tarifa->precio, null, null, json_encode($data), $registro->id);

            DB::commit();
            // Retornar el PDF para su visualización
            $this->mensaje("exito", $resultado);

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->mensaje("error", "Error " . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }


    public function obtenerPuesto($fecha_actual, $usuario_actual)
    {
        return Puesto::select('id', 'nombre')
            ->whereHas('users', function ($query) use ($usuario_actual, $fecha_actual) {
                $query->where('historial_puesto.usuario_id', '=', $usuario_actual)
                    ->whereDate('historial_puesto.created_at', '=', $fecha_actual);
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

    public function generar_ReportePdf($tarifa, $qrCodeBase64, $fecha_actual, $puesto)
    {

        $data = [
            'tarifa' => $tarifa,
            'qrCodeBase64' => $qrCodeBase64,
            'fecha_actual' => $fecha_actual,
            'usuario' => auth()->user()->only(['nombres', 'apellidos']),
            'puesto' => $puesto,
        ];
        // Crear instancia del PDFhttpsÑ--www.example.com


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



    public function store(RegistroPeajeRequest $request)
    {
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
            $id_registro = $this->nuevoRegistro($puesto->id, $tarifa->id, $usuario_actual, $vehiculo->id);

            // GENERAMOS EL QR ENCRIPTADO Y EL REPORTE 


            // se encripta el el id del registro
            $cod_qr_unico = $this->encrypt($id_registro);

            // Se genera el qr apartir de la encriptacion del registro
            $nuevo_Qr = $this->generar_qrReporte($cod_qr_unico);

            // se obtiene el pdf en base 64
            $resultado = $this->generar_ReportePdf($tarifa, $nuevo_Qr, $fecha_actual, $puesto);

            // guardar el cod_qr generado en la base de datos

            $this->registrarQr($cod_qr_unico, $id_registro);

            $data = [
                'tarifa' => $tarifa,
                'nuevo_Qr' => $cod_qr_unico,
                'fecha_actual' => $fecha_actual,
                'puesto' => $puesto,
            ];

            $this->guardarHistorialRegistro($cod_qr_unico, $puesto->nombre, $tarifa->precio, $vehiculo->placa, $persona->ci,  json_encode($data), $id_registro);

            DB::commit();

            $this->mensaje('exito', $resultado);
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {

            DB::rollBack();
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
        ];
        return  $personaObject = json_decode(json_encode($vehiculo));
    }

    public function nuevoRegistro($puesto, $tarifa, $usuario_actual, $vehiculo)
    {


        $registro = new Registro();
        $registro->puesto_id = $puesto;
        $registro->tarifa_id = $tarifa;
        $registro->usuario_id  = $usuario_actual;
        $registro->vehiculo_id   = $vehiculo;

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

            $fecha_actual = $this->fecha->toDateString();
            DB::beginTransaction();

            $registro = Registro::select('codigo_qr', 'created_at')
                ->where('codigo_qr', $qr)
                ->first();


            if (!$registro) {
                throw new Exception('el codigo escaneado no  fue generado en ningun puesto de peaje');
            }

            $registro_fecha = Carbon::parse($registro->created_at->format('Y-m-d'));

            if ($registro_fecha->isBefore($fecha_actual)) {
                throw new Exception('el QR ya nos es valido...!! ' . $registro_fecha = Carbon::parse($registro->created_at->format('Y-m-d')));
            }




            DB::commit();

            $this->mensaje('exito', "El QR escaneado es valido");
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

        return view("administrador.control_peaje.listar_registro");
    }


    public function listar_registro(Request $request)
    {

        // Inicia la consulta con los usuarios y sus puestos
        $registroQuery = HistorialRegistros::select('puesto', 'nombre_usuario', 'precio', 'placa', 'ci', 'created_at')
            ->where('usuario_id', auth()->user()->id)
            ->orderBy('created_at', 'desc') // Ordena por created_at de manera descendente
            ->get();
            








        // Filtro de búsqueda: Filtra por los campos correctos en la tabla User
        if (!empty($request->search['value'])) {
            $registroQuery->where(function ($query) use ($request) {
                $query->where('puesto', 'like', '%' . $request->search['value'] . '%')
                    ->orWhere('nombre_usuario', 'like', '%' . $request->search['value'] . '%')
                    ->orWhere('precio', 'like', '%' . $request->search['value'] . '%')
                    ->orWhere('ci', 'like', '%' . $request->search['value'] . '%');
            });
        }


        // Total de registros antes del filtrado
        $recordsTotal = $registroQuery->count();

        // Total de registros filtrados
        $recordsFilter = $registroQuery->count();

        // Paginación y orden
        $datos_registros = $registroQuery
            ->skip($request->start)
            ->take($request->length); // Usamos take() para limitar la cantidad de registros por página

        // Permisos (para el frontend, si es necesario)
        $permissions = [
            'desactivar' => auth()->user()->can('admin.usuario.desactivar'),
            'reset' => auth()->user()->can('admin.usuario.reset'),
            'editarRol' => auth()->user()->can('admin.usuario.editarRol'),
            'editarTargeta' => auth()->user()->can('admin.usuario.editarTargeta'),
        ];

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFilter, // Ajustar si hay filtros
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
        //
    }


    public function mensaje($titulo, $mensaje)
    {

        $this->mensaje = [
            'tipo' => $titulo,
            'mensaje' => $mensaje
        ];
    }
}
