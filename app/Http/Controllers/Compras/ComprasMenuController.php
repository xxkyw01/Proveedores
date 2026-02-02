<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProveedorMenuController extends Controller
{
    public function menu()
    {
        return view('pages.compras.menu');
    }
}
