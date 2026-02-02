<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CorreoCitaConfirmada extends Mailable
{
    use Queueable, SerializesModels;

    public $fecha, $sucursal, $horarios , $proveedor;

    public function __construct($fecha, $sucursal, $horarios, $proveedor)
    {
        $this->fecha = $fecha;
        $this->sucursal = $sucursal;
        $this->horarios = $horarios;
        $this ->proveedor = $proveedor;
    }


    public function build()
    {

    return $this->view('emails.cita_pendiente')
            ->subject('Cita Pendiente')
            ->with([
                'fecha' => $this->fecha,
                'sucursal' => $this->sucursal,
                'horarios' => $this->horarios,
                'proveedor' => $this->proveedor
            ]);
    }


}
