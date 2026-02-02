<?php

namespace App\Http\Middleware;

use Closure;

class RolMonitor
{
    public function handle($request, Closure $next)
    {
        if (session()->has('Usuario')) {
            $rol = session('Usuario')['IdRol'] ?? null;

            if (in_array($rol, [8])) {
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rol no autorizado para Monitor'
                ], 403);
            }

            return redirect()->route('proveedor.login')->with('error', 'Rol no autorizado.');
        }

        // Si no hay sesión iniciada
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado o sesión expirada'
            ], 401);
        }

        return redirect()->route('proveedor.login')->withErrors('Acceso denegado.');
    }
}
