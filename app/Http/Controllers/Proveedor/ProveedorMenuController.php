<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProveedorMenuController extends Controller
{
    public function menu()
    {
        return view('pages.proveedor.menu');
    }


}
