<?php

namespace App\Mail;

use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GroupInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $group;
    public $user;
    public $generatedPassword;
    public $cc;
    public $bcc;

    public function __construct(Group $group, User $user, $generatedPassword, $cc = null, $bcc = null)
    {
        $this->group = $group;
        $this->user = $user;
        $this->generatedPassword = $generatedPassword;
        $this->cc = $cc;
        $this->bcc = $bcc;
    }

    public function build()
    {
        $mail = $this->view('emails.group-invitation')
            ->to($this->user->email)
            ->subject("You've been invited to join {$this->group->title}")
            ->with([
                'group' => $this->group,
                'user' => $this->user,
                'inviter' => auth()->guard()->user(),
                'generatedPassword' => $this->generatedPassword,
            ]);

        // Add CC if provided (must be non-null)
        if (!is_null($this->cc) && !empty($this->cc)) {
            $mail = $mail->cc($this->cc);
        }

        // Add BCC if provided (must be non-null)
        if (!is_null($this->bcc) && !empty($this->bcc)) {
            $mail = $mail->bcc($this->bcc);
        }

        return $mail;
    }
}