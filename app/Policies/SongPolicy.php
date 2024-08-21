<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

use App\Models\User;

class SongPolicy
{
    public function manage(User $user): Response {
        return $user->hasPermission('manage_songs')
                ? Response::allow()
                : Response::denyWithStatus(403);
    }
}
