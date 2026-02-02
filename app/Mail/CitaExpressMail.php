<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CitaExpressMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sucursal;
    public $tipoEntrega;
    public $proveedor;
    public $fecha;
    public $hora;
    public $descripcion;
    public $evidencia;

    public function __construct($sucursal, $tipoEntrega, $proveedor, $fecha, $hora, $descripcion, $evidencia = null)
    {
        $this->sucursal    = $sucursal;
        $this->tipoEntrega = $tipoEntrega;
        $this->proveedor   = $proveedor;
        $this->fecha       = $fecha;
        $this->hora        = $hora;
        $this->descripcion = $descripcion;
        $this->evidencia   = $evidencia;
    }

    public function build()
    {
        return $this->view('emails.cita_express')
                    ->subject('Nueva Cita Express Registrada')
                    ->with([
                        'detalles' => [
                            'sucursal'     => $this->sucursal,
                            'tipo_entrega' => $this->tipoEntrega,
                            'proveedor'    => $this->proveedor,
                            'fecha'        => $this->fecha,
                            'hora'         => $this->hora,
                            'descripcion'  => $this->descripcion,
                            'evidencia'    => $this->evidencia
                        ]
                    ]);
    }
}
