<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;

class ProveedorAuthController extends Controller
{
    private function validateRecaptcha(Request $request): bool
    {
        $token = $request->input('g-recaptcha-response'); // v2 manda este campo solo
        if (!$token) return false;

        $resp = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $token,
            'remoteip' => $request->ip(), // opcional
        ])->json();

        return (bool)($resp['success'] ?? false);
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'user' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        if (!$this->validateRecaptcha($request)) {
            return redirect()
                ->back()
                ->withErrors(['errorMsg' => 'Verificación reCAPTCHA fallida. Por favor, inténtalo de nuevo.'])
                ->withInput($request->only('user'));
        }

        $user = $request->input('user');
        $password = $request->input('password');
        $proveedor = DB::connection('sqlsrv_proveedores')->table('proveedor_usuarios')
            ->where('username', $user)
            ->where('activo', 'Y')
            ->first();

        if ($proveedor && Hash::check($password, $proveedor->password)) {
            session(['Proveedor' => [
                'id' => $proveedor->id,
                'username' => $proveedor->username,
                'CardCode' => $proveedor->CardCode
            ]]);

            session(['proveedor_id' => $proveedor->id]);
            $request->session()->regenerate();
            return redirect()->route('proveedor.dashboard');
        }

        $admin = DB::connection('sqlsrv_proveedores')->table('usuarios')
            ->where('Codigo', $user)
            ->where('Activo', 'Y')
            ->where('IdRol', 1)
            ->first();

        if ($admin && Hash::check($password, $admin->Clave)) {
            session(['Usuario' => [
                'IdUsuario' => $admin->IdUsuario,
                'Codigo' => $admin->Codigo,
                'Nombre' => $admin->Nombre,
                'IdRol' => $admin->IdRol,
            ]]);

            session(['usuario_id' => $admin->IdUsuario]);
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        $almacen = DB::connection('sqlsrv_proveedores')->table('usuarios')
            ->where('Codigo', $user)
            ->where('Activo', 'Y')
            ->where('IdRol', 2)
            ->first();

        if ($almacen && Hash::check($password, $almacen->Clave)) {
            session(['Usuario' => [
                'IdUsuario' => $almacen->IdUsuario,
                'Codigo' => $almacen->Codigo,
                'Nombre' => $almacen->Nombre,
                'IdRol' => $almacen->IdRol,
                'SucursalID' => $almacen->id_sucursal,
            ]]);

            session(['usuario_id' => $almacen->IdUsuario]);
            $request->session()->regenerate();
            return redirect()->route('almacen.dashboard');
        }

        $compras = DB::connection('sqlsrv_proveedores')->table('usuarios')
            ->where('Codigo', $user)
            ->where('Activo', 'Y')
            ->where('IdRol', 3)
            ->first();

        if ($compras && Hash::check($password, $compras->Clave)) {
            session(['Usuario' => [
                'IdUsuario' => $compras->IdUsuario,
                'Codigo' => $compras->Codigo,
                'Nombre' => $compras->Nombre,
                'IdRol' => $compras->IdRol,
            ]]);

            session(['usuario_id' => $compras->IdUsuario]);
            $request->session()->regenerate();
            return redirect()->route('compras.dashboard');
        }

        $mejora = DB::connection('sqlsrv_proveedores')->table('usuarios')
            ->where('Codigo', $user)
            ->where('Activo', 'Y')
            ->where('IdRol', 4)
            ->first();

        if ($mejora && Hash::check($password, $mejora->Clave)) {
            session(['Usuario' => [
                'IdUsuario' => $mejora->IdUsuario,
                'Codigo' => $mejora->Codigo,
                'Nombre' => $mejora->Nombre,
                'IdRol' => $mejora->IdRol,
                'SucursalID' => $mejora->id_sucursal,
            ]]);

            session(['usuario_id' => $mejora->IdUsuario]);
            $request->session()->regenerate();
            return redirect()->route('mejora.dashboard');
        }

        $dev = DB::connection('sqlsrv_proveedores')->table('usuarios')
            ->where('Codigo', $user)
            ->where('Activo', 'Y')
            ->where('IdRol', 5)
            ->first();

        if ($dev && Hash::check($password, $dev->Clave)) {
            session(['Usuario' => [
                'IdUsuario' => $dev->IdUsuario,
                'Codigo' => $dev->Codigo,
                'Nombre' => $dev->Nombre,
                'IdRol' => $dev->IdRol,
                'SucursalID' => $dev->id_sucursal,
            ]]);

            session(['usuario_id' => $dev->IdUsuario]);
            $request->session()->regenerate();
            return redirect()->route('dev.dashboard');
        }

        $auditoria = DB::connection('sqlsrv_proveedores')->table('usuarios')
            ->where('Codigo', $user)
            ->where('Activo', 'Y')
            ->where('IdRol', 7)
            ->first();

        if ($auditoria && Hash::check($password, $auditoria->Clave)) {
            session(['Usuario' => [
                'IdUsuario' => $auditoria->IdUsuario,
                'Codigo' => $auditoria->Codigo,
                'Nombre' => $auditoria->Nombre,
                'IdRol' => $auditoria->IdRol,
                'SucursalID' => $auditoria->id_sucursal,
            ]]);

            session(['usuario_id' => $auditoria->IdUsuario]);
            $request->session()->regenerate();
            return redirect()->route('auditoria.dashboard');
        }

        $monitor = DB::connection('sqlsrv_proveedores')->table('usuarios')
            ->where('Codigo', $user)
            ->where('Activo', 'Y')
            ->where('IdRol', 8)
            ->first();
        if ($monitor && Hash::check($password, $monitor->Clave)) {
            session(['Usuario' => [
                'IdUsuario' => $monitor->IdUsuario,
                'Codigo' => $monitor->Codigo,
                'Nombre' => $monitor->Nombre,
                'IdRol' => $monitor->IdRol,
                'SucursalID' => $monitor->id_sucursal,
            ]]);
            session(['usuario_id' => $monitor->IdUsuario]);
            $request->session()->regenerate();
            return redirect()->route('monitor.dashboard');
        }

        return redirect()
            ->back()
            ->withErrors(['errorMsg' => 'Usuario o contraseña incorrectos'])
            ->withCookie(Cookie::forget('laravel_log'))
            ->withCookie(Cookie::forget('laravel_key'))
            ->withCookie(Cookie::forget('laravel_check'));
    }

    public function logout(Request $request)
    {
        //Auth::logout(); // Cierra la sesión de Laravel
        $request->session()->invalidate(); // Invalida la sesión actual
        $request->session()->flush(); // Elimina todos los datos de la sesión
        $request->session()->regenerateToken(); // Regenera el token CSRF para seguridad
        return redirect('/')->with([
            'message' => 'Has cerrado sesión exitosamente.',
            'alert-type' => 'success'
        ]);
    }

    public static function redirect()
    {
        if (session()->has('Proveedor')) {
            return redirect()->route('proveedor.dashboard');
        } elseif (session()->has('Usuario')) {
            $rol = session('Usuario.IdRol');
            return match ($rol) {
                1 => redirect()->route('admin.dashboard'),
                2 => redirect()->route('almacen.dashboard'),
                3 => redirect()->route('compras.dashboard'),
                4 => redirect()->route('mejora.dashboard'),
                5 => redirect()->route('dev.dashboard'),
                7 => redirect()->route('auditoria.dashboard'),
                8 => redirect()->route('monitor.dashboard'),
                default => redirect('/login')->withErrors(['errorMsg' => 'Rol de usuario no reconocido.']),
            };
        } else {
            return redirect('/login');
        }
    }
}
