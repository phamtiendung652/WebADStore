<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailRegister extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $active_token;

    public function __construct($name, $active_token)
    {
        $this->name = $name;
        $this->active_token = $active_token;
    }

    public function build()
    {
        return $this->view('emails.register_success')
                    ->subject('Xác thực tài khoản')
                    ->with([
                        'name' => $this->name,
                        'active_token' => $this->active_token,
                    ]);
    }
}
