<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

use App\Models\User;

class ProductPolicy
{
    public function manage(User $user): Response {
        return $user->hasPermission('manage_inventory')
                ? Response::allow()
                : Response::denyWithStatus(403);
    }
}