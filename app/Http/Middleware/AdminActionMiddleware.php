<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminActionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Auth::guard('admin')->user();
        
        // Nếu không phải admin hoặc là phó tổng giám đốc (chỉ có quyền xem)
        if (!$admin->canPerformAction()) {
            $notify[] = ['error', 'Bạn không có quyền thực hiện hành động này.'];
            return back()->withNotify($notify);
        }

        return $next($request);
    }
}
