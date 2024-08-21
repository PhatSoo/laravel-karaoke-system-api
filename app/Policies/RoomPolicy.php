<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

use App\Models\User;

class RoomPolicy
{
    public function manage(User $user): Response {
        return $user->hasPermission('manage_rooms')
                ? Response::allow()
                : Response::denyWithStatus(403);
    }
}