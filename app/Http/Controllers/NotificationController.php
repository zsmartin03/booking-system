<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('sent_at')
            ->take(20)
            ->get();
        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Mark all notifications as read for the user.
     */
    public function markAllRead(Request $request)
    {
        $user = Auth::user();
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['success' => true]);
    }
}
