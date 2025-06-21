<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications
     */
    public function index()
    {
        $pageTitle = 'Notifications';
        $user = Auth::user();
        $general = gs();
        
        $notifications = $user->notifications()
            ->latest()
            ->paginate(getPaginate());
            
        $emptyMessage = 'No notifications found';
        
        return view('user.staff.notifications.index', compact('pageTitle', 'notifications', 'emptyMessage', 'general'));
    }
    
    /**
     * Display the specified notification
     */
    public function show($id)
    {
        $user = Auth::user();
        $general = gs();
        
        $notification = $user->notifications()
            ->where('id', $id)
            ->firstOrFail();
            
        // Mark as read
        $notification->update(['user_read' => 1]);
        
        $pageTitle = 'Notification Details';
        
        return view('user.staff.notifications.show', compact('pageTitle', 'notification', 'general'));
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        
        $notification = $user->notifications()
            ->where('id', $id)
            ->firstOrFail();
            
        $notification->update(['user_read' => 1]);
        
        $notify[] = ['success', 'Notification marked as read'];
        return back()->withNotify($notify);
    }
} 