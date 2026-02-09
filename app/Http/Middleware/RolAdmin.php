<?php

namespace App\Http\Middleware;

use Closure;

class RolAdmin
{
    public function handle($request, Closure $next)
    {
        if (session()->has('Usuario')) {
            $rol = session('Usuario')['IdRol'] ?? null;
            if (in_array($rol, [1 , 5])) {
                return $next($request);
            }
        }

        return redirect('/')->withErrors('Acceso solo para usuarios de Administrador.');
    }
}
