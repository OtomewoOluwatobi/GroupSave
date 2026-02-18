<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class Onboarding extends Mailable
{
    private string $name;
    private string $email;
    private string $verifyLink;

    public function __construct(string $name, string $email, string $verificationCode)
    {
        $this->name = $name;
        $this->email = $email;
        $this->verifyLink = 'https://phplaravel-1549794-6203025.cloudwaysapps.com/api/auth/verify/' . $verificationCode;
    }

    public function build()
    {
        return $this->view('emails.onboading')
            ->to($this->email)
            ->subject('Welcome to GroupSave!')
            ->with([
                'name' => $this->name,
                'email' => $this->email,
                'verifyLink' => $this->verifyLink,
            ]);
    }
}

