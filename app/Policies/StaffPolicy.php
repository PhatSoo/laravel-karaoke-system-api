<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

use App\Models\User;

class StaffPolicy
{
    public function manage(User $user): Response {
        return $user->hasPermission('manage_staffs')
                ? Response::allow()
                : Response::denyWithStatus(403);
    }
}
