<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications for the dropdown.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('sent_at')
            ->take(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->translated_title,
                    'content' => $notification->translated_content,
                    'is_read' => $notification->is_read,
                    'sent_at' => $notification->sent_at,
                    'read_at' => $notification->read_at,
                ];
            });
        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Display the full notifications page.
     */
    public function viewAll(Request $request)
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('sent_at')
            ->paginate(20);

        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('notifications.index', compact('notifications'));
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

    /**
     * Clear all notifications for the user.
     */
    public function clearAll(Request $request)
    {
        $user = Auth::user();
        Notification::where('user_id', $user->id)->delete();

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('notifications.viewAll')
            ->with('success', __('notifications.notifications_cleared'));
    }
}
