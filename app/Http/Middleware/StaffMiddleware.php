<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StaffMiddleware
{
    /**
     * Handle an incoming request.
     * Only allow staff members (managers and staff) to access
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // If user is not logged in, redirect to login
        if (!Auth::check()) {
            return redirect()->route('user.login');
        }
        
        $user = Auth::user();
        
        // Debug logging
        Log::info('StaffMiddleware check', [
            'route' => $request->path(),
            'user_id' => $user->id,
            'is_staff' => $user->is_staff ?? false,
            'role' => $user->role ?? 'none'
        ]);
        
        // Check if user has staff flag
        if (!$user->is_staff) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }
            return redirect()->route('user.home')->with('error', 'Bạn không có quyền truy cập trang này');
        }
        
        return $next($request);
    }
} 