<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SolicitudActualizacionProveedor extends Mailable
{
    use Queueable, SerializesModels;

    public $proveedor;
    public $campos;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($proveedor, $campos)
    {
        $this->proveedor = $proveedor;
        $this->campos = $campos;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Solicitud de Actualización de Datos del Proveedor')
                    ->view('emails.solicitud_actualizacion');
    }
}
