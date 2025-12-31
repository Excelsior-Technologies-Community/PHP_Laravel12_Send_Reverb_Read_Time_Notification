<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Send notification
    public function sendNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string|max:255',
            'type' => 'required|string'
        ]);

        $user = User::find($request->user_id);

        // Create notification in database
        $notification = Notification::create([
            'type' => $request->type,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => [
                'message' => $request->message,
                'type' => $request->type,
                'sent_at' => now()->toDateTimeString()
            ]
        ]);

        // Prepare data for broadcast
        $broadcastData = [
            'id' => $notification->id,
            'user_id' => $user->id,
            'message' => $request->message,
            'type' => $request->type,
            'sent_at' => $notification->created_at->diffForHumans(),
            'read' => false
        ];

        // Broadcast the event
        broadcast(new NotificationEvent($broadcastData));

        return response()->json([
            'success' => true,
            'message' => 'Notification sent successfully',
            'notification' => $notification
        ]);
    }

    // Mark notification as read
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);

            // Broadcast read status update
            broadcast(new NotificationEvent([
                'id' => $notification->id,
                'user_id' => $notification->notifiable_id,
                'type' => 'notification_read',
                'read' => true,
                'read_at' => now()->diffForHumans()
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notification already read'
        ]);
    }

    // Get user notifications
    public function getUserNotifications($userId)
    {
        $user = User::findOrFail($userId);
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'message' => $notification->data['message'],
                    'type' => $notification->type,
                    'sent_at' => $notification->created_at->diffForHumans(),
                    'read' => !is_null($notification->read_at),
                    'read_at' => $notification->read_at ? $notification->read_at->diffForHumans() : null
                ];
            });

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    // Get unread count
    public function getUnreadCount($userId)
    {
        $count = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count
        ]);
    }
}