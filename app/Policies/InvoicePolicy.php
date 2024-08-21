<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

use App\Models\User;

class InvoicePolicy
{
    public function manage(User $user): Response {
        return $user->hasPermission('manage_invoices')
                ? Response::allow()
                : Response::denyWithStatus(403);
    }
}