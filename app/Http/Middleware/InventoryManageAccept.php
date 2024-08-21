<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

use App\Helpers\APIHelper;
use App\Models\User;

class InventoryManageAccept
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    private $ROLE_REQUIRED = [
        '01_admin',
        '03_inventory_management'
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $id = Auth::id();
        $user_roles = User::find($id)->roles->pluck('key')->toArray();
        $check_roles = array_intersect($this->ROLE_REQUIRED, $user_roles);

        if (count($check_roles) === 0) {
            return APIHelper::errorResponse(statusCode:403, message: 'You have no permission for this function!');
        }

        return $next($request);
    }
}