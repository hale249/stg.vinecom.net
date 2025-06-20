<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStaffRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check() || !Auth::user()->is_staff) {
            return to_route('user.login');
        }

        // Check if user has the required role
        if (Auth::user()->role !== $role) {
            if (Auth::user()->role === 'sales_manager') {
                return to_route('user.staff.manager');
            } elseif (Auth::user()->role === 'sales_staff') {
                return to_route('user.staff.staff');
            } else {
                return to_route('user.home');
            }
        }

        return $next($request);
    }
} 