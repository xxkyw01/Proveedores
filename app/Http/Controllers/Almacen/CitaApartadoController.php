<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Proveedor\Entidad;
use App\Models\Proveedor\Transporte;

class CitaApartadoController extends Controller
{
    public function index()
    {
        $ciudades    = Entidad::select('id', 'nombre')->orderBy('nombre')->get();
        $transportes = Transporte::select('id', 'tipo', 'nombre', 'es_interno')->orderByDesc('es_interno')->orderBy('tipo')->get();

        $tiposApartado = [
            'Mantenimiento',
            'Control de inventario',
            'Limpieza',
            'Cerrado',
            'Contenedores',
            'Transferencia',
            'Otro'
        ];

        return view('pages.almacen.CitaApartado', compact('ciudades', 'transportes', 'tiposApartado'));
    }


    public function disponibilidad(Request $request)
    {
        $request->validate([
            'sucursal_id'   => 'required|integer',
            'fecha'         => 'required|date',
            'transporte_id' => 'required|integer',
            'anden_id'      => 'nullable|integer'
        ]);

        try {
            if ($request->filled('anden_id') === false) {
                return response()->json([
                    'ok' => true,
                    'horarios' => [], 
                    'nota' => 'Selecciona un andén para ver disponibilidad.'
                ]);
            }

            $rows = DB::connection('sqlsrv_proveedores')->select(
                "EXEC sp_horarios_disponibles_andenes @sucursal_id = ?, @fecha = ?, @transporte_id = ?, @anden_id = ?",
                [
                    $request->sucursal_id,
                    $request->fecha,
                    $request->transporte_id,
                    $request->anden_id
                ]
            );

            $horarios = array_map(function ($r) {
                return [
                    'horario' => $r->horario ?? ($r->Hora ?? null),
                    'estado'  => $r->estado  ?? ($r->Estado ?? 'Disponible'),
                ];
            }, $rows);

            return response()->json(['ok' => true, 'horarios' => $horarios]);
        } catch (\Throwable $e) {
            //Log::error('Disponibilidad (apartado): ' . $e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Error al consultar disponibilidad.'], 500);
        }
    }

    public function store(Request $request)
    {
        $request->merge(['transporte_id' => 8]);
        $idUser = (string) (session('Usuario.IdUsuario') ?? '0');
        $request->validate([
            'sucursal_id'    => 'required|integer',
            'fecha'          => 'required|date',
            'transporte_id'  => 'required|integer',
            'apartado_tipo'  => 'required|string|max:30',
            'motivo'         => 'nullable|string|max:500',
            'anden_id'       => 'required|integer',
            'horas'          => 'required|array|min:1',
            'horas.*'        => 'required|string'
        ]);


        if ($request->alcance === 'anden' && empty($request->anden_id)) {
            return response()->json(['success' => false, 'message' => 'Debes seleccionar un andén.'], 422);
        }

        $idUser       = (string) (session('Usuario.IdUsuario') ?? '0');
        $proveedor_id = null; 

        $success = [];
        $errors  = [];

        foreach ($request->horas as $hora) {
            try {
                $params = [
                    $request->sucursal_id,
                    $request->fecha,
                    $hora,
                    $request->apartado_tipo,
                    $idUser,
                    (int)$request->anden_id,
                    $request->motivo,
                    (int)$request->transporte_id,
                    $proveedor_id
                ];
                $res = DB::connection('sqlsrv_proveedores')->select(
                    "EXEC dbo.sp_insertar_reservacion_apartado
                        @sucursal_id=?, @fecha=?, @hora=?, @apartado_tipo=?, @idUser=?,
                        @anden_id=?, @motivo=?, @transporte_id=?, @proveedor_id=?",
                    $params
                );

                $row = $res[0] ?? null;
                if ($row && isset($row->tipo) && $row->tipo === 'ok') {
                    $success[] = ['hora' => $hora, 'id' => $row->id ?? null];
                } else {
                    $errors[]  = ['hora' => $hora, 'message' => $row->mensaje ?? 'No se pudo crear el apartado.'];
                }
            } catch (\Throwable $e) {
                //Log::warning("Apartado fallo @{$hora}: " . $e->getMessage());
                $errors[] = ['hora' => $hora, 'message' => 'Error al crear el apartado.'];
            }
        }

        if (!empty($errors) && empty($success)) {
            return response()->json(['success' => false, 'message' => 'No se pudo crear ningún apartado.', 'errors' => $errors], 409);
        }

        $msg = count($success) . " apartado(s) creado(s)";
        if (!empty($errors)) {
            $msg .= " — " . count($errors) . " horario(s) rechazado(s).";
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'ok'      => $success,
            'fail'    => $errors
        ]);
    }

    public function obtenerSucursales($entidad_id)
    {
        $sucursales = DB::connection('sqlsrv_proveedores')
            ->table('sucursales')
            ->where('entidad_id', $entidad_id)
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get();

        return response()->json($sucursales);
    }

    public function obtenerAndenes($sucursal_id)
    {
        $andenes = DB::connection('sqlsrv_proveedores')
            ->table('andenes')
            ->where('sucursal_id', $sucursal_id)
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get();

        return response()->json($andenes);
    }
}
