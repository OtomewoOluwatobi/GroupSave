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

    public function __construct(Group $group, User $user)
    {
        $this->group = $group;
        $this->user = $user;
    }

    public function build()
    {
        return $this->view('emails.group-invitation')
            ->subject("You've been invited to join {$this->group->title}")
            ->with([
                'group' => $this->group,
                'user' => $this->user,
                'inviter' => auth()->guard()->user()
            ]);
    }
}