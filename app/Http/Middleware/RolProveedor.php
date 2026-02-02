<?php

namespace App\Http\Middleware;

use Closure;

class RolProveedor
{
    public function handle($request, Closure $next)
    {
        // Si es un proveedor autenticado
        if (session()->has('Proveedor')) {
            return $next($request);
        }

        // Si es un usuario interno con rol Admin (1) , Compras (3)  , Mejora Continua (4) o Desarollador (5)
        if (session()->has('Usuario')) {
            $rol = session('Usuario')['IdRol'] ?? null;
            if (in_array($rol, [1,2,3,4,5,6])) {
                return $next($request);
            }
        }
        return redirect()->route('proveedor.login')->withErrors('Acceso denegado.');
    }
}
