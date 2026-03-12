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
use App\Http\Controllers\Almacen\GestionSupplierController;
use App\Services\SAPServiceLayer;


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
        foreach (['commit_afterrecep', 'evidencia_path', 'evidencia_nombre', 'evidencia_mime', 'evidencia_size', 'tipo_evento'] as $k) {
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

        // Extraer ids robustamente (el SP puede devolver 'id', 'Id' o 'ID')
        $ids = collect($reservaciones)->map(function ($r) {
            if (is_array($r)) $r = (object)$r;
            return $r->id ?? $r->Id ?? $r->ID ?? null;
        })->filter()->map(fn($v) => (int) $v)->unique()->values()->all();
        $colsCandidatas = ['commit_afterrecep', 'evidencia_path', 'evidencia_nombre', 'evidencia_mime', 'evidencia_size'];
        $colsActivas    = $this->columnasDisponibles('reservaciones', $colsCandidatas);
        $colsCancel     = $this->columnasDisponibles('reservacion_cancelada', array_merge(['reservacion_id'], $colsCandidatas));

        // Columnas relacionadas a estados/fechas que pueden existir en la tabla reservaciones
        $colsEstadoCandidatas = [
            'created_at', 'updated_at',
            'estado_completado','estado_cancelado','estado_asistio','estado_no_asistio',
            'estado_confirmado','estado_proceso','estado_tardia','estado_timeout','estado_cancelada_sp',
            'tipo_evento'
        ];
        $colsEstados = $this->columnasDisponibles('reservaciones', $colsEstadoCandidatas);

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

        $metaEstados = [];
        if (!empty($ids) && !empty($colsEstados)) {
            $metaEstados = DB::connection('sqlsrv_proveedores')
                ->table('reservaciones')
                ->whereIn('id', $ids)
                ->select(array_merge(['id'], $colsEstados))
                ->get()
                ->keyBy('id')
                ->map(function ($r) use ($colsEstados) {
                    $out = [];
                    foreach ($colsEstados as $c) {
                        if (property_exists($r, $c)) $out[$c] = $r->$c;
                    }
                    return $out;
                })
                ->toArray();
        }

        // Merge metaEstados into metaActivas so adjuntarMetaAReserva los incluya
        if (!empty($metaEstados)) {
            foreach ($metaEstados as $id => $vals) {
                if (isset($metaActivas[$id])) {
                    $metaActivas[$id] = array_merge($metaActivas[$id], $vals);
                } else {
                    $metaActivas[$id] = $vals;
                }
            }
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
        $citasRecepcionTardia = $reservaciones->whereIn('estado', ['Asistio Fuera de Horario'])->count();
        $citasCanceladasPorProveedor = $reservaciones->where('estado', 'Cancelada por proveedor')->count();
        $citasNoProgramado = $reservaciones->filter(fn($i) => strtolower(trim($i->estado)) === 'no programado')->count();

        // Contadores por tipo_evento (normalizando texto)
        $normalize = fn($v) => strtolower(trim((string)($v ?? '')));
        $eventoProgramada = $reservaciones->filter(fn($r) => $normalize($r->tipo_evento) === 'programada')->count();
        $eventoNoProgramada = $reservaciones->filter(fn($r) => in_array($normalize($r->tipo_evento), ['no programada', 'no-programada', 'noprogramada'], true))->count();
        $eventoExpres = $reservaciones->filter(fn($r) => in_array($normalize($r->tipo_evento), ['expres', 'exprés', 'paquetería express'], true))->count();
        $eventoApartado = $reservaciones->filter(fn($r) => $normalize($r->tipo_evento) === 'apartado')->count();

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
            'eventoProgramada',
            'eventoNoProgramada',
            'eventoExpres',
            'eventoApartado',
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

    /**
     * Devuelve las Órdenes de Compra relacionadas a una reservación y sus entradas (si se encuentran).
     * Respuesta JSON: { ocs: [ { id, docnum, fecha, almacen, total, entradas: [ { docnum, fecha, ref_proveedor, total } ] } ] }
     */
    public function ocRelacionadas(Request $request, $id)
    {
        try {
            $id = (int) $id;

            // Intentar obtener la reservación desde el SP (igual que en GestionSupplierController::getDetails)
            $data = DB::connection('sqlsrv_proveedores')->select("EXEC sp_consultar_reservaciones_todas");
            $evento = collect($data)->firstWhere('id', $id);

            $ordenes = collect();
            if ($evento) {
                if (isset($evento->orden_compra) && $evento->orden_compra !== null) {
                    if (is_array($evento->orden_compra)) {
                        $ordenes = collect($evento->orden_compra);
                    } elseif (is_string($evento->orden_compra)) {
                        $tmp = json_decode($evento->orden_compra, true);
                        if (is_array($tmp)) {
                            $ordenes = collect($tmp);
                        } else {
                            $ordenes = collect(explode(',', $evento->orden_compra));
                        }
                    } else {
                        $ordenes = collect([(string) $evento->orden_compra]);
                    }
                }
            }

            // Si no hay ordenes en el SP, buscar en la tabla reservacion_orden_compra
            if ($ordenes->isEmpty()) {
                $fromTable = DB::connection('sqlsrv_proveedores')
                    ->table('reservacion_orden_compra')
                    ->where('reservacion_id', $id)
                    ->pluck('orden_compra')
                    ->map(fn($v) => trim((string)$v))
                    ->filter()
                    ->values();

                $ordenes = collect($fromTable);
            }

            $ocs = $ordenes->map(function ($o) {
                $num = trim((string)$o);

                // Intentar enriquecer con datos desde SAP (encabezado PO)
                $fecha = null;
                $almacen = null;
                $total = null;
                try {
                    // Primero intentamos usando el helper existente (sapGetPO)
                    $resp = app()->call([GestionSupplierController::class, 'sapGetPO'], [
                        'docNum' => $num,
                        'sl'     => app(SAPServiceLayer::class)
                    ]);

                    if ($resp instanceof \Illuminate\Http\JsonResponse) {
                        $arr = $resp->getData(true);
                    } else {
                        $body = is_string($resp) ? $resp : (method_exists($resp, 'getContent') ? $resp->getContent() : json_encode($resp));
                        $arr = json_decode($body, true) ?? [];
                    }

                    if (isset($arr['ok']) && $arr['ok']) {
                        $po = $arr['po'] ?? [];
                        $lines = $arr['lines'] ?? [];
                        $fecha = $po['DocDate'] ?? ($po['DocDateString'] ?? null);
                        $total = $po['DocTotal'] ?? $po['DocTotalFC'] ?? null;
                        $docEntry = $po['DocEntry'] ?? null;
                        if (!empty($lines) && isset($lines[0]['WarehouseCode'])) {
                            $almacen = $lines[0]['WarehouseCode'];
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('ocRelacionadas sapGetPO error: ' . $e->getMessage());
                }

                // Si no obtuvimos datos con sapGetPO, intentar consulta directa al Service Layer
                if (empty($fecha) && empty($total)) {
                    try {
                        $sl = app(SAPServiceLayer::class);
                        $qHead = "PurchaseOrders?\$filter=DocNum eq $num";
                        $headRaw = $sl->request('GET', $qHead);
                        $headArr = json_decode(is_string($headRaw) ? $headRaw : (string)$headRaw->getBody(), true) ?? [];
                        $po = $headArr['value'][0] ?? null;
                        if ($po) {
                            $fecha = $po['DocDate'] ?? ($po['DocDateString'] ?? null);
                            $total = $po['DocTotal'] ?? $po['DocTotalFC'] ?? null;
                            $docEntry = $po['DocEntry'] ?? null;
                            if ($docEntry) {
                                $qFull = "PurchaseOrders($docEntry)";
                                $fullRaw = $sl->request('GET', $qFull);
                                $fullArr = json_decode(is_string($fullRaw) ? $fullRaw : (string)$fullRaw->getBody(), true) ?? [];
                                $lines = $fullArr['DocumentLines'] ?? [];
                                if (!empty($lines) && isset($lines[0]['WarehouseCode'])) {
                                    $almacen = $lines[0]['WarehouseCode'];
                                }
                            }
                        }
                    } catch (\Throwable $e) {
                        Log::warning('ocRelacionadas SL direct error: ' . $e->getMessage());
                    }
                }

                // Obtener Entradas (GRPO) relacionadas por DocEntry
                $entradasOut = [];
                if (!empty($docEntry)) {
                    try {
                        $sl = app(SAPServiceLayer::class);
                        $q = "PurchaseDeliveryNotes?\$filter=DocumentLines/any(d: d/BaseEntry eq $docEntry and d/BaseType eq 22)";
                        $res = $sl->request('GET', $q);
                        $json = json_decode(is_string($res) ? $res : (string)$res->getBody(), true) ?? [];
                        $vals = $json['value'] ?? [];

                        // Si no encontró con BaseType, intentar sin esa condición
                        if (empty($vals)) {
                            $q2 = "PurchaseDeliveryNotes?\$filter=DocumentLines/any(d: d/BaseEntry eq $docEntry)";
                            $res2 = $sl->request('GET', $q2);
                            $json2 = json_decode(is_string($res2) ? $res2 : (string)$res2->getBody(), true) ?? [];
                            $vals = $json2['value'] ?? [];
                        }

                        $entradasOut = collect($vals)->map(function($pdn) {
                            return [
                                'docnum' => $pdn['DocNum'] ?? null,
                                'docentry' => $pdn['DocEntry'] ?? null,
                                'fecha' => $pdn['DocDate'] ?? null,
                                'ref_proveedor' => $pdn['NumAtCard'] ?? null,
                                'total' => $pdn['DocTotal'] ?? null,
                            ];
                        })->values()->all();
                        Log::info('ocRelacionadas GRPO query', ['docEntry' => $docEntry, 'found' => count($entradasOut)]);

                        if (empty($entradasOut)) {
                            try {
                                Log::info("ocRelacionadas GRPO fallback: no vals for docEntry={$docEntry}, trying bulk fetch expand lines");
                                $resAll = $sl->request('GET', "PurchaseDeliveryNotes?") ;
                                try {
                                    $resAll = $sl->request('GET', "PurchaseDeliveryNotes?");
                                } catch (\Throwable $inner) {
                                    Log::warning('ocRelacionadas GRPO fallback request error: ' . $inner->getMessage());
                                }
                                try {
                                    $resAll = $sl->request('GET', 'PurchaseDeliveryNotes?$top=500&$expand=DocumentLines');
                                } catch (\Throwable $inner) {
                                    Log::warning('ocRelacionadas GRPO fallback expand error: ' . $inner->getMessage());
                                }
                                $allArr = json_decode(is_string($resAll) ? $resAll : (string)$resAll->getBody(), true) ?? [];
                                $allVals = $allArr['value'] ?? [];
                                $matches = [];
                                foreach ($allVals as $pdn) {
                                    $lines = $pdn['DocumentLines'] ?? [];
                                    foreach ($lines as $ln) {
                                        $base = $ln['BaseEntry'] ?? ($ln['BaseEntry'] ?? null);
                                        if ($base !== null && ((string)$base) === ((string)$docEntry)) {
                                            $matches[] = $pdn;
                                            break;
                                        }
                                    }
                                }
                                Log::info('ocRelacionadas GRPO fallback scanned', ['total_pdn' => count($allVals), 'matches' => count($matches)]);
                                if (!empty($matches)) {
                                    $entradasOut = collect($matches)->map(function($pdn) {
                                        return [
                                            'docnum' => $pdn['DocNum'] ?? null,
                                            'docentry' => $pdn['DocEntry'] ?? null,
                                            'fecha' => $pdn['DocDate'] ?? null,
                                            'ref_proveedor' => $pdn['NumAtCard'] ?? null,
                                            'total' => $pdn['DocTotal'] ?? null,
                                        ];
                                    })->values()->all();
                                }
                            } catch (\Throwable $e) {
                                Log::warning('ocRelacionadas GRPO fallback error: ' . $e->getMessage());
                            }
                        }
                    } catch (\Throwable $e) {
                        Log::warning('ocRelacionadas GRPO query error: ' . $e->getMessage());
                    }
                }

                return [
                    'docnum' => $num,
                    'docentry' => $docEntry ?? null,
                    'fecha' => $fecha,
                    'almacen' => $almacen,
                    'total' => $total,
                    'entradas' => $entradasOut
                ];
            })->values()->all();

            return response()->json(['ocs' => $ocs]);
        } catch (\Throwable $e) {
            Log::warning('ocRelacionadas error: ' . $e->getMessage());
            return response()->json(['ocs' => []], 500);
        }
    }
}
