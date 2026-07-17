<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\NotificationMail;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    /**
     * Send Notification
     */
    public function sendNotification(Request $request)
    {

        $request->validate([

            'user_id' => 'required|exists:users,id',
            'message' => 'required|string|max:255',
            'type' => 'required|string',
            'category' => 'required|string',
            'priority' => 'required|in:low,medium,high'

        ]);

        $user = User::findOrFail(
            $request->user_id
        );

        $notification = Notification::create([

            'type' => $request->type,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'category' => $request->category,
            'priority' => $request->priority,

            'data' => [
                'message' => $request->message,
                'type' => $request->type,
                'sent_at' => now()->toDateTimeString()
            ]

        ]);

        broadcast(new NotificationEvent([
            'id' => $notification->id,
            'user_id' => $user->id,
            'message' => $request->message,
            'type' => $request->type,
            'category' => $request->category,
            'priority' => $request->priority,
            'read' => false,
            'sent_at' => $notification->created_at->diffForHumans()
        ]));

        // Email notification

        Mail::to($user->email)
            ->send(
                new NotificationMail($notification)
            );

        return response()->json([
            'success' => true,
            'message' => 'Notification sent successfully.',
            'notification' => $notification
        ]);
    }

    /**
     * Mark Notification As Read
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);

        if ($notification->read_at) {
            return response()->json([
                'success' => false,
                'message' => 'Notification already marked as read.'
            ]);
        }

        $notification->update([
            'read_at' => now()
        ]);

        // Broadcast Read Event
        broadcast(new NotificationEvent([
            'id' => $notification->id,
            'user_id' => $notification->notifiable_id,
            'type' => 'notification_read',
            'read' => true,
            'read_at' => now()->diffForHumans()
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read successfully.'
        ]);
    }

    /**
     * Get User Notifications
     * Search + Pagination
     */
    public function getUserNotifications(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $search = $request->get('search');

        $query = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $userId)
            ->orderBy('created_at', 'desc');

        // Search by notification message
        if (!empty($search)) {
            $query->where('data->message', 'like', '%' . $search . '%');
        }

        // Pagination (5 notifications per page)
        $notifications = $query->paginate(5);

        $notifications->getCollection()->transform(function ($notification) {
            return [
                'id' => $notification->id,
                'message' => $notification->data['message'] ?? '',
                'type' => $notification->type,

                // NEW
                'category' => $notification->category,
                'priority' => $notification->priority,

                'sent_at' => $notification->created_at->diffForHumans(),
                'read' => !is_null($notification->read_at),
                'read_at' => $notification->read_at
                    ? $notification->read_at->diffForHumans()
                    : null,
            ];
        });

        return response()->json([
            'success' => true,
            'notifications' => $notifications->items(),

            // Pagination Data
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'from' => $notifications->firstItem(),
                'to' => $notifications->lastItem(),
                'has_more_pages' => $notifications->hasMorePages(),
            ],
        ]);
    }

    /**
     * Delete Notification
     */
    public function deleteNotification($id)
    {
        $notification = Notification::findOrFail($id);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully.'
        ]);
    }

    /**
     * Dashboard Statistics
     */
    public function getStatistics($userId)
    {
        $total = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $userId)
            ->count();

        $read = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $userId)
            ->whereNotNull('read_at')
            ->count();

        $unread = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->count();

        $today = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $userId)
            ->whereDate('created_at', today())
            ->count();

        return response()->json([
            'success' => true,
            'statistics' => [
                'total' => $total,
                'read' => $read,
                'unread' => $unread,
                'today' => $today,
            ]
        ]);
    }

    /**
     * Get Unread Count
     */
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
