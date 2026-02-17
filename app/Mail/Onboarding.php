<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;

class Onboarding extends Mailable
{
    public function __construct(private User $user, private string $verificationCode)
    {
    }

    public function build()
    {
        $verifyLink = 'https://phplaravel-1549794-6203025.cloudwaysapps.com/api/auth/verify/' . $this->verificationCode;
        
        return $this->view('emails.onboading')
            ->to($this->user->email)
            ->subject('Welcome to GroupSave!')
            ->with([
                'name' => $this->user->name,
                'email' => $this->user->email,
                'verifyLink' => $verifyLink,
            ]);
    }
}

