<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffRoleMiddleware
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
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('user.login');
        }
        
        // Check if user has staff role
        if (!$user->staff_role || $user->staff_role != $role) {
            $notify[] = ['error', 'You are not authorized to access this page'];
            return redirect()->route('user.home')->withNotify($notify);
        }
        
        return $next($request);
    }
} 