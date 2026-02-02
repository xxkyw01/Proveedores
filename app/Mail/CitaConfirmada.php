<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CitaConfirmada extends Mailable
{
    use Queueable, SerializesModels;

    public $saludo, $proveedor, $idReservacion, $fechaCompleta, $lugar, $anden, $hora, $vehiculo, $comentario;

    public function __construct($saludo, $proveedor, $idReservacion, $fechaCompleta, $lugar, $anden, $hora, $vehiculo, $comentario)
    {
        $this->saludo = $saludo;
        $this->proveedor = $proveedor;
        $this->idReservacion = $idReservacion;
        $this->fechaCompleta = $fechaCompleta;
        $this->lugar = $lugar;
        $this->anden = $anden;
        $this->hora = $hora;
        $this->vehiculo = $vehiculo;
        $this->comentario = $comentario;
    }

    public function build()
    {
        return $this->view('emails.cita_confirmada')
                    ->subject('Cita Confirmada')
                    ->with([
                        'saludo' => $this->saludo,
                        'proveedor' => $this->proveedor,
                        'idReservacion' => $this->idReservacion,
                        'fechaCompleta' => $this->fechaCompleta,
                        'lugar' => $this->lugar,
                        'anden' => $this->anden,
                        'hora' => $this->hora,
                        'vehiculo' => $this->vehiculo,
                        'comentario' => $this->comentario,
                    ]);
    }
}
