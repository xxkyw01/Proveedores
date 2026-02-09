<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class ReporteManiobrasController extends Controller
{
    public function reporteManiobras(Request $request)
    {
        $fechaInicio = $request->input('fechaInicio');
        $fechaFin    = $request->input('fechaFin');

        $datos = collect(DB::connection('sqlsrv_proveedores')
            ->select('EXEC sp_reporte_maniobras_compras @fechaInicio = ?, @fechaFin = ?', [
                $fechaInicio,
                $fechaFin
            ]));

        return view('pages.compras.reporte_maniobras', compact('datos'));
    }

    public function exportExcel(Request $request)
    {
        try {
            $fechaInicio = $request->input('fechaInicio', Carbon::now()->startOfMonth()->toDateString());
            $fechaFin    = $request->input('fechaFin', Carbon::now()->toDateString());

            //Log::info("📤 Exportando reporte desde $fechaInicio hasta $fechaFin");

            $datos = DB::connection('sqlsrv_proveedores')->select("EXEC sp_reporte_maniobras_compras ?, ?", [
                $fechaInicio,
                $fechaFin
            ]);

            if (empty($datos)) {
                return redirect()->back()->with('error', 'No hay datos disponibles para exportar.');
            }


            $response = new StreamedResponse(function () use ($datos) {

                if (ob_get_level()) ob_end_clean();

                $handle = fopen('php://output', 'w');
                fwrite($handle, "\xEF\xBB\xBF"); 

                fputcsv($handle, [
                    'Folio',
                    'Fecha',
                    'Sucursal',
                    'Proveedor',
                    'Transporte',
                    'Andén',
                    'Monto Maniobra',
                    'Ordenes de Compra',
                ]);


                foreach ($datos as $r) {
                    fputcsv($handle, [
                        $r->reservacion_id,
                        Carbon::parse($r->Fecha_Recepcion)->format('d-m-Y'),
                        $r->Sucursal,
                        $r->Proveedor,
                        $r->Tipo_Transporte,
                        $r->Anden,
                        $r->Monto_Maniobra,
                        $r->Ordenes_Compra,
                    ]);
                }

                fclose($handle);
            });

            $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
            $response->headers->set('Content-Disposition', 'attachment; filename="reporte_maniobras.csv"');
            $response->headers->set('Cache-Control', 'no-store, no-cache');

            return $response;
        } catch (\Throwable $e) {
            //Log::error('❌ Error al exportar reporte maniobras: ' . $e->getMessage());
            abort(500, 'Error al generar el reporte.');
        }
    }
}
