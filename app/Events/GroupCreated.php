<?php

namespace App\Events;

use App\Models\Group;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class GroupCreated
{
    use Dispatchable, InteractsWithSockets;

    public $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }
}