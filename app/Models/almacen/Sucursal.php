<?php

namespace App\Models\almacen;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursales';
    protected $connection = 'sqlsrv_proveedores';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'domicilio',
        'hora_inicio',
        'hora_fin',
        'trabaja_sabado',
        'trabaja_domingo',
        'hora_inicio_sabado',
        'hora_fin_sabado',
    ];
}
