<?php
namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CalendarioSPController extends Controller
{
    public function index()
    {
        $sucursales = DB::connection('sqlsrv_proveedores')
            ->table('sucursales')
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get();

        return view('pages.compras.calendariosp', compact('sucursales'));
    }
    
    public function mostrarDisponibilidad(Request $request)
    {
        try {
            $sucursal_id = $request->get('sucursal_id');
            $fecha = $request->get('fecha', \Carbon\Carbon::today()->format('Y-m-d'));

            $sucursal = DB::connection('sqlsrv_proveedores')
                ->table('sucursales')
                ->where('id', $sucursal_id)
                ->first();

            $tablaDisponibilidad = DB::connection('sqlsrv_proveedores')->select(
                'EXEC sp_Consultar_tableroAndenes_Disponibilidad @sucursal_id = ?, @fecha = ?',
                [$sucursal_id, $fecha]
            );

            //Esta es la vista parcial 
            return view('pages.compras.partials.tabla_disponibilidad', compact('sucursal', 'fecha', 'tablaDisponibilidad'))->render();
        } catch (\Throwable $e) {
            return response("<div class='alert alert-danger p-2 text-center'>Error interno: {$e->getMessage()}</div>", 500);
        }
    }

    public function disponibilidad(Request $request)
    {
        try {
            $sucursal_id = $request->sucursal_id;
            $fecha = $request->fecha;

            $sucursal = DB::connection('sqlsrv_proveedores')
                ->table('sucursales')
                ->where('id', $sucursal_id)
                ->first();

            $tablaDisponibilidad = DB::connection('sqlsrv_proveedores')->select(
                'EXEC sp_Consultar_tableroAndenes_Disponibilidad @sucursal_id = ?, @fecha = ?',
                [$sucursal_id, $fecha]
            );

            return view('pages.compras.partials.tabla_disponibilidad', compact('sucursal', 'fecha', 'tablaDisponibilidad'))->render();
        } catch (\Throwable $e) {
            return response('<div class="alert alert-danger text-center">Error interno: ' . $e->getMessage() . '</div>', 500);
        }
    }
}
