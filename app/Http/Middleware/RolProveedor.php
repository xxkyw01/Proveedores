<?php

namespace App\Http\Middleware;

use Closure;

class RolProveedor
{
    public function handle($request, Closure $next)
    {
        if (session()->has('Proveedor')) {
            return $next($request);
        }

        if (session()->has('Usuario')) {
            $rol = session('Usuario')['IdRol'] ?? null;
            if (in_array($rol, [1,2,3,4,5,6])) {
                return $next($request);
            }
        }
        return redirect()->route('proveedor.login')->withErrors('Acceso denegado.');
    }
}
