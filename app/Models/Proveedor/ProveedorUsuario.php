<?php

namespace App\Models\Proveedor;

use Illuminate\Foundation\Auth\User as Authenticatable;

class ProveedorUsuario extends Authenticatable
{
    protected $table = 'proveedor_usuarios'; 
    protected $connection = 'sqlsrv_proveedores'; 

    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'CardCode',
        'activo',
        'creado_en',
        'precio_por_caja',
    ];

    protected $hidden = ['password'];


    public function getAuthIdentifierName()
    {
        return 'username'; 
    }
}
