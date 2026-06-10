<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AkunBaruMail extends Mailable
{
    use Queueable, SerializesModels;

    public $dataEmail;

    // Menangkap data yang dilempar dari Controller
    public function __construct($dataEmail)
    {
        $this->dataEmail = $dataEmail;
    }

    public function build()
    {
        return $this->subject('Detail Login Akun Sistem Lab Anda')
                    ->view('akunEmail.email');
    }
}