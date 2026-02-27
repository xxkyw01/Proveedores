<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class RolLider
{
    public function handle($request, Closure $next)
    {
        try {
            Log::debug('RolLider middleware - session snapshot', [
                'session_all' => session()->all(),
                'session_id' => session()->getId(),
                'has_proveedor' => session()->has('Proveedor'),
                'has_usuario' => session()->has('Usuario'),
            ]);
        } catch (\Throwable $e) {
        }

        if (session()->has('Usuario')) {
            $rol = session('Usuario')['IdRol'] ?? null;
            if (in_array($rol, [5 , 6])) {
                return $next($request);
            }
        }
        return redirect()->route('proveedor.login')->withErrors('Acceso denegado.');
    }
}
