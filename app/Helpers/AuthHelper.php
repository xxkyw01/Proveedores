<?php

namespace App\Helpers;

class AuthHelper
{
    public static function tipoUsuario()
    {
        if (session()->has('Proveedor')) {
            return 'proveedor';
        } elseif (session()->has('Usuario')) {
            return 'admin';
        } else {
            return null;
        }
    }
}
