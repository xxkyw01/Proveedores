<?php

namespace App\Models\proveedor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entidad extends Model
{
    use HasFactory;

    protected $connection = 'sqlsrv_proveedores'; 
    protected $table = 'entidades';
    
    protected $fillable = [
        'nombre',
        'descripcion'
    ];
}