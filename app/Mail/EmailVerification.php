<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class EmailVerification extends Mailable
{
    private string $name;
    private string $email;
    private string $verificationCode;

    public function __construct(string $name, string $email, string $verificationCode)
    {
        $this->name = $name;
        $this->email = $email;
        $this->verificationCode = $verificationCode;
    }

    public function build()
    {
        return $this->view('emails.email_verification')
            ->to($this->email)
            ->subject('Verify Your Email Address')
            ->with([
                'name' => $this->name,
                'email' => $this->email,
                'verificationCode' => $this->verificationCode,
            ]);
    }
}
