<?php

namespace App\Http\Middleware;

use App\Helpers\RoleHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class CheckAdminPanel
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $role = RoleHelper::getPrimaryRoleName($user);

        // Check if user's role has admin panel access
        if ($role && RoleHelper::canAccessPanel($role, 'admin')) {
            return $next($request);
        }

        // Redirect non-admin users to their own dashboard
        $homeRoute = RoleHelper::getUserHomeRoute($user);
        if ($homeRoute) {
            if (str_starts_with($homeRoute, '/')) {
                return redirect()->to($homeRoute);
            }
            if (Route::has($homeRoute)) {
                return redirect()->route($homeRoute);
            }
        }

        abort(403);
    }
}
