<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Mail\CitaConfirmada;
use Illuminate\Support\Facades\Mail;
use App\Models\almacen\Sucursal;
use Carbon\Carbon;


class ConfirmarCitaController extends Controller
{
    public function index()
    {
        $rolId = session('Usuario.IdRol');
        $sucursalUsuario = session('Usuario.SucursalID');
        if (!in_array($rolId, [1, 3, 4, 5])) {
            $sucursal_id = $sucursalUsuario;
        }
        //Log::info("Sucursal por sesión: " . $sucursalUsuario);
        //Log::info("Sucursal usada: " . ($sucursal_id ?? 'Todas'));

        $sucursales = Sucursal::all();

        return view('pages.almacen.confirmarCita', [
            'sucursal_id' => $sucursal_id ?? null,
            'sucursales' => $sucursales
        ]);
    }

    public function obtenerPendientes()
    {
        $rolId = session('Usuario.IdRol');
        $sucursalUsuario = session('Usuario.SucursalID');
        if (in_array($rolId, [1, 3, 5])) {
            $data = DB::connection('sqlsrv_proveedores')->select('EXEC [dbo].[sp_consultar_reservaciones_pendientes]');
        } elseif ($rolId == 2 && $sucursalUsuario) {
            $data = DB::connection('sqlsrv_proveedores')->select('EXEC [dbo].[sp_consultar_reservaciones_pendientes] ?', [$sucursalUsuario]);
        } else {
            return response()->json([]);
        }
        return DataTables::of($data)->toJson();
    }

    public function obtenerAyer()
    {
        $rolId = session('Usuario.IdRol');
        $sucursalUsuario = session('Usuario.SucursalID');
        if (in_array($rolId, [1, 3, 5])) {
            $data = DB::connection('sqlsrv_proveedores')->select('EXEC [dbo].[sp_consultar_citas_ayer]');
        } elseif ($rolId == 2 && $sucursalUsuario) {
            $data = DB::connection('sqlsrv_proveedores')->select('EXEC [dbo].[sp_consultar_citas_ayer] ?', [$sucursalUsuario]);
        } else {
            return response()->json([]);
        }
        return DataTables::of($data)->toJson();
    }

    public function obtenerSemana()
    {
        $rolId = session('Usuario.IdRol');
        $sucursalUsuario = session('Usuario.SucursalID');
        if (in_array($rolId, [1, 3, 5])) {
            $data = DB::connection('sqlsrv_proveedores')->select('EXEC [dbo].[sp_consultar_citas_semana_pasada]');
        } elseif ($rolId == 2 && $sucursalUsuario) {
            $data = DB::connection('sqlsrv_proveedores')->select('EXEC [dbo].[sp_consultar_citas_semana_pasada] ?', [$sucursalUsuario]);
        } else {
            return response()->json([]);
        }
        return DataTables::of($data)->toJson();
    }


    public function obtenerMes()
    {
        $rolId = session('Usuario.IdRol');
        $sucursalUsuario = session('Usuario.SucursalID');
        if (in_array($rolId, [1, 3, 5])) {
            $data = DB::connection('sqlsrv_proveedores')->select('EXEC [dbo].[sp_consultar_citas_mes_pasado]');
        } elseif ($rolId == 2 && $sucursalUsuario) {
            $data = DB::connection('sqlsrv_proveedores')->select('EXEC [dbo].[sp_consultar_citas_mes_pasado] ?', [$sucursalUsuario]);
        } else {
            return response()->json([]);
        }
        return DataTables::of($data)->toJson();
    }

    public function detalleCita($id)
    {
        $data = DB::connection('sqlsrv_proveedores')
            ->select('EXEC [dbo].[sp_consultar_reservacion_por_id] ?', [$id]);
        if (count($data) === 0) {
            return response()->json(['error' => 'No se encontró la cita.'], 404);
        }

        $info = [
            'id' => $data[0]->id,
            'fecha' => $data[0]->fecha,
            'hora' => $data[0]->hora,
            'estado' => $data[0]->estado,
            'sucursal_nombre' => $data[0]->sucursal_nombre,
            'proveedor_nombre' => $data[0]->proveedor_nombre,
            'RFC_proveedor' => $data[0]->RFC_proveedor,
            'Telefono' => $data[0]->Telefono,
            'Celular' => $data[0]->Celular,
            'Correo' => $data[0]->Correo,
            'Direccion' => $data[0]->Direccion,
            'Contacto_Referencia' => $data[0]->Contacto_Referencia,
            'Telefono_contacto' => $data[0]->Telefono_contacto,
            'Celular_contacto' => $data[0]->Celular_contacto,
            'Correo_contacto' => $data[0]->Correo_contacto,
            'orden_compra' => $data[0]->orden_compra,
            'Lugar' => $data[0]->anden
        ];

        $vehiculos = array_map(function ($row) {
            return [
                'hora' => $row->hora,
                'anden' => $row->anden,
                'transporte' => $row->tipo_transporte ?? $row->transporte_nombre,
                'lleva_macheteros' => $row->cantidad_macheteros ? 1 : 0,
                'descripcion' => $row->cantidad_macheteros,
                'monto_maniobra' => $row->monto_maniobra
            ];
        }, $data);

        return response()->json([
            'info' => $info,
            'vehiculos' => $vehiculos
        ]);
    }

    public function actualizarEstado(Request $request)
    {
        $id = $request->input('id');
        $estadoNuevo = $request->input('estado');
        $comentario = $request->input('comentario');
        $usuarioInterno = auth()->user()->name ?? 'Sistema';
        $motivoCancelacion = $comentario ?: 'Sin motivo especificado';

        try {
            DB::connection('sqlsrv_proveedores')->statement('EXEC sp_actualizar_estado_reservacion ?, ?, ?', [
                $id,
                $estadoNuevo,
                $comentario
            ]);
            DB::connection('sqlsrv_proveedores')->table('reservaciones')
                ->where('id', $id)
                ->update(['comentario_usuario' => $comentario]);
            $datos = DB::connection('sqlsrv_proveedores')->select('EXEC sp_consultar_reservacion_por_id ?', [$id]);
            $cita = $datos[0] ?? null;

            if (!$cita) {
                return response()->json(['success' => false, 'message' => 'No se encontraron datos de la reservación para el ID proporcionado.']);
            }

            $saludo = $this->generarSaludo();
            $fechaFormateada = Carbon::parse($cita->fecha)->locale('es')->translatedFormat('l d \d\e F \d\e Y');
            $correoProveedor = $cita->Correo ?? null;
            $correoContacto = $cita->Correo_contacto ?? null;
            $destinatario = $correoProveedor ?: $correoContacto;
            $copiasInternas = ['mesadecontrol@laconcha.com.mx', 'mesadecontrol.centro@laconcha.com.mx','auxdesarrollador.it@laconcha.com.mx', 'facturas.compras@laconcha.com.mx', 'sistemas@laconcha.com.mx'];

            if ($destinatario && filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
                if ($estadoNuevo === 'Confirmada') {
                    Mail::to($destinatario)
                        ->bcc($copiasInternas)
                        ->send(new \App\Mail\CitaConfirmada(
                            $saludo,
                            $cita->proveedor_nombre,
                            $cita->id,
                            $fechaFormateada,
                            $cita->sucursal_nombre,
                            $cita->anden,
                            $cita->hora,
                            $cita->transporte_nombre,
                            $comentario
                        ));
                } elseif ($estadoNuevo === 'Cancelada') {
                    Mail::to($destinatario)
                        ->bcc($copiasInternas)
                        ->send(new \App\Mail\CitaCancelada(
                            $saludo,
                            $cita->proveedor_nombre,
                            $cita->id,
                            $fechaFormateada,
                            $cita->sucursal_nombre,
                            $cita->anden,
                            $cita->hora,
                            $cita->transporte_nombre,
                            $comentario
                        ));

                    $this->moverReservacionCancelada($id, $motivoCancelacion, $usuarioInterno);
                }
            } else {
                Log::warning("No se pudo enviar correo de cita (ID {$id}): correo no válido. Proveedor: {$correoProveedor}, Contacto: {$correoContacto}");
            }

            return response()->json(['success' => true, 'message' => 'Cita actualizada correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar la cita: ' . $e->getMessage()]);
        }
    }

    private function generarSaludo()
    {
        $hora = now()->format('H');
        if ($hora < 12) return 'Buenos días';
        if ($hora < 19) return 'Buenas tardes';
        return 'Buenas noches';
    }

    private function moverReservacionCancelada($id, $motivo, $canceladoPor)
    {
        $cita = DB::connection('sqlsrv_proveedores')
            ->table('reservaciones')
            ->where('id', $id)
            ->first();
        if (!$cita) return;

        DB::connection('sqlsrv_proveedores')->table('reservacion_cancelada')->insert([
            'reservacion_id' => $cita->id,
            'sucursal_id' => $cita->sucursal_id,
            'transporte_id' => $cita->transporte_id,
            'fecha' => $cita->fecha,
            'hora' => $cita->hora,
            'proveedor_id' => $cita->proveedor_id,
            'idUser' => $cita->idUser,
            'orden_compra' => $cita->orden_compra,
            'anden_id' => $cita->anden_id,
            'descripcion' => $cita->descripcion,
            'comentario_usuario' => $cita->comentario_usuario,
            'estado_cancelado' => now(),
            'motivo_cancelacion' => $motivo,
            'cancelado_por' => $canceladoPor,
            'created_at' => now()
        ]);

        DB::connection('sqlsrv_proveedores')->table('reservacion_orden_compra')
            ->where('reservacion_id', $id)
            ->delete();

        DB::connection('sqlsrv_proveedores')
            ->table('reservaciones')
            ->where('id', $id)
            ->delete();
    }
}
