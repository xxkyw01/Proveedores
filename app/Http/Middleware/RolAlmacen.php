<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class RolAlmacen
{
    public function handle($request, Closure $next)
    {
        // LOG TEMPORAL: inspeccionar estado de sesión para depuración de 419
        try {
            Log::debug('RolAlmacen middleware - session snapshot', [
                'session_all' => session()->all(),
                'session_id' => session()->getId(),
                'has_proveedor' => session()->has('Proveedor'),
                'has_usuario' => session()->has('Usuario'),
            ]);
        } catch (\Throwable $e) {
        }

        if (session()->has('Usuario')) {
            $rol = session('Usuario')['IdRol'] ?? null;

            if (in_array($rol, [1, 2, 3, 4, 5, 6 , 7 , 8])) {
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Rol no autorizado para Almacén'
                ], 403);
            }

            return redirect()->route('proveedor.login')->with('error', 'Rol no autorizado.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado o sesión expirada'
            ], 401);
        }

        return redirect()->route('proveedor.login')->withErrors('Acceso denegado.');
    }
}
