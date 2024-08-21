<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

use App\Models\User;

class UserPolicy
{
    public function manage(User $user): Response {
        return $user->hasPermission('manage_users')
                ? Response::allow()
                : Response::denyWithStatus(403);
    }
}
