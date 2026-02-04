<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Models\Proveedor\Reservacion;
use Illuminate\Http\Request;
use App\Models\Proveedor\Transporte;
use Illuminate\Support\Facades\Log;
use App\Models\Proveedor\Entidad;
use Illuminate\Support\Facades\DB;
use App\Models\Proveedor\ProveedorUsuario;
use Carbon\Carbon;
use App\Mail\CitaNoProgramadaMail;


class CitaNoProgramadaController extends Controller
{
    public function index()
    {
        $ciudades = Entidad::select('id', 'nombre')->get();
        $transportes = Transporte::select('id', 'tipo')->get();

        return view('pages.almacen.CitaNoProgramado', compact('ciudades', 'transportes'));
    }

    public function obtenerEstado($id)
    {
        $entidad = Entidad::find($id);
        if (!$entidad) {
            return response()->json(['error' => 'Entidad no encontrada'], 404);
        }
        return response()->json(['estado' => $entidad->nombre]);
    }

    public function obtenerAndenes($sucursalId)
    {
        $andenes = DB::connection('sqlsrv_proveedores')
            ->table('andenes')
            ->where('sucursal_id', $sucursalId)
            ->select('id', 'nombre')
            ->get();
        return response()->json($andenes);
    }

    public function obtenerOrdenesCompra(Request $request)
    {
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

    public function obtenerSerieOC($entidad_id)
    {
        $series = [
            1 => 'ZO',
            4 => 'ZC'  
        ];

        return response()->json(['serie_oc' => $series[$entidad_id] ?? null]);
    }

    private function obtenerSeriePorSucursal($sucursal_id)
    {
        $series = [
            1 => 'ZO',
            4 => 'ZC', 
        ];
        return $series[$sucursal_id] ?? null;
    }

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

    public function obtenerSucursales($id)
    {
        $sucursales = DB::connection('sqlsrv_proveedores')
            ->table('sucursales')
            ->where('entidad_id', $id)
            ->select('id', 'nombre')
            ->get();

        return response()->json($sucursales);
    }

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
                'Nombre_Proveedor'    => $proveedor[0]->Nombre_Proveedor ?? 'No disponible en el SAP',
            ]);
        } catch (\Exception $e) {
            Log::error('Error en obtenerDatosProveedor: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'motivo_evento' => 'required|string',
            'evidencias.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $motivo = $request->input('motivo_evento');
        $rutasEvidencias = [];

        if ($request->hasFile('evidencias')) {
            foreach ($request->file('evidencias') as $archivo) {
                $ruta = $archivo->store('evidencias', 'public');
                $rutasEvidencias[] = $ruta;
            }
        }

        try {
            $vehiculos = $request->input('vehiculos', []);
            if (!is_array($vehiculos) || count($vehiculos) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se recibieron vehículos válidos.'
                ]);
            }

            $sucursalId = $request->input('sucursal_id');
            $fecha = $request->input('fecha');
            $idUser  = $request->input('idUser'); 
            $proveedorId = $request->input('proveedor_id');
            $tipoEvento = 'No Programada';

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

            foreach ($vehiculos as $vehiculo) {
                $yaExiste = Reservacion::where('fecha', $fecha)
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

            foreach ($vehiculos as $vehiculo) {
                $reservacion = new Reservacion();
                $reservacion->sucursal_id = $sucursalId;
                $reservacion->transporte_id = $vehiculo['transporte_id'];
                $reservacion->fecha = $fecha;
                $reservacion->hora = $vehiculo['hora'];
                $reservacion->idUser  = $idUser;
                $reservacion->proveedor_id = $proveedorId;
                $reservacion->descripcion = $vehiculo['descripcion'];
                $reservacion->motivo_evento_no_programado = $motivo;
                $reservacion->evidencias = json_encode($rutasEvidencias);
                $reservacion->anden_id = $vehiculo['anden_id'];
                $reservacion->estado = 'Confirmada';
                $reservacion->tipo_evento = 'No Programada';
                $reservacion->save();
            }

        $evidenciasURL = collect($rutasEvidencias)
            ->map(fn($r) => asset('storage/' . $r))
            ->toArray();

        Mail::to('facturas.compras@laconcha.com.mx')
            ->bcc(['sistemas@laconcha.com.mx', 'auxdesarrollador.it@laconcha.com.mx'])
            ->send(new CitaNoProgramadaMail(
                $proveedorId,
                $sucursalId,
                $fecha,
                $motivo,
                $vehiculos,
                $evidenciasURL
            ));

            return response()->json([
                'success' => true,
                'message' => 'Cita no programada registrada correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la cita',
                'error' => $e->getMessage()
            ]);
        }
    }
}
