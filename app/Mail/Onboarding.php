<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Onboarding extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $cc;
    public $bcc;

    public function __construct(User $user, $cc = null, $bcc = null)
    {
        $this->user = $user;
        $this->cc = $cc;
        $this->bcc = $bcc;
    }

    public function build()
    {
        $mail = $this->view('emails.onboading')
            ->subject('Welcome to GroupSave!')
            ->with([
                'user' => $this->user,
            ]);

        // Add CC if provided
        if ($this->cc) {
            $mail->cc($this->cc);
        }

        // Add BCC if provided
        if ($this->bcc) {
            $mail->bcc($this->bcc);
        }

        return $mail;
    }
}
