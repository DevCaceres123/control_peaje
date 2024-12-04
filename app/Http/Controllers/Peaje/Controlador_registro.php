<?php

namespace App\Http\Controllers\Peaje;

use App\Http\Controllers\Controller;
use App\Http\Requests\peaje\RegistroPeajeRequest;
use App\Models\Color;
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
            $id_persona = $this->guardarPersona($request->ci, $request->nombres, $request->ap_materno, $request->ap_paterno);
            // se genran los datos del vehiculo
            $id_vehiculo = $this->guardarVehiculo($request->placa, $id_persona, $request->id_color, $request->id_tipo_veh);


            //  SE REGISTRA LOS DATOS EN LA BASE DE DATOS SI TODO ESTA CORRECTO
            $id_registro = $this->nuevoRegistro($puesto->id, $tarifa->id, $usuario_actual, $id_vehiculo);

            // GENERAMOS EL QR ENCRIPTADO Y EL REPORTE 


            // se encripta el el id del registro
            $cod_qr_unico = $this->encrypt($id_registro);

            // Se genera el qr apartir de la encriptacion del registro
            $nuevo_Qr = $this->generar_qrReporte($cod_qr_unico);

            // se obtiene el pdf en base 64
            $resultado = $this->generar_ReportePdf($tarifa, $nuevo_Qr, $fecha_actual, $puesto);

            // guardar el cod_qr generado en la base de datos

           $this->registrarQr($cod_qr_unico,$id_registro);

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
        } else {
            return null;
        }

        // Si encentra un ci registrado obtiene su id caso contrario se le asignara el ci registrado
        $datos_ci = Persona::select('id')->where('ci', $ci)->first();

        // Si encentra un ci ya regitrado con ese dato entonces no es necesario crear una nueva persona
        if ($datos_ci) {
            return $datos_ci->id;
        }

        $datos_ci = $ci;

        $persona = new Persona();
        $persona->ci = $datos_ci;
        $persona->nombres = $nombre;
        $persona->ap_paterno = $paterno;
        $persona->ap_materno = $materno;

        $persona->save();

        return $persona->id;
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
        } else {
            return null;
        }

        $vehiculo_nuevo = new Vehiculo();
        $vehiculo_nuevo->placa = $placa;
        $vehiculo_nuevo->persona_id = $persona;
        $vehiculo_nuevo->color_id = $color;
        $vehiculo_nuevo->tipovehiculo_id  = $vehiculo;
        $vehiculo_nuevo->save();

        return $vehiculo_nuevo->id;
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

    public function registrarQr($qr, $id_registro){

        $registro=Registro::find($id_registro);

        $registro->codigo_qr=$qr;

        $registro->save();

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
