<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\CitaExpressMail;

class CitaExpressController extends Controller
{
    public function index()
    {
        $ciudades = DB::connection('sqlsrv_proveedores')
            ->table('entidades')
            ->select('id', 'nombre')
            ->get();

        return view('pages.almacen.CitaExpress', compact('ciudades'));
    }

    public function registrar(Request $request)
    {
        Log::info('Iniciando registro de cita express', [
            'usuario' => session('Usuario'),
            'inputs' => $request->all()
        ]);

        if (!session()->has('Usuario')) {
            Log::warning('Intento de registrar sin sesión', ['ip' => $request->ip()]);

            return response()->json([
                'success' => false,
                'message' => 'No autenticado o sesión expirada'
            ], 401);
        }

        try {
            $request->validate([
                'sucursal_id'   => 'required|integer',
                'qbox'          => 'required|string',
                'fecha'         => 'required|date',
                'descripcion'   => 'required|string|min:50',
                'hora'          => 'nullable',
                'proveedor_id'  => 'nullable|string',
                'evidencias' => 'nullable|file|mimes:jpg,jpeg,png,pdf,xlsx,xls|max:2048',

            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validación fallida', ['errores' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            $tipoEntrega = $request->input('qbox');
            $proveedor   = $request->input('proveedor_id') ?: 'Externo';

            $rutaAdjunto = $request->hasFile('evidencias')
                ? $request->file('evidencias')->store('adjuntos_citas_express', 'public')
                : null;

            Log::info('Ejecutando SP sp_insertar_cita_express', [
                'sucursal_id' => $request->input('sucursal_id'),
                'tipoEntrega' => $tipoEntrega,
                'proveedor'   => $proveedor,
                'fecha'       => $request->input('fecha'),
                'hora'        => $request->input('hora') ?: '17:00',
                'descripcion' => $request->input('descripcion'),
                'rutaAdjunto' => $rutaAdjunto
            ]);

            $resultado = DB::connection('sqlsrv_proveedores')->select(
                "EXEC sp_insertar_cita_express ?, ?, ?, ?, ?, ?, ?",
                [
                    $request->input('sucursal_id'),
                    $tipoEntrega,
                    $proveedor,
                    $request->input('fecha'),
                    $request->input('hora') ?: '17:00',
                    $request->input('descripcion'),
                    $rutaAdjunto
                ]
            );

            $sucursalNombre = DB::connection('sqlsrv_proveedores')
                ->table('sucursales')
                ->where('id', $request->input('sucursal_id'))
                ->value('nombre');

            Mail::to([
                'coord.compras@laconcha.com.mx',
                'mesadecontrol.centro@laconcha.com.mx',
                'mesadecontrol@laconcha.com.mx'
            ])
                ->bcc(['auxdesarrollador.it@laconcha.com.mx','sistemas@laconcha.com.mx'])
                ->send(new CitaExpressMail(
                    $sucursalNombre,
                    $tipoEntrega,
                    $proveedor,
                    $request->input('fecha'),
                    $request->input('hora') ?: '17:00',
                    $request->input('descripcion'),
                    $rutaAdjunto
                ));



            Log::info('Resultado SP', ['resultado' => $resultado]);

            return response()->json([
                'success' => true,
                'data'    => $resultado,
                'message' => 'Cita express registrada correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al registrar cita express: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un problema al guardar la cita',
                'error'   => $e->getMessage()
            ], 500);
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


    public function lista()
    {
        $citas = DB::connection('sqlsrv_proveedores')
            ->table('reservaciones')
            ->where('tipo_evento', 'Paquetería Express')
            ->orderByDesc('id')
            ->get();

        return response()->json($citas);
    }

    public function estado($id, $estado)
    {
        DB::connection('sqlsrv_proveedores')
            ->table('reservaciones')
            ->where('id', $id)
            ->update(['estado' => ucfirst($estado)]);

        return response()->json(['success' => true]);
    }
}
