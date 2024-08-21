<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

use App\Models\User;

class PermissionPolicy
{
    public function manage(User $user): Response {
        return $user->hasPermission('manage_permissions')
                ? Response::allow()
                : Response::denyWithStatus(403);
    }
}
