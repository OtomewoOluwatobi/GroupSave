<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PasswordReset extends Mailable
{
    private string $name;
    private string $email;
    private string $resetCode;

    public function __construct(string $name, string $email, string $resetCode)
    {
        $this->name = $name;
        $this->email = $email;
        $this->resetCode = $resetCode;
    }

    public function build()
    {
        return $this->view('emails.password_reset')
            ->to($this->email)
            ->subject('Password Reset Request')
            ->with([
                'name' => $this->name,
                'email' => $this->email,
                'resetCode' => $this->resetCode,
            ]);
    }
}
