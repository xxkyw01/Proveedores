<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Mensaje;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        $contactos = [];

        if (session()->has('Proveedor')) {
            $yo = session('Proveedor');

            $contactos = DB::connection('sqlsrv_proveedores')->table('usuarios')
                ->whereIn('IdRol', [1, 2, 3, 4, 5])
                ->where('Activo', 'Y')
                ->select('IdUsuario as id', 'Nombre', DB::raw("'U' as tipo"))
                ->get();

            $es_proveedor = true;
        } elseif (session()->has('Usuario')) {
            $yo = session('Usuario');

            $usuarios = DB::connection('sqlsrv_proveedores')->table('usuarios')
                ->where('IdUsuario', '<>', $yo['IdUsuario'])
                ->where('Activo', 'Y')
                ->select('IdUsuario as id', 'Nombre', DB::raw("'U' as tipo"));

            $proveedores = DB::connection('sqlsrv_proveedores')->table('proveedor_usuarios')
                ->where('activo', 'Y')
                ->select('id', DB::raw("CONCAT('Proveedor :  ', CardCode) as Nombre"), DB::raw("'P' as tipo"));

            $contactos = $usuarios->unionAll($proveedores)->get();

            $es_proveedor = false;
        }

        return view('pages.mensajeria.index', [
            'contactos' => $contactos,
            'es_proveedor' => $es_proveedor ?? false
        ]);
    }

    public function fetch($tipo, $id)
    {
        try {
            $yo = session('Usuario') ?? session('Proveedor');

            if (!$yo) {
                return response()->json(['error' => 'No hay sesión activa'], 401);
            }

            $yo_id = $yo['IdUsuario'] ?? $yo['id'];
            $yo_tipo = session()->has('Proveedor') ? 'P' : 'U';

            if (!in_array($tipo, ['P', 'U'])) {
                return response()->json(['error' => 'Tipo inválido'], 400);
            }

            Mensaje::where('receptor_id', $yo_id)
                ->where('receptor_tipo', $yo_tipo)
                ->where('remitente_id', $id)
                ->where('remitente_tipo', $tipo)
                ->where('leido', 0)
                ->update(['leido' => 1]);

            $mensajes = Mensaje::where(function ($q) use ($yo_id, $id, $tipo) {
                $q->where('remitente_id', $yo_id)
                    ->where('receptor_id', $id)
                    ->where('receptor_tipo', $tipo);
            })->orWhere(function ($q) use ($yo_id, $id, $tipo) {
                $q->where('receptor_id', $yo_id)
                    ->where('remitente_id', $id)
                    ->where('remitente_tipo', $tipo);
            })->orderBy('fecha_envio')->get();

            return response()->json($mensajes);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'linea' => $th->getLine(),
                'archivo' => $th->getFile()
            ], 500);
        }
    }

    public function send(Request $request)
    {
        $yo = session('Usuario') ?? session('Proveedor');
        $yo_id = $yo['IdUsuario'] ?? $yo['id'];
        $yo_tipo = session()->has('Proveedor') ? 'P' : 'U';

        $mensaje = new Mensaje([
            'remitente_id' => $yo_id,
            'remitente_tipo' => $yo_tipo,
            'receptor_id' => $request->receptor_id,
            'receptor_tipo' => $request->receptor_tipo,
            'mensaje' => $request->mensaje
        ]);
        $mensaje->save();

        try {
            Http::post('http://127.0.0.1:3001/nuevo-mensaje', [
                'receptor_id' => $request->receptor_id,
                'receptor_tipo' => $request->receptor_tipo,
                'mensaje' => $request->mensaje
            ]);
        } catch (\Throwable $e) {
            logger()->error('Error al notificar al WebSocket', [
                'mensaje' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile()
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function chatsRecientes()
    {
        try {
            $yo = session('Usuario') ?? session('Proveedor');
            $yo_id = $yo['IdUsuario'] ?? $yo['id'];
            $yo_tipo = session()->has('Proveedor') ? 'P' : 'U';

            $mensajes = DB::connection('sqlsrv_proveedores')
                ->select("EXEC sp_chats_recientes @id = ?, @tipo = ?", [$yo_id, $yo_tipo]);

            return response()->json($mensajes);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile()
            ], 500);
        }
    }

    public function marcarComoLeido(Request $request)
{
    $contactoId = $request->contacto_id;
    $contactoTipo = $request->contacto_tipo;
    $usuarioActual = auth()->user();

    $receptorId = $usuarioActual->id;
    $receptorTipo = $usuarioActual->esProveedor() ? 'P' : 'U';

    \App\Models\Mensaje::where('remitente_id', $contactoId)
        ->where('remitente_tipo', $contactoTipo)
        ->where('receptor_id', $receptorId)
        ->where('receptor_tipo', $receptorTipo)
        ->where('leido', false)
        ->update(['leido' => true]);

    return response()->json(['success' => true]);
}

}
