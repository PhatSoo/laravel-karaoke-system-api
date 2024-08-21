<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

use App\Models\User;

class BookingPolicy
{
    public function manage(User $user): Response {
        return $user->hasPermission('manage_bookings')
                ? Response::allow()
                : Response::denyWithStatus(403);
    }
}
