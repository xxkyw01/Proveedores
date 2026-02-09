<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proveedor\Reservacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use App\Models\Proveedor\ReservacionOrdenCompra;
use App\Models\almacen\Sucursal;
use App\Models\Proveedor\Transporte;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;


class ReservacionController extends Controller
{
    public function index(Request $request)
    {
        $sucursal_id = $request->input('sucursal_id');
        $fecha = $request->input('fecha');
        $idUser = auth()->user()->id ?? null;

        $reservaciones = Reservacion::obtenerReservaciones($sucursal_id, $fecha, $idUser);

        return view('pages.proveedor.historial', compact('reservaciones'));
    }


    public function generarPDF(Request $request)
    {
        $fechaInicio = $request->input('fechaInicio');
        $fechaFin = $request->input('fechaFin');
        $sucursal = $request->input('sucursal');

        $reservaciones = DB::connection('sqlsrv_proveedores')->select(
            'EXEC [dbo].[sp_consultar_reservaciones_todas] @fechaInicio = ?, @CardCode = NULL',
            [$fechaInicio]
        );
        $reservaciones = collect($reservaciones)->filter(function ($r) use ($fechaInicio, $fechaFin, $sucursal) {
            $fecha = \Carbon\Carbon::parse($r->fecha);
            return $fecha->between($fechaInicio, $fechaFin) &&
                ($sucursal == '' || $r->sucursal_nombre == $sucursal);
        });
    }

    public function solicitarCancelacion(Request $request)
    {
        $request->validate([
            'cita_id' => 'required|integer',
            'motivo' => 'required|string|max:1000',
        ]);

        $citaId = $request->cita_id;
        $motivo = $request->motivo;
        $detallesCita = DB::connection('sqlsrv_proveedores')
            ->selectOne('EXEC [dbo].[sp_consultar_reservaciones_todas] @Id = ?', [$citaId]);

        if (!$detallesCita) {
            return response()->json(['success' => false, 'message' => 'No se encontró la cita.'], 404);
        }

        $proveedorCodigo = session('Proveedor.CardCode');
        $proveedor = DB::connection('sqlsrv_proveedores')
            ->selectOne('EXEC [dbo].[sp_listar_proveedores] @codigoProveedor = ?', [$proveedorCodigo]);

        $fechaSolicitud = Carbon::now()->format('d/m/Y H:i');
        $data = [
            'id' => $citaId,
            'motivo' => $motivo,
            'sucursalNombre'  => $detallesCita->sucursal_nombre ?? 'No especificada',
            'proveedor' => $proveedor ? $proveedor->Nombre_Proveedor . " ({$proveedorCodigo})" : 'Usuario interno (' . $proveedorCodigo . ')',
            'fechaSolicitud' => $fechaSolicitud,
            'fechaCita' => $detallesCita->fecha,
            'horaCita' => $detallesCita->hora,
            'transporte' => $detallesCita->transporte_nombre,
            'anden' => $detallesCita->Lugar
        ];

        Mail::send('emails.solicitud_cancelacion', ['data' => $data], function ($message) {
            $message->to(['mesadecontrol@laconcha.com.mx', 'mesadecontrol.centro@laconcha.com.mx'])
                ->bcc('auxdesarrollador.it@laconcha.com.mx', 'sistemas@laconcha.com')
                ->subject('Solicitud de Cancelación de Cita');
        });

        return response()->json(['success' => true]);
    }

    private function columnasDisponibles(string $tabla, array $candidatas): array
    {
        $enBD = DB::connection('sqlsrv_proveedores')
            ->table('INFORMATION_SCHEMA.COLUMNS')
            ->where('TABLE_SCHEMA', 'dbo')
            ->where('TABLE_NAME', $tabla)
            ->pluck('COLUMN_NAME')
            ->map(fn($v) => strtolower($v))
            ->toArray();

        return array_values(array_filter($candidatas, function ($c) use ($enBD) {
            return in_array(strtolower($c), $enBD, true);
        }));
    }

    private function adjuntarMetaAReserva(object $row, array $metaActivas, array $metaCanceladas): object
    {
        $id = (int)($row->id ?? 0);
        if (isset($metaActivas[$id])) {
            foreach ($metaActivas[$id] as $k => $v) $row->$k = $v;
            return $row;
        }
        if (isset($metaCanceladas[$id])) {
            foreach ($metaCanceladas[$id] as $k => $v) $row->$k = $v;
            return $row;
        }
        foreach (['commit_afterrecep', 'evidencia_path', 'evidencia_nombre', 'evidencia_mime', 'evidencia_size'] as $k) {
            if (!property_exists($row, $k)) $row->$k = null;
        }
        return $row;
    }

    public function historial(Request $request)
    {
        $fecha    = $request->mes ? $request->mes . '-01' : null;
        $cardCode = session('Proveedor.CardCode') ?? null;

        if ($cardCode) {
            $reservaciones = DB::connection('sqlsrv_proveedores')
                ->select('EXEC [dbo].[sp_consultar_reservaciones_todas] @fechaInicio = ?, @CardCode = ?', [$fecha, $cardCode]);
        } else {
            $reservaciones = DB::connection('sqlsrv_proveedores')
                ->select('EXEC [dbo].[sp_consultar_reservaciones_todas] @fechaInicio = ?', [$fecha]);
        }

        $ids = collect($reservaciones)->pluck('id')->filter()->values()->all();
        $colsCandidatas = ['commit_afterrecep', 'evidencia_path', 'evidencia_nombre', 'evidencia_mime', 'evidencia_size'];
        $colsActivas    = $this->columnasDisponibles('reservaciones', $colsCandidatas);
        $colsCancel     = $this->columnasDisponibles('reservacion_cancelada', array_merge(['reservacion_id'], $colsCandidatas));

        $metaActivas = [];
        if (!empty($ids) && !empty($colsActivas)) {
            $metaActivas = DB::connection('sqlsrv_proveedores')
                ->table('reservaciones')
                ->whereIn('id', $ids)
                ->select(array_merge(['id'], $colsActivas))
                ->get()
                ->keyBy('id')
                ->map(function ($r) {
                    return [
                        'commit_afterrecep' => $r->commit_afterrecep ?? null,
                        'evidencia_path'    => $r->evidencia_path    ?? null,
                        'evidencia_nombre'  => $r->evidencia_nombre  ?? null,
                        'evidencia_mime'    => $r->evidencia_mime    ?? null,
                        'evidencia_size'    => $r->evidencia_size    ?? null,
                    ];
                })
                ->toArray();
        }

        $metaCanceladas = [];
        if (!empty($ids) && !empty($colsCancel)) {
            $metaCanceladas = DB::connection('sqlsrv_proveedores')
                ->table('reservacion_cancelada')
                ->whereIn('reservacion_id', $ids)
                ->select($colsCancel)
                ->get()
                ->keyBy('reservacion_id')
                ->map(function ($r) {
                    return [
                        'commit_afterrecep' => $r->commit_afterrecep ?? null,
                        'evidencia_path'    => $r->evidencia_path    ?? null,
                        'evidencia_nombre'  => $r->evidencia_nombre  ?? null,
                        'evidencia_mime'    => $r->evidencia_mime    ?? null,
                        'evidencia_size'    => $r->evidencia_size    ?? null,
                    ];
                })
                ->toArray();
        }

        $reservaciones = collect($reservaciones)
            ->map(function ($r) use ($metaActivas, $metaCanceladas) {
                return $this->adjuntarMetaAReserva($r, $metaActivas, $metaCanceladas);
            });

        $sucursales = Sucursal::all();
        $totalCitas = $reservaciones->count();
        $citasPendientes = $reservaciones->where('estado', 'Pendiente')->count();
        $citasConfirmadas = $reservaciones->where('estado', 'Confirmada')->count();
        $citasCanceladas  = $reservaciones->whereIn('estado', ['Cancelada', 'Cancelado'])->count();
        $citasAsistio     = $reservaciones->where('estado', 'Asistió')->count();
        $citasNoAsistio   = $reservaciones->where('estado', 'No asistió')->count();
        $citasEnProceso   = $reservaciones->where('estado', 'En proceso')->count();
        $citasRecepcionTardia = $reservaciones->where('estado', 'Recepción tardía')->count();
        $citasCanceladasPorProveedor = $reservaciones->where('estado', 'Cancelada por proveedor')->count();
        $citasNoProgramado = $reservaciones->filter(fn($i) => strtolower(trim($i->estado)) === 'no programado')->count();

        return view('pages.proveedor.historial', compact(
            'totalCitas',
            'citasPendientes',
            'citasConfirmadas',
            'citasCanceladas',
            'citasAsistio',
            'citasNoAsistio',
            'citasEnProceso',
            'citasRecepcionTardia',
            'citasCanceladasPorProveedor',
            'citasNoProgramado',
            'reservaciones',
            'sucursales'
        ));
    }

    public function verEvidencia($id)
    {
        $row = DB::connection('sqlsrv_proveedores')
            ->table('reservaciones')
            ->select('evidencia_path', 'evidencia_nombre', 'evidencia_mime')
            ->where('id', $id)
            ->first();

        if (!$row || !$row->evidencia_path) {
            abort(404, 'Evidencia no encontrada');
        }

        $path = ltrim($row->evidencia_path, '/'); 
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Archivo no existe en almacenamiento');
        }

        return Storage::disk('public')->response(
            $path,
            $row->evidencia_nombre ?? basename($path),
            ['Content-Type' => $row->evidencia_mime ?? 'application/octet-stream']
        );
    }
}
