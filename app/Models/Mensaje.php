<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $table = 'mensajes';
    protected $connection = 'sqlsrv_proveedores';

    protected $fillable = [
        'remitente_id',
        'remitente_tipo',
        'receptor_id',
        'receptor_tipo',
        'mensaje',
        'fecha_envio'
    ];

    public $timestamps = false; 
}


