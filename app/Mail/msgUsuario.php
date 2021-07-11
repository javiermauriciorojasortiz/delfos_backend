<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class msgUsuario extends Mailable
{
    use Queueable, SerializesModels;
    public $correo;
    public $vista;
    public function __construct(Array $param, string $subject, string $vista = 'mails.correoUsuario'){
        $this->correo = $param;
        $this->subject($subject);
        $this->vista = $vista;
    }
    public function build()
    {
        return $this->view($this->vista);
    }
}
