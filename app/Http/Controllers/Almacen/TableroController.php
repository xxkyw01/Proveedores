<?php 
namespace App\Http\Controllers\Almacen;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\almacen\Sucursal;

class TableroController extends Controller
{


    public function index(Request $request)
    {
        $rolId = session('Usuario.IdRol');
        $sucursalUsuario = session('Usuario.SucursalID');
         // Si el rol es Admin, Compras, Dev -> puede elegir sucursal
        if (in_array($rolId, [1, 3, 4, 5])) {
            $sucursal_id = $request->get('sucursal_id') ?? 1; // valor por defecto
        } else {
            $sucursal_id = $sucursalUsuario; // almacén
        }

        $sucursales = Sucursal::all();
        return redirect()->route('almacen.tablero.mostrar', [
            'sucursal_id' => $sucursal_id,
            'sucursales' => $sucursales
        ]);
    }


    public function mostrar(Request $request)
    {
        $sucursal_id = $request->get('sucursal_id');
        $rolId = session('Usuario.IdRol');
        $sucursalUsuario = session('Usuario.SucursalID');

        // Almacén forzamos su sucursal si no mandó nada
        if ($rolId == 2 && !$sucursal_id) {
            $sucursal_id = $sucursalUsuario;
        }

        if (!$sucursal_id) {
            return redirect()->route('almacen.tablero.tabla')->with('error', 'Sucursal no seleccionada');
        }

        \Carbon\Carbon::setLocale('es');

        $sucursales = DB::connection('sqlsrv_proveedores')
            ->table('sucursales')
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get();

        $sucursal = DB::connection('sqlsrv_proveedores')
            ->table('sucursales')
            ->where('id', $sucursal_id)
            ->first();

        $fecha = \Carbon\Carbon::today()->format('Y-m-d');

        $tablaDisponibilidad = DB::connection('sqlsrv_proveedores')->select(
            'EXEC sp_Consultar_tableroAndenes_Disponibilidad @sucursal_id = ?, @fecha = ?',
            [$sucursal_id, $fecha]
        );

        return view('pages.almacen.tablero', compact('sucursales', 'sucursal', 'fecha', 'tablaDisponibilidad'));
    }

    public function parcial(Request $request)
    {
        $sucursal_id = $request->get('sucursal_id');
        $fecha = now()->format('Y-m-d');

        $tablaDisponibilidad = DB::connection('sqlsrv_proveedores')->select(
    'EXEC sp_Consultar_tableroAndenes_Disponibilidad @sucursal_id = ?, @fecha = ?',
    [$sucursal_id, $fecha]
    );
    return view('pages.almacen.partials.tablaDisponibilidad', compact('tablaDisponibilidad'));
    }

}