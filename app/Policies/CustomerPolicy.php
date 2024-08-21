<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

use App\Models\User;

class CustomerPolicy
{
    public function manage(User $user): Response {
        return $user->hasPermission('manage_customers')
                ? Response::allow()
                : Response::denyWithStatus(403);
    }
}