<?php

namespace App\Http\Controllers\Almacen;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\almacen\Sucursal;


class DashboardController extends Controller
{
    public function KPIDashboard(Request $request)
{
    $fi = $request->input('inicio') ?? now()->startOfMonth()->toDateString();
    $ff = $request->input('fin')    ?? now()->endOfMonth()->toDateString();
    $sucursalesLista = Sucursal::select('id','nombre')->orderBy('nombre')->get();
    $sucursalId = $request->filled('sucursal') ? (int) $request->input('sucursal') : null;

    $prov = $request->input('proveedor') ?: null;
    $topN = 10;
    $rows = DB::connection('sqlsrv_proveedores')->select(
        'EXEC [IntranetProveedores].[dbo].[sp_kpis_reservaciones_v2] ?, ?, ?, ?, ?',
        [$fi, $ff, $sucursalId, $prov, $topN]
    );

    $data = json_decode($rows[0]->json ?? '{}', true);

    return view('includes.Dashboard.Dashboardinterno', [
        'totales'         => $data['totales'] ?? [],
        'donutEventos'    => $data['donutEventos'] ?? [],
        'panels'          => $data['panels'] ?? [],
        'tendencia'       => $data['tendenciaSemanal'] ?? [],
        'sucursalesStack' => $data['sucursalesStack'] ?? [],
        'topAsistieron'   => $data['topAsistieron'] ?? [],
        'topCancelados'   => $data['topCancelados'] ?? [],
        'tablaSemanal'    => $data['tablaSemanal'] ?? [],
        'fechaInicio'     => $fi,
        'fechaFin'        => $ff,
        'sucursales'      => $sucursalesLista,
        'sucursalId'      => $sucursalId,
    ]);
}
}
