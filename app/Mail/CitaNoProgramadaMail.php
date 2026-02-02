<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class CitaNoProgramadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $proveedor;
    public $sucursal;
    public $fecha;
    public $motivo;
    public $vehiculos;
    public $evidencias;

    /**
     * Crear una nueva instancia del mensaje.
     */
    public function __construct($proveedor, $sucursal, $fecha, $motivo, $vehiculos, $evidencias)
    {
        $this->proveedor = $proveedor;
        $this->sucursal = $sucursal;
        $this->fecha = $fecha;
        $this->motivo = $motivo;
        $this->vehiculos = $vehiculos;
        $this->evidencias = $evidencias;
    }

    /**
     * Construir el mensaje.
     */
    public function build()
    {
        return $this->view('emails.cita_no_programada')
                    ->subject('Nueva Cita No Programada')
                    ->with([
                        'detalles' => [
                            'proveedor'  => $this->proveedor,
                            'sucursal'   => $this->sucursal,
                            'fecha'      => $this->fecha,
                            'motivo'     => $this->motivo,
                            'vehiculos'  => $this->vehiculos,
                            'evidencias' => $this->evidencias
                        ]
                    ]);
    }
}
