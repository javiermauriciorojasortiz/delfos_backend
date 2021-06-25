<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class msgUsuario extends Mailable
{
    use Queueable, SerializesModels;
    public $correo;
    public function __construct(Array $param)
    {
        $this->correo = $param;
    }
    public function build()
    {
        return $this->view('mails.correoUsuario');
    }
}
