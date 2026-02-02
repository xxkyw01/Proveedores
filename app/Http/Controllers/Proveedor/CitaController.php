<?php

namespace App\Http\Controllers\Proveedor;

use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proveedor\Transporte;
use Illuminate\Support\Facades\Log;
use App\Models\Proveedor\Entidad;
use Illuminate\Support\Facades\DB;
use App\Models\Proveedor\ProveedorUsuario;
use Carbon\Carbon;

class CitaController extends Controller
{
    // PASO 1: Mostrar formulario de cita con ciudades y transportes
    public function index()
    {
        $ciudades = Entidad::select('id', 'nombre')->get();
        $transportes = Transporte::select('id', 'tipo')->get();
        $precioCaja = ProveedorUsuario::select('Cardcode', 'precio_por_caja')->get();

        return view('pages.proveedor.cita', compact('ciudades', 'transportes',  'precioCaja'));
    }

    // Obtener estado según la ciudad seleccionada
    public function obtenerEstado($id)
    {
        $entidad = Entidad::find($id);
        if (!$entidad) {
            return response()->json(['error' => 'Entidad no encontrada'], 404);
        }
        return response()->json(['estado' => $entidad->nombre]);
    }

    // PASO 2: Obtener horarios disponibles en la sucursal
    public function getDisponibilidad(Request $request)
    {
        $request->validate([
            'sucursal_id' => 'required|integer',
            'fecha' => 'required|date',
            'transporte_id' => 'required|integer',
            'anden_id' => 'nullable|integer'
        ]);

        try {
            $horarios = DB::connection('sqlsrv_proveedores')->select(
                "EXEC sp_horarios_disponibles_andenes @sucursal_id = ?, @fecha = ?, @transporte_id = ?, @anden_id = ?",
                [
                    $request->sucursal_id,
                    $request->fecha,
                    $request->transporte_id,
                    $request->anden_id
                ]
            );

            if (!empty($horarios) && isset($horarios[0]->tipo) && $horarios[0]->tipo === 'error') {
                return response()->json(['error' => $horarios[0]->mensaje], 200);
            }

            return response()->json($horarios);
        } catch (\Exception $e) {
            Log::error('Error en getDisponibilidad: ' . $e->getMessage());
            return response()->json(['error' => 'Error al consultar disponibilidad.'], 500);
        }
    }

    // Finalmente, se redirige al usuario a la vista de citas con un mensaje de éxito o error
    public function store(Request $request)
    {
        try {
            $vehiculos      = $request->input('vehiculos');
            $sucursalId     = $request->input('sucursal_id');
            $fecha          = $request->input('fecha');
            $idUser         = $request->input('idUser');
            $proveedorId    = $request->input('proveedor_id');
            $ordenesCompra  = $request->input('orden_compra');
            $foliosFactura  = $request->input('folios_factura');
            $tipoEvento     = $request->input('tipo_evento');

            // Validación 1: No permitir vehículos con mismo andén y hora
            $combinaciones = [];
            foreach ($vehiculos as $vehiculo) {
                $clave = $vehiculo['anden_id'] . '-' . $vehiculo['hora'];
                if (in_array($clave, $combinaciones)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Hay vehículos con el mismo andén y horario. Asigna diferentes horarios.'
                    ]);
                }
                $combinaciones[] = $clave;
            }

            // Validación 2: Revisar si ya existe una reservación en ese horario
            foreach ($vehiculos as $vehiculo) {
                $yaExiste = DB::connection('sqlsrv_proveedores')
                    ->table('reservaciones')
                    ->where('fecha', $fecha)
                    ->whereRaw("CAST(hora AS TIME) = ?", [$vehiculo['hora']])
                    ->where('sucursal_id', $sucursalId)
                    ->where('anden_id', $vehiculo['anden_id'])
                    ->where('estado', '!=', 'Cancelada')
                    ->exists();

                if ($yaExiste) {
                    return response()->json([
                        'success' => false,
                        'message' => "Ya hay una cita registrada a las {$vehiculo['hora']}. Usa otro horario."
                    ]);
                }
            }

            //Insertar cada vehículo como una reservación y guardar IDs
            $reservacionIds = [];

            foreach ($vehiculos as $vehiculo) {
                $resultado = DB::connection('sqlsrv_proveedores')->select(
                    'EXEC sp_insertar_reservacion 
                    @sucursal_id = ?, 
                    @transporte_id = ?, 
                    @fecha = ?, 
                    @hora = ?, 
                    @idUser = ?, 
                    @proveedor_id = ?, 
                    @descripcion = ?, 
                    @ordenesCompra = ?, 
                    @anden_id = ?,
                    @lleva_macheteros = ?,
                    @monto_maniobra = ?,
                    @tipo_evento = ?',
                    [
                        $sucursalId,
                        $vehiculo['transporte_id'],
                        $fecha,
                        $vehiculo['hora'],
                        $idUser,
                        $proveedorId,
                        $vehiculo['descripcion'],
                        json_encode($ordenesCompra),
                        $vehiculo['anden_id'],
                        $vehiculo['lleva_macheteros'],
                        $vehiculo['monto_maniobra'],
                        $tipoEvento
                    ]
                );

                $reservacionId = $resultado[0]->id ?? null;
                if ($reservacionId) {
                    $reservacionIds[] = $reservacionId;

                    //  Actualizar folios de factura si existen
                    foreach ($ordenesCompra as $orden) {
                        if (!empty($foliosFactura[$orden])) {
                            DB::connection('sqlsrv_proveedores')
                                ->table('reservacion_orden_compra')
                                ->where('reservacion_id', $reservacionId)
                                ->where('orden_compra', $orden)
                                ->update([
                                    'folio_factura' => $foliosFactura[$orden]
                                ]);
                        }
                    }
                }
            }

            //  Datos para correo
            $nombreSucursal = DB::connection('sqlsrv_proveedores')
                ->table('sucursales')
                ->where('id', $sucursalId)
                ->value('nombre');

            $datosProveedor = DB::connection('sqlsrv_proveedores')
                ->select("EXEC dbo.sp_listar_proveedores @codigoProveedor = ?", [$proveedorId]);

            $nombreproveedor = $datosProveedor[0]->Nombre_Proveedor ?? 'Proveedor';

            $horarios = [];
            foreach ($vehiculos as $vehiculo) {
                $hora = $vehiculo['hora'];
                $anden = DB::connection('sqlsrv_proveedores')
                    ->table('andenes')
                    ->where('id', $vehiculo['anden_id'])
                    ->value('nombre');
                $transporte = DB::connection('sqlsrv_proveedores')
                    ->table('transportes')
                    ->where('id', $vehiculo['transporte_id'])
                    ->value('nombre');
                $detalle = "{$anden} a las " . \Carbon\Carbon::parse($hora)->format('g:i A') . " ({$transporte})";
                if (!empty($vehiculo['descripcion'])) {
                    $detalle .= " / {$vehiculo['descripcion']} macheteros";
                }
                if (!empty($vehiculo['monto_maniobra'])) {
                    $monto = number_format($vehiculo['monto_maniobra'], 2);
                    $detalle .= " / El monto total es \${$monto}";
                }
                $horarios[] = ['detalle' => $detalle];
            }

            //  Enviar correo de confirmación
            Mail::to(['mesadecontrol@laconcha.com.mx', 'auxdesarrollador.it@laconcha.com.mx' , 'sistemas@laconcha.com.mx'])->send(
                new \App\Mail\CorreoCitaConfirmada($fecha, $nombreSucursal, $horarios, $nombreproveedor)
            );

            return response()->json([
                'success' => true,
                'message' => 'Cita registrada correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la cita',
                'error' => $e->getMessage()
            ]);
        }
    }

    // Obtener sucursales según la ciudad seleccionada
    public function obtenerSucursales($id)
    {
        $sucursales = DB::connection('sqlsrv_proveedores')
            ->table('sucursales')
            ->where('entidad_id', $id)
            ->select('id', 'nombre')
            ->get();

        return response()->json($sucursales);
    }

    //Para obtener la consulta de busque de proveedor de la vista proveedores/Cita del paso n°4 donde dice q ingrese el id de proveddor y me despliegua los datos
    public function obtenerDatosProveedor($codigo)
    {
        try {
            $esProveedor = session()->has('Proveedor') ? 1 : 0;

            if ($esProveedor) {
                $codigo = session('Proveedor.CardCode');
            }

            $proveedor = DB::connection('sqlsrv_proveedores')->select(
                "EXEC dbo.sp_listar_proveedores @codigoProveedor = ?, @esProveedor = ?",
                [$codigo, $esProveedor]
            );

            if (empty($proveedor)) {
                return response()->json(['error' => 'Proveedor no encontrado'], 404);
            }

            return response()->json([
                'Nombre_Proveedor'    => $proveedor[0]->Nombre_Proveedor ?? 'No disponible',
                'Celular'             => $proveedor[0]->Celular ?? 'No registrado',
                'Correo_Electronico'  => $proveedor[0]->Correo_Electronico ?? 'No registrado',
                'Nombre_Contacto'     => $proveedor[0]->Nombre_Contacto ?? 'No disponible',
                'Celular_Contacto'    => $proveedor[0]->Celular_Contacto ?? 'No registrado',
                'Correo_Contacto'     => $proveedor[0]->Correo_Contacto ?? 'No registrado',
                'Usuario_Intranet'    => $proveedor[0]->Usuario_Intranet ?? null,
                'Password_Intranet'   => $proveedor[0]->Password_Intranet ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error en obtenerDatosProveedor: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    //Para obtener los articulos x cada orden de compra cunado este abierto  o pendinetes 
    public function obtenerArticulosPendientes(Request $request)
    {
        $numeroOrden = $request->numeroOrden;
        $articulos = DB::select("EXEC IntranetProveedores.dbo.sp_Consultar_ArticulosPendientes ?", [$numeroOrden]);
        return response()->json($articulos);
    }

    public function almacenarCita(Request $request)
    {
        $request->validate([
            'idUser' => 'required',
            'ordenCompra' => 'required|array',
            'ordenCompra.*' => 'integer',
        ]);

        $proveedor = session()->has('Proveedor') ? session('Proveedor.CardCode') : $request->idUser;
        //$proveedor = $request->idUser;
        $ordenesSeleccionadas = $request->ordenCompra;

        foreach ($ordenesSeleccionadas as $orden) {
            DB::table('citas_proveedores')->insert([
                'proveedor_id' => $proveedor,
                'orden_compra' => $orden,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Cita registrada con éxito. Solamente  espera que la recepcion te la confirme.');
    }

    // Para obtener andenes según la sucursal seleccionada
    public function obtenerAndenes($sucursalId)
    {
        $andenes = DB::connection('sqlsrv_proveedores')
            ->table('andenes')
            ->where('sucursal_id', $sucursalId)
            ->select('id', 'nombre')
            ->get();
        return response()->json($andenes);
    }

    // Carga la serie de orden de compra dependiento de la sucursal seleccionada
    public function obtenerOrdenesCompra(Request $request)
    {
        //$codigoProveedor = $request->codigoProveedor;
        $codigoProveedor = session()->has('Proveedor') ? session('Proveedor.CardCode') : $request->codigoProveedor;

        $entidadId = $request->entidad_id;

        $serieOC = $this->obtenerSeriePorSucursal($entidadId);

        if (!$serieOC) {
            return response()->json(['error' => 'No se pudo determinar la serie para esta sucursal.'], 400);
        }

        try {
            $ordenes = DB::connection('sqlsrv_proveedores')->select("EXEC dbo.sp_Consultar_OrdenesCompraAbiertas ?, ?", [
                $codigoProveedor,
                $serieOC
            ]);

            Log::info("Código proveedor: {$codigoProveedor}, Entidad ID: {$entidadId}, Serie: {$serieOC}");

            return response()->json($ordenes);
        } catch (\Exception $e) {
            Log::error('Error al obtener órdenes de compra: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo obtener las órdenes de compra.'], 500);
        }
    }

    // Este método es para mostrar en la consola el nombre de la serie según la sucursal seleccionada
    public function obtenerSerieOC($entidad_id)
    {
        $series = [
            1 => 'ZO', // Guadalajara
            4 => 'ZC'  // Guanajuato
        ];

        return response()->json(['serie_oc' => $series[$entidad_id] ?? null]);
    }

    // Método interno para usar la lógica de la serie dentro del controlador
    private function obtenerSeriePorSucursal($sucursal_id)
    {
        $series = [
            1 => 'ZO', // Guadalajara
            4 => 'ZC', // Guanajuato

        ];

        return $series[$sucursal_id] ?? null;
    }

    public function enviarSolicitudCorreo(Request $request)
    {
        try {
            // $codigo = $request->input('codigoProveedor');
            if (session()->has('Proveedor')) {
                $codigo = session('Proveedor.CardCode');
            }
            $campos = $request->input('campos');

            $datos = DB::connection('sqlsrv_proveedores')->select("EXEC dbo.sp_listar_proveedores ?", [$codigo]);

            if (empty($datos)) {
                return response()->json(['success' => false, 'message' => 'Proveedor no encontrado']);
            }

            $proveedor = $datos[0];

            // Enviar siempre a este correo
            Mail::to('facturas.compras@laconcha.com.mx', 'auxdesarrollador.it@laconcha.com.mx')->send(
                new \App\Mail\SolicitudActualizacionProveedor($proveedor, $campos)
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // Log para depuración
            Log::error('Error al enviar solicitud de actualización: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Obteer el precio x cada 
    public function obtenerPrecioPorCaja($codigoProveedor)
    {
        $precio = ProveedorUsuario::where('CardCode', $codigoProveedor)->value('precio_por_caja');

        // Si no existe, retornar 2.50 como valor por defecto
        return response()->json([
            'precio' => $precio ?? 2.50
        ]);
    }

}