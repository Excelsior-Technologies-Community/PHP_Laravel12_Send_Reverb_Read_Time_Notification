<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        // Broadcast to user-specific channel
        return new Channel('user.' . $this->notification['user_id']);
    }

    public function broadcastAs()
    {
        return 'notification.received';
    }

    public function broadcastWith()
    {
        return [
            'notification' => $this->notification
        ];
    }
}