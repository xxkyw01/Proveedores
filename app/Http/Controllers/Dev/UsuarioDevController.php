<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioDevController extends Controller
{
    public function formulario()
    {
        return view('pages.dev.crear_usuario');
    }

    public function crear(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:100',
            'password' => 'required|string|min:8',
            'rol'      => 'required|in:proveedor,admin,almacen,compras'
        ]);

        $username = $request->username;

        if ($request->rol === 'proveedor') {
            $existe = DB::connection('sqlsrv_proveedores')->table('proveedor_usuarios')
                ->where('username', $username)
                ->exists();

            if ($existe) {
                return back()->withErrors(['username' => 'Ya existe un proveedor con ese usuario.']);
            }

            DB::connection('sqlsrv_proveedores')->table('proveedor_usuarios')->insert([
                'username'   => $username,
                'password'   => Hash::make($request->password),
                'CardCode'   => $request->cardcode ?? 'EditarCodigo',
                'activo'     => 'Y',
                'creado_en'  => now()
            ]);

        } else {
            $existe = DB::connection('sqlsrv_proveedores')->table('usuarios')
                ->where('Codigo', $username)
                ->exists();

            if ($existe) {
                return back()->withErrors(['username' => 'Ya existe un usuario interno con ese código.']);
            }

            DB::connection('sqlsrv_proveedores')->table('usuarios')->insert([
                'Codigo'      => $username,
                'Clave'       => Hash::make($request->password),
                'Nombre'      => $username,
                'Activo'      => 'Y',
                'IdRol'       => $this->mapRol($request->rol),
                'id_sucursal' => 1,
                'FechaCreacion' => now()
            ]);
        }

        return back()->with('success', 'Usuario creado correctamente con contraseña segura.');
    }

    public function listarUsuarios()
    {
        $proveedores = DB::connection('sqlsrv_proveedores')->table('proveedor_usuarios')->get();
        $internos = DB::connection('sqlsrv_proveedores')->table('usuarios')->get();

        return view('pages.dev.consultar_usuarios', compact('proveedores', 'internos'));
    }

    public function cambiarPasswordUsuario(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'rol' => 'required|in:proveedor,interno',
            'password' => 'required|string|min:8'
        ]);

        $hash = Hash::make($request->password);

        if ($request->rol === 'proveedor') {
            DB::connection('sqlsrv_proveedores')->table('proveedor_usuarios')
                ->where('id', $request->id)
                ->update(['password' => $hash]);
        } else {
            DB::connection('sqlsrv_proveedores')->table('usuarios')
                ->where('IdUsuario', $request->id)
                ->update(['Clave' => $hash]);
        }

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

    private function mapRol($rol)
    {
        return match ($rol) {
            'admin'   => 1,
            'almacen' => 2,
            'compras' => 3,
            'mejora' => 4,
            'dev' => 5,
            default   => null,
        };
    }

}
