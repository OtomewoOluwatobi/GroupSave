<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Onboarding extends Mailable
{
    use SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->view('emails.onboading')
            ->to($this->user->email)
            ->subject('Welcome to GroupSave!')
            ->with([
                'user' => $this->user,
            ]);
    }
}

