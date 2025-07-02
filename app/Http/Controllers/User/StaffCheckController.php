<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffCheckController extends Controller
{
    /**
     * Check if current user is staff
     */
    public function check()
    {
        $user = Auth::user();
        
        $data = [
            'user_id' => $user->id,
            'username' => $user->username,
            'is_staff' => $user->is_staff ?? false,
            'role' => $user->role ?? 'none',
            'staff_methods' => [
                'isManager' => $user->isManager(),
                'isStaff' => $user->isStaff(),
            ]
        ];
        
        return response()->json($data);
    }
} 