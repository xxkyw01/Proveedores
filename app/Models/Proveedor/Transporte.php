<?php

namespace App\Models\Proveedor;

use Illuminate\Database\Eloquent\Model;

class Transporte extends Model
{
    protected $connection = 'sqlsrv_proveedores';
    protected $table = 'transportes';
    public $timestamps = false;

    protected $fillable = ['nombre', 'duracion_minutos'];
}
