<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

use App\Models\User;

class StaffPolicy
{
    private $table_name = 'staffs';

    public function manage(User $user): Response {
        return $user->hasPermission($this->table_name)
                ? Response::allow()
                : Response::denyWithStatus(403);
    }
}