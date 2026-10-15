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
     * Send Notification (Supports Action URL, Buttons, Preferences and DND Check)
     */
    public function sendNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string|max:255',
            'type' => 'required|string',
            'category' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'action_text' => 'nullable|string|max:60',
            'action_url' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($request->user_id);
        $isDndActive = $user->isDndActive();

        $notificationData = [
            'message' => $request->message,
            'type' => $request->type,
            'action_text' => $request->action_text ?: null,
            'action_url' => $request->action_url ?: null,
            'sent_at' => now()->toDateTimeString(),
        ];

        $notification = Notification::create([
            'type' => $request->type,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'category' => $request->category,
            'priority' => $request->priority,
            'data' => $notificationData,
        ]);

        // Broadcast to Reverb user channel
        broadcast(new NotificationEvent([
            'id' => $notification->id,
            'user_id' => $user->id,
            'message' => $request->message,
            'type' => $request->type,
            'category' => $request->category,
            'priority' => $request->priority,
            'action_text' => $request->action_text ?: null,
            'action_url' => $request->action_url ?: null,
            'read' => false,
            'dnd_active' => $isDndActive,
            'sound_enabled' => (bool) $user->sound_notifications,
            'sent_at' => $notification->created_at->diffForHumans(),
        ]));

        // Email notification (Respects User Preferences & DND)
        $emailSent = false;
        if ($user->email_notifications && !$isDndActive) {
            try {
                Mail::to($user->email)->send(new NotificationMail($notification));
                $emailSent = true;
            } catch (\Throwable $e) {
                logger()->error('Failed to send notification email: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification sent successfully.',
            'email_sent' => $emailSent,
            'dnd_active' => $isDndActive,
            'notification' => [
                'id' => $notification->id,
                'user_id' => $user->id,
                'message' => $request->message,
                'type' => $request->type,
                'category' => $request->category,
                'priority' => $request->priority,
                'action_text' => $request->action_text,
                'action_url' => $request->action_url,
                'created_at' => $notification->created_at->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Mark Single Notification As Read
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);

        if ($notification->read_at) {
            return response()->json([
                'success' => true,
                'message' => 'Notification already marked as read.',
                'already_read' => true,
            ]);
        }

        $notification->update([
            'read_at' => now(),
        ]);

        // Broadcast Read Event to Reverb
        broadcast(new NotificationEvent([
            'id' => $notification->id,
            'user_id' => $notification->notifiable_id,
            'type' => 'notification_read',
            'read' => true,
            'read_at' => now()->diffForHumans(),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read successfully.',
        ]);
    }

    /**
     * Mark All Notifications As Read for a User
     */
    public function markAllAsRead($userId)
    {
        User::findOrFail($userId);

        $unreadCount = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->count();

        Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Broadcast to Reverb that all notifications are read
        broadcast(new NotificationEvent([
            'user_id' => (int) $userId,
            'type' => 'all_notifications_read',
            'read' => true,
            'read_at' => now()->diffForHumans(),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
            'affected_count' => $unreadCount,
        ]);
    }

    /**
     * Get User Notifications with Advanced Filters, Category Tabs, and Sorting
     */
    public function getUserNotifications(Request $request, $userId)
    {
        User::findOrFail($userId);

        $filter = $request->get('filter'); // 'all', 'unread', 'read', 'high_priority', 'security', 'system', 'general', 'payment'
        $category = $request->get('category');
        $priority = $request->get('priority');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $sort = $request->get('sort', 'newest'); // 'newest', 'oldest', 'priority'
        $search = $request->get('search');

        $query = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $userId);

        // Filter tabs logic
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        } elseif ($filter === 'high_priority') {
            $query->where('priority', 'high');
        } elseif (in_array($filter, ['security', 'system', 'general', 'payment'])) {
            $query->where('category', $filter);
        }

        // Additional category filter
        if (!empty($category) && $filter !== $category) {
            $query->where('category', $category);
        }

        // Additional priority filter
        if (!empty($priority)) {
            $query->where('priority', $priority);
        }

        // Date range filters
        if (!empty($fromDate)) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        // Search by message or action text
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('data->message', 'like', '%' . $search . '%')
                  ->orWhere('data->action_text', 'like', '%' . $search . '%');
            });
        }

        // Sorting
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'priority') {
            $query->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END")
                  ->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination (5 notifications per page)
        $notifications = $query->paginate(5);

        $notifications->getCollection()->transform(function ($notification) {
            $data = is_array($notification->data) ? $notification->data : [];

            return [
                'id' => $notification->id,
                'message' => $data['message'] ?? '',
                'type' => $notification->type,
                'category' => $notification->category,
                'priority' => $notification->priority,
                'action_text' => $data['action_text'] ?? null,
                'action_url' => $data['action_url'] ?? null,
                'sent_at' => $notification->created_at->diffForHumans(),
                'created_at_formatted' => $notification->created_at->format('Y-m-d H:i:s'),
                'read' => !is_null($notification->read_at),
                'read_at' => $notification->read_at ? $notification->read_at->diffForHumans() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'notifications' => $notifications->items(),
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
            'message' => 'Notification deleted successfully.',
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

        $highPriority = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $userId)
            ->where('priority', 'high')
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'statistics' => [
                'total' => $total,
                'read' => $read,
                'unread' => $unread,
                'today' => $today,
                'high_priority_unread' => $highPriority,
            ],
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
            'unread_count' => $count,
        ]);
    }

    /**
     * Get User Notification Preferences & DND Status
     */
    public function getPreferences($userId)
    {
        $user = User::findOrFail($userId);

        return response()->json([
            'success' => true,
            'preferences' => [
                'email_notifications' => (bool) $user->email_notifications,
                'sound_notifications' => (bool) $user->sound_notifications,
                'dnd_enabled' => (bool) $user->dnd_enabled,
                'dnd_start_time' => $user->dnd_start_time ?: '22:00',
                'dnd_end_time' => $user->dnd_end_time ?: '08:00',
                'is_dnd_active' => $user->isDndActive(),
            ],
        ]);
    }

    /**
     * Update User Notification Preferences & DND Mode
     */
    public function updatePreferences(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'email_notifications' => 'required|boolean',
            'sound_notifications' => 'required|boolean',
            'dnd_enabled' => 'required|boolean',
            'dnd_start_time' => 'nullable|string|max:5',
            'dnd_end_time' => 'nullable|string|max:5',
        ]);

        $user->update([
            'email_notifications' => $validated['email_notifications'],
            'sound_notifications' => $validated['sound_notifications'],
            'dnd_enabled' => $validated['dnd_enabled'],
            'dnd_start_time' => $validated['dnd_start_time'] ?: '22:00',
            'dnd_end_time' => $validated['dnd_end_time'] ?: '08:00',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification preferences updated successfully.',
            'preferences' => [
                'email_notifications' => (bool) $user->email_notifications,
                'sound_notifications' => (bool) $user->sound_notifications,
                'dnd_enabled' => (bool) $user->dnd_enabled,
                'dnd_start_time' => $user->dnd_start_time,
                'dnd_end_time' => $user->dnd_end_time,
                'is_dnd_active' => $user->isDndActive(),
            ],
        ]);
    }
}
