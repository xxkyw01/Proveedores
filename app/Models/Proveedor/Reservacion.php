<?php

namespace App\Models\Proveedor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Reservacion extends Model
{
    protected $connection = 'sqlsrv_proveedores';
    protected $table = 'reservaciones';
    public $timestamps = false;

    public static function obtenerReservaciones($fecha = null)
    {
        return collect(DB::connection('sqlsrv_proveedores')
            ->select('EXEC sp_consultar_reservaciones ?', [$fecha]));
    }
    
}
