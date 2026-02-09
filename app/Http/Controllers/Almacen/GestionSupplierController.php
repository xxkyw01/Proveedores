<?php

namespace App\Http\Controllers\Almacen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\almacen\Sucursal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\SAPServiceLayer;
use Throwable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;


class GestionSupplierController extends Controller
{
    public function index(Request $request)
    {
        $rolId = session('Usuario.IdRol');
        $sucursalUsuario = session('Usuario.SucursalID');

        if (in_array($rolId, [1, 3, 4, 5, 6, 7])) {
            $sucursal_id = $request->get('sucursal_id') ?? 1;
        } else {
            $sucursal_id = $sucursalUsuario;
        }

        $fecha = now()->format('Y-m-d');
        $reservaciones = DB::connection('sqlsrv_proveedores')
            ->select("EXEC sp_consultar_reservaciones_todas @fechaInicio = ?", [$fecha]);

        $sucursales = Sucursal::all();

        return view('pages.almacen.AgendaProveedor', [
            'sucursal_id' => $sucursal_id,
            'sucursales' => $sucursales,
            'reservaciones' => $reservaciones,
            'fecha' => $fecha,
            'getStatusClass' => function ($status) {},
            'formatOrdenCompra' => function ($data) {}
        ]);
    }

    public function reciboMercancia(Request $request)
    {
        $rolId = session('Usuario.IdRol');
        $sucursalUsuario = session('Usuario.SucursalID');

        if (in_array($rolId, [1, 3, 4, 5, 6, 7])) {
            $sucursal_id = $request->get('sucursal_id') ?? 1;
        } else {
            $sucursal_id = $sucursalUsuario;
        }

        $fecha = now()->format('Y-m-d');
        $reservaciones = DB::connection('sqlsrv_proveedores')
            ->select("EXEC sp_consultar_reservaciones_todas_v2 @fechaInicio = ?", [$fecha]);

        $sucursales = Sucursal::all();

        return view('pages.almacen.ReciboMercancia', [
            'sucursal_id' => $sucursal_id,
            'sucursales' => $sucursales,
            'reservaciones' => $reservaciones,
            'fecha' => $fecha,
            'getStatusClass' => function ($status) {},
            'formatOrdenCompra' => function ($data) {}
        ]);
    }

    public function verEvidencia($id)
    {
        $row = DB::connection('sqlsrv_proveedores')
            ->table('reservaciones')
            ->select('evidencia_path', 'evidencia_nombre', 'evidencia_mime')
            ->where('id', $id)
            ->first();

        if ((!$row || !$row->evidencia_path)) {
            $row = DB::connection('sqlsrv_proveedores')
                ->table('reservacion_cancelada')
                ->select('evidencia_path', 'evidencia_nombre', 'evidencia_mime')
                ->where('reservacion_id', $id)
                ->first();
        }

        if (!$row || !$row->evidencia_path) {
            abort(404, 'Evidencia no encontrada');
        }

        $path = ltrim(preg_replace('#^public/#', '', $row->evidencia_path), '/');

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Archivo no existe en almacenamiento');
        }

        return Storage::disk('public')->response(
            $path,
            $row->evidencia_nombre ?? basename($path),
            ['Content-Type' => $row->evidencia_mime ?? 'application/octet-stream']
        );
    }

    public function getAgendaData(Request $request)
    {
        try {
            $fecha       = $request->input('fecha');
            $sucursal_id = (int) $request->input('sucursal_id');
            $fechaInicio = \Carbon\Carbon::parse($fecha)->subDays(7)->format('Y-m-d');
            
            $rows = DB::connection('sqlsrv_proveedores')->select(
                "EXEC sp_consultar_reservaciones_todas @fechaInicio = ?",
                [$fechaInicio]
            );

            $nombreSucursal = trim((string) $this->obtenerNombreSucursal($sucursal_id));

            $filtered = array_values(array_filter($rows, function ($r) use ($fecha, $sucursal_id, $nombreSucursal) {
                $esMismaFecha = \Carbon\Carbon::parse($r->fecha)->toDateString() === $fecha;

                if (isset($r->sucursal_id)) {
                    return $esMismaFecha && ((int)$r->sucursal_id === $sucursal_id);
                }

                if (isset($r->sucursal_nombre) && $r->sucursal_nombre !== null) {
                    return $esMismaFecha
                        && mb_strtolower(trim($r->sucursal_nombre)) === mb_strtolower($nombreSucursal);
                }

                return false;
            }));

            if (!empty($filtered)) {
                $ids = array_map(fn($r) => (int)$r->id, $filtered);

                $map = DB::connection('sqlsrv_proveedores')
                    ->table('reservacion_orden_compra')
                    ->whereIn('reservacion_id', $ids)
                    ->select('reservacion_id', 'orden_compra')
                    ->get()
                    ->groupBy('reservacion_id')
                    ->map(
                        fn($g) => $g->pluck('orden_compra')
                            ->map(fn($v) => trim((string)$v))
                            ->filter()
                            ->values()
                            ->all()
                    );

                foreach ($filtered as $it) {
                    $fromSp = [];
                    if (isset($it->orden_compra) && $it->orden_compra !== null) {
                        if (is_array($it->orden_compra)) {
                            $fromSp = $it->orden_compra;
                        } elseif (is_string($it->orden_compra)) {
                            $tmp = json_decode($it->orden_compra, true);
                            $fromSp = is_array($tmp) ? $tmp : explode(',', $it->orden_compra);
                        } else {
                            $fromSp = [(string)$it->orden_compra];
                        }
                    }
                    $fromSp = collect($fromSp)->map(fn($v) => trim((string)$v))->filter()->values()->all();
                    $fromMap = $map[(int)$it->id] ?? [];
                    $it->orden_compra = collect($fromSp)->merge($fromMap)->unique()->values()->all();
                }
            }

            return response()->json($filtered);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function obtenerNombreSucursal($id)
    {
        return DB::connection('sqlsrv_proveedores')
            ->table('IntranetProveedores.dbo.sucursales')
            ->where('id', $id)
            ->value('nombre');
    }

    public function getDetails($id)
    {
        try {
            $data   = DB::connection('sqlsrv_proveedores')->select("EXEC sp_consultar_reservaciones_todas");
            $evento = collect($data)->firstWhere('id', (int) $id);

            if (!$evento) {
                return response()->json(['error' => 'Reservación no encontrada'], 404);
            }

            $ordenes = collect();
            if (isset($evento->orden_compra) && $evento->orden_compra !== null) {
                if (is_array($evento->orden_compra)) {
                    $ordenes = collect($evento->orden_compra);
                } elseif (is_string($evento->orden_compra)) {
                    $ordenes = collect(explode(',', $evento->orden_compra))
                        ->map(fn($v) => trim($v))
                        ->filter(fn($v) => $v !== '');
                } else {
                    $ordenes = collect([(string) $evento->orden_compra]);
                }
            }

            if ($ordenes->isEmpty()) {
                $ordenes = DB::connection('sqlsrv_proveedores')
                    ->table('reservacion_orden_compra')
                    ->where('reservacion_id', $id)
                    ->pluck('orden_compra');
            }

            $evento->orden_compra = $ordenes
                ->map(fn($v) => trim((string) $v))
                ->filter(fn($v) => $v !== '')
                ->values()
                ->all();

            $colsMeta = ['commit_afterrecep', 'evidencia_nombre', 'evidencia_path', 'evidencia_mime', 'evidencia_size'];
            if ($this->columnasExisten('reservaciones', $colsMeta)) {
                $extra = DB::connection('sqlsrv_proveedores')
                    ->table('reservaciones')
                    ->where('id', $id)
                    ->select($colsMeta)
                    ->first();

                if ($extra) {
                    foreach ($colsMeta as $c) {
                        if (!isset($evento->$c)) $evento->$c = $extra->$c ?? null;
                    }
                }
            }

            return response()->json($evento);
        } catch (\Throwable $e) {
            //Log::error('getDetails error: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al obtener detalles'], 500);
        }
    }

    private function columnasExisten(string $tabla, array $cols): bool
    {
        $encontradas = DB::connection('sqlsrv_proveedores')->table('INFORMATION_SCHEMA.COLUMNS')
            ->where('TABLE_SCHEMA', 'dbo')
            ->whereIn('COLUMN_NAME', $cols)
            ->count();

        return $encontradas === count($cols);
    }

    public function getArticulosPendientes($orden)
    {
        $articulos = DB::connection('sqlsrv_proveedores')
            ->select("EXEC sp_Consultar_ArticulosPendientes @NumeroOrdenCompra = ?", [$orden]);

        return response()->json($articulos);
    }

    public function actualizarEstado(Request $request)
    {
        try {
            $request->validate([
                'id'         => 'required|integer',
                'estado'     => 'required|string',
                'comentario' => 'nullable|string|max:4000',
                'evidencia'  => 'nullable|file|max:10240',
            ]);

            $id     = (int) $request->input('id');
            $estado = trim($request->input('estado'));

            DB::connection('sqlsrv_proveedores')->statement(
                'EXEC sp_actualizar_estado_reservacion ?, ?',
                [$id, $estado]
            );

            $comentario = trim((string) $request->input('comentario', ''));
            $update = [
                'commit_afterrecep' => $comentario !== '' ? $comentario : null,
            ];

            if ($request->hasFile('evidencia')) {
                $file = $request->file('evidencia');
                $path = $file->store('reservaciones', 'public');
                $update += [
                    'evidencia_path'   => $path,
                    'evidencia_nombre' => $file->getClientOriginalName(),
                    'evidencia_mime'   => $file->getClientMimeType(),
                    'evidencia_size'   => $file->getSize(),
                ];
            }

            $update = $this->filtraColumnasExistentes('reservaciones', $update);

            if (!empty($update)) {
                DB::connection('sqlsrv_proveedores')
                    ->table('reservaciones')
                    ->where('id', $id)
                    ->update($update);
            }

            if (in_array($estado, ['No asistió', 'Cancelada por proveedor'])) {
                $this->moverReservacionCancelada($id, $estado);
            }

            return response()->json(['success' => true, 'message' => 'Estado actualizado correctamente.']);
        } catch (\Throwable $e) {
            //Log::error('actualizarEstado error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    private function filtraColumnasExistentes(string $tabla, array $data): array
    {
        $cols = DB::connection('sqlsrv_proveedores')
            ->table('INFORMATION_SCHEMA.COLUMNS')
            ->where('TABLE_SCHEMA', 'dbo')
            ->where('TABLE_NAME', $tabla)
            ->pluck('COLUMN_NAME')
            ->map(fn($c) => strtolower($c))
            ->toArray();

        return collect($data)
            ->filter(fn($v, $k) => in_array(strtolower($k), $cols) && $v !== null)
            ->all();
    }

    private function moverReservacionCancelada($id, $motivo)
    {
        $cita = DB::connection('sqlsrv_proveedores')
            ->table('reservaciones')
            ->where('id', $id)
            ->first();

        if (!$cita) return;

        DB::connection('sqlsrv_proveedores')->table('reservacion_cancelada')->insert([
            'reservacion_id'            => $cita->id,
            'sucursal_id'               => $cita->sucursal_id,
            'transporte_id'             => $cita->transporte_id,
            'fecha'                     => $cita->fecha,
            'hora'                      => $cita->hora,
            'proveedor_id'              => $cita->proveedor_id,
            'idUser'                    => $cita->idUser,
            'orden_compra'              => $cita->orden_compra,
            'anden_id'                  => $cita->anden_id,
            'descripcion'               => $cita->descripcion,
            'comentario_usuario'        => $cita->comentario_usuario,
            'estado_cancelado'          => now(),
            'motivo_cancelacion'        => $motivo,
            'cancelado_por'             => auth()->user()->name ?? 'Almacen',
            'created_at'                => $cita->created_at ?? now(),
            'motivo_evento_no_programado' => $cita->motivo_evento_no_programado,
            'evidencias'                => $cita->evidencias,
            'tipo_cancelacion'          => 'Agenda',
            'tipo_evento'               => $cita->tipo_evento,
            'estado'                    => $cita->estado,
            'commit_afterrecep'           => $cita->commit_afterrecep,
            'evidencia_nombre'            => $cita->evidencia_nombre,
            'evidencia_path'              => $cita->evidencia_path,
            'evidencia_mime'              => $cita->evidencia_mime,
            'evidencia_size'              => $cita->evidencia_size,

        ]);
        DB::connection('sqlsrv_proveedores')->table('reservacion_orden_compra')
            ->where('reservacion_id', $id)
            ->delete();

        DB::connection('sqlsrv_proveedores')->table('reservaciones')
            ->where('id', $id)
            ->delete();
    }

    public function agregarOCReservacion(Request $request, $id)
    {
        $reservacionId = (int) $id;

        $oc = trim((string) $request->input('orden_compra', ''));
        $folio = trim((string) $request->input('folio_factura', ''));

        if (!$reservacionId || $oc === '') {
            return response()->json(['ok' => false, 'msg' => 'Falta reservacion_id u orden_compra'], 422);
        }

        preg_match('/\d+/', $oc, $m);
        $ocNum = $m[0] ?? $oc;

        $conn = DB::connection('sqlsrv_proveedores');

        $exists = $conn->table('reservacion_orden_compra')
            ->where('reservacion_id', $reservacionId)
            ->where('orden_compra', $ocNum)
            ->exists();

        if ($exists) {
            return response()->json(['ok' => false, 'msg' => "La OC $ocNum ya está agregada en esta cita."], 409);
        }

        $conn->table('reservacion_orden_compra')->insert([
            'reservacion_id' => $reservacionId,
            'orden_compra'   => $ocNum,
            'folio_factura'  => $folio ?: null,
        ]);

        return response()->json(['ok' => true, 'oc' => $ocNum, 'msg' => 'OC agregada a la cita']);
    }

    public function ocsDisponiblesParaReservacion($id)
    {
        $reservacionId = (int)$id;
    
        $res = DB::connection('sqlsrv_proveedores')->table('reservaciones')
            ->select('id', 'proveedor_id', 'sucursal_id')
            ->where('id', $reservacionId)
            ->first();
    
        if (!$res) return response()->json(['ok' => false, 'msg' => 'Reservación no encontrada'], 404);
    
        $cardCode = trim((string)$res->proveedor_id);
        $sucursalId = (int)$res->sucursal_id;
    
        try {
            $serieOC = $this->poSeriePorSucursal($sucursalId);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'msg' => $e->getMessage()], 422);
        }
    
        $ordenes = DB::connection('sqlsrv_proveedores')
            ->select("EXEC dbo.sp_Consultar_OrdenesCompraAbiertas ?, ?", [$cardCode, $serieOC]);
    
        $ya = DB::connection('sqlsrv_proveedores')
            ->table('reservacion_orden_compra')
            ->where('reservacion_id', $reservacionId)
            ->pluck('orden_compra')
            ->map(fn($x) => (string)$x)
            ->toArray();
    
        $ordenesFiltradas = array_values(array_filter($ordenes, function ($o) use ($ya) {
            $num = (string)($o->NumeroOrdenCompra ?? $o->DocNum ?? '');
            return $num !== '' && !in_array($num, $ya, true);
        }));
    
        return response()->json([
            'ok' => true,
            'cardCode' => $cardCode,
            'sucursal_id' => $sucursalId,
            'serie' => $serieOC,
            'ordenes' => $ordenesFiltradas
        ]);
    }    

    public function ocsDeReservacion($id)
    {
        $reservacionId = (int)$id;

        $rows = DB::connection('sqlsrv_proveedores')
            ->table('reservacion_orden_compra')
            ->where('reservacion_id', $reservacionId)
            ->select('orden_compra', 'folio_factura')
            ->orderBy('orden_compra')
            ->get();

        return response()->json([
            'ok' => true,
            'ocs' => $rows
        ]);
    }

    private function getSerieGRPOPorSucursal(int $sucursalId): int
    {
        $map = [
            1 => 156, 
            4 => 157, 
        ];

        return $map[$sucursalId] ?? 156; 
    }

    private function poSeriePorSucursal(int $sucursalId): string
{
    $map = config('sap_series.po_series_by_sucursal', []);
    if (!isset($map[$sucursalId])) {
        throw new \RuntimeException("Sucursal sin serie PO configurada: $sucursalId");
    }
    return $map[$sucursalId];
}

private function grpoSeriePorSucursal(int $sucursalId): int
{
    $map = config('sap_series.grpo_series_by_sucursal', []);
    if (!isset($map[$sucursalId])) {
        throw new \RuntimeException("Sucursal sin serie GRPO configurada: $sucursalId");
    }
    return (int)$map[$sucursalId];
}

    public function sapValidarGRPO(Request $req)
    {
        $docNum      = (int) $req->input('docNum');
        $sucursal_id = (int) $req->input('sucursal_id'); 
        $cardCode    = trim((string) $req->input('cardCode', ''));
        $numAtCard   = trim((string) $req->input('numAtCard', ''));
        $lines       = $req->input('lines', []);

        $errors = [];

        if (!$docNum)        $errors[] = 'Falta DocNum de la orden de compra.';
        if ($cardCode === '') $errors[] = 'Falta CardCode del proveedor.';
        if ($numAtCard === '') $errors[] = 'Falta No. ref. del acreedor (NumAtCard).';

        if (!is_array($lines) || !count($lines)) {
            $errors[] = 'No se recibieron líneas para la GRPO.';
        } else {
            foreach ($lines as $i => $ln) {
                $qty      = (float)($ln['Quantity'] ?? 0);
                $baseLine = $ln['BaseLine'] ?? null;

                if (!is_numeric($baseLine)) {
                    $errors[] = "Línea {$i}: falta BaseLine.";
                }
                if (!($qty > 0)) {
                    $errors[] = "Línea {$i}: cantidad debe ser mayor a 0.";
                }
            }
        }

        if ($errors) {
            return response()->json([
                'ok'     => false,
                'errors' => $errors,
            ], 200);
        }

        return response()->json([
            'ok' => true,
        ]);
    }

    public function sapGetPO($docNum, SAPServiceLayer $sl)
    {
        try {
            $docNum = (int) trim($docNum);
            //Log::info('sapGetPO IN', ['docNum' => $docNum]);

            $qHead = "PurchaseOrders?\$filter=DocNum eq $docNum";
            //Log::info('sapGetPO qHead', ['q' => $qHead]);

            $headArr = $this->slToArray($sl->request('GET', $qHead));

            if (!isset($headArr['value'][0])) {
                return response()->json([
                    'ok'  => false,
                    'msg' => "No se encontró la OC $docNum en SAP",
                    'raw' => $headArr,
                ], 404);
            }

            $po       = $headArr['value'][0];
            $docEntry = $po['DocEntry'] ?? null;

            if (!$docEntry) {
                //Log::error('sapGetPO sin DocEntry', ['po' => $po]);
                return response()->json([
                    'ok'  => false,
                    'msg' => 'La respuesta de SAP no contiene DocEntry',
                    'raw' => $po,
                ], 500);
            }

            $qLines = "PurchaseOrders($docEntry)";
            //Log::info('sapGetPO qLines', ['q' => $qLines]);

            $poFull = $this->slToArray($sl->request('GET', $qLines));
            $lines  = $poFull['DocumentLines'] ?? [];

            return response()->json([
                'ok'    => true,
                'po'    => $po,
                'lines' => $lines,
            ]);
        } catch (ClientException $e) {
            $body = (string) $e->getResponse()->getBody();
            /* Log::error('sapGetPO ClientException', [
                'msg'     => $e->getMessage(),
                'sapBody' => $body,
            ]); */

            return response()->json([
                'ok'      => false,
                'msg'     => 'Error SAP al consultar la OC',
                'sapBody' => json_decode($body, true) ?? $body,
            ], 500);
        } catch (\Throwable $e) {
            /* Log::error('sapGetPO error', [
                'msg'   => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]); */

            return response()->json([
                'ok'  => false,
                'msg' => 'Error al consultar la OC en SAP',
                'err' => $e->getMessage(),
            ], 500);
        }
    }

    private function slToArray($raw): array
    {
        if (is_array($raw)) {
            return $raw;
        }
        if ($raw instanceof \Psr\Http\Message\ResponseInterface) {
            $body = (string) $raw->getBody();
        } elseif (is_string($raw)) {
            $body = $raw;
        } else {
            $body = json_encode($raw);
        }

        $json = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('slToArray json_decode error', [
                'error' => json_last_error_msg(),
                'body'  => $body,
            ]);
            return [];
        }

        return $json ?? [];
    }

    public function crearGRPO(Request $req, SAPServiceLayer $sl)
    {
        $docNum    = (int) $req->input('docNum');
        $cardCode  = trim((string) $req->input('cardCode', ''));
        $numAtCard = trim((string) $req->input('numAtCard', ''));
        $lines     = $req->input('lines', []);
        $comments  = trim((string) $req->input('comments', ''));

        $errors = [];
        if (!$docNum)          $errors[] = 'Falta DocNum de la orden de compra.';
        if ($cardCode === '')  $errors[] = 'Falta CardCode del proveedor.';
        if ($numAtCard === '') $errors[] = 'Falta No. ref. del acreedor (NumAtCard).';
        if (!is_array($lines) || !count($lines)) {
            $errors[] = 'No se recibieron líneas para la GRPO.';
        }

        if ($errors) {
            return response()->json([
                'ok'     => false,
                'msg'    => 'Errores de validación',
                'errors' => $errors,
            ], 200);
        }

        try {
            $qHead = "PurchaseOrders?\$filter=DocNum eq $docNum";
            //Log::info('crearGRPO qHead', ['q' => $qHead]);

            $headArr = $this->slToArray($sl->request('GET', $qHead));

            if (!isset($headArr['value'][0])) {
                return response()->json([
                    'ok'  => false,
                    'msg' => "No se encontró la OC $docNum en SAP",
                    'raw' => $headArr,
                ], 404);
            }

            $po       = $headArr['value'][0];
            $docEntry = $po['DocEntry'] ?? null;

            if (!$docEntry) {
                //Log::error('crearGRPO sin DocEntry', ['po' => $po]);
                return response()->json([
                    'ok'  => false,
                    'msg' => 'La respuesta de SAP no contiene DocEntry',
                    'raw' => $po,
                ], 500);
            }

            $docLines = [];
            foreach ($lines as $ln) {
                $baseLine = isset($ln['BaseLine']) ? (int) $ln['BaseLine'] : null;
                $qty      = (float) ($ln['Quantity'] ?? 0);

                if ($baseLine === null || !($qty > 0)) {
                    continue;
                }

                $docLines[] = [
                    'BaseEntry'     => $docEntry,
                    'BaseType'      => 22,
                    'BaseLine'      => $baseLine,
                    'Quantity'      => $qty,
                    'WarehouseCode' => $ln['WarehouseCode'] ?? null,
                ];
            }

            if (!count($docLines)) {
                return response()->json([
                    'ok'  => false,
                    'msg' => 'No hay líneas válidas para crear la GRPO.',
                ], 200);
            }

            $today = Carbon::now()->format('Y-m-d');
            $serie = $this->grpoSeriePorSucursal((int)$req->input('sucursal_id'));

            $payload = [
                'Series'       => $serie,
                'CardCode'      => $cardCode,
                'DocDate'       => $today,
                'DocDueDate'    => $today,
                'NumAtCard'     => $numAtCard,
                'Comments'      => $comments ?: "Recepción desde Intranet Proveedores. | OC $docNum",
                'DocumentLines' => $docLines,
            ];

            //Log::info('crearGRPO payload', $payload);

            $res  = $sl->request('POST', 'PurchaseDeliveryNotes', ['json' => $payload]);
            $json = $this->slToArray($res);

            return response()->json([
                'ok'   => true,
                'msg'  => 'GRPO creado correctamente',
                'data' => $json,
            ], 200);
        } catch (ClientException $e) {
            $body = (string) $e->getResponse()->getBody();
            /* Log::error('crearGRPO ClientException', [
                'msg'     => $e->getMessage(),
                'sapBody' => $body,
            ]); */

            return response()->json([
                'ok'      => false,
                'msg'     => 'Error SAP al crear la GRPO',
                'sapBody' => json_decode($body, true) ?? $body,
            ], 500);
        } catch (\Throwable $e) {
            /* Log::error('crearGRPO error', [
                'msg'   => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]); */

            return response()->json([
                'ok'  => false,
                'msg' => 'Error interno al crear la GRPO',
                'err' => $e->getMessage(),
            ], 500);
        }
    }
}