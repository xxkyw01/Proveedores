<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EncriptarProveedores extends Command
{
    protected $signature = 'proveedores:encriptar-passwords';
    protected $description = 'Encripta todas las contraseñas de los proveedores que aún están en texto plano';

    public function handle()
    {
        $proveedores = DB::connection('sqlsrv_proveedores')
            ->table('proveedor_usuarios')
            ->get();

        $total = 0;
        foreach ($proveedores as $user) {
            if (strlen($user->password) < 60) { 
                DB::connection('sqlsrv_proveedores')
                    ->table('proveedor_usuarios')
                    ->where('id', $user->id)
                    ->update([
                        'password' => Hash::make($user->password),
                    ]);

                $this->info("✅ Encriptado usuario: {$user->username}");
                $total++;
            }
        }

        $this->info("🔐 Total de contraseñas actualizadas: {$total}");
    }
}

