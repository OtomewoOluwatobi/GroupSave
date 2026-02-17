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
    private $cc;
    private $bcc;

    public function __construct(User $user, $cc = null, $bcc = null)
    {
        $this->user = $user;
        $this->cc = $cc;
        $this->bcc = $bcc;
    }

    public function build()
    {
        $mail = $this->view('emails.onboading')
            ->to($this->user->email)
            ->subject('Welcome to GroupSave!')
            ->with([
                'user' => $this->user,
            ]);

        // Add CC if provided (must be non-null and non-empty)
        if (!is_null($this->cc) && !empty($this->cc)) {
            $mail = $mail->cc($this->cc);
        }

        // Add BCC if provided (must be non-null and non-empty)
        if (!is_null($this->bcc) && !empty($this->bcc)) {
            $mail = $mail->bcc($this->bcc);
        }

        return $mail;
    }
}
