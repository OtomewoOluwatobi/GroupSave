<?php

namespace App\Mail;

use App\Models\Group;
use App\Models\User;
use Illuminate\Mail\Mailable;

class GroupInvitation extends Mailable
{
    public $group;
    public $user;
    public $generatedPassword;

    public function __construct(Group $group, User $user, $generatedPassword)
    {
        $this->group = $group;
        $this->user = $user;
        $this->generatedPassword = $generatedPassword;
    }

    public function build()
    {
        return $this->view('emails.group-invitation')
            ->to($this->user->email)
            ->subject("You've been invited to join {$this->group->title}")
            ->with([
                'group' => $this->group,
                'user' => $this->user,
                'inviter' => auth()->guard()->user(),
                'generatedPassword' => $this->generatedPassword,
            ]);
    }
}

