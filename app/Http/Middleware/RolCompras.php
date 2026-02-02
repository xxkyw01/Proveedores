<?php

namespace App\Http\Middleware;

use Closure;

class RolCompras
{
    public function handle($request, Closure $next)
    {
        if (session()->has('Usuario')) {
            $rol = session('Usuario')['IdRol'] ?? null;
            if (in_array($rol, [1, 2 , 3 , 4, 5])) {
                return $next($request);
            }
        }
        return redirect()->route('proveedor.login')->withErrors('Acceso denegado.');
    }
}
