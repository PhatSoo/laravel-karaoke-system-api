<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

use App\Models\User;

class RolePolicy
{
    public function manage(User $user): Response {
        return $user->hasPermission('manage_roles')
                ? Response::allow()
                : Response::denyWithStatus(403);
    }
}
