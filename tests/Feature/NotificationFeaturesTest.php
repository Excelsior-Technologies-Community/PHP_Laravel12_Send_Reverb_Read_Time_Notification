<?php

namespace Tests\Feature;

use App\Events\NotificationEvent;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NotificationFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'email_notifications' => true,
            'sound_notifications' => true,
            'dnd_enabled' => false,
            'dnd_start_time' => '22:00',
            'dnd_end_time' => '08:00',
        ]);
    }

    public function test_send_notification_with_action_text_and_url(): void
    {
        Event::fake([NotificationEvent::class]);

        $payload = [
            'user_id' => $this->user->id,
            'message' => 'Your subscription invoice #1001 is ready.',
            'type' => 'info',
            'category' => 'payment',
            'priority' => 'high',
            'action_text' => 'View Invoice',
            'action_url' => 'https://example.com/invoices/1001',
        ];

        $response = $this->postJson('/api/send-notification', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Notification sent successfully.',
                'notification' => [
                    'message' => 'Your subscription invoice #1001 is ready.',
                    'action_text' => 'View Invoice',
                    'action_url' => 'https://example.com/invoices/1001',
                ],
            ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->user->id,
            'category' => 'payment',
            'priority' => 'high',
        ]);

        Event::assertDispatched(NotificationEvent::class, function ($event) {
            return $event->notification['action_text'] === 'View Invoice'
                && $event->notification['action_url'] === 'https://example.com/invoices/1001';
        });
    }

    public function test_advanced_filtering_and_sorting_notifications(): void
    {
        // Notification 1: General Low
        Notification::create([
            'type' => 'info',
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id,
            'category' => 'general',
            'priority' => 'low',
            'data' => ['message' => 'Welcome message'],
            'created_at' => now()->subDays(2),
        ]);

        // Notification 2: Security High Unread
        Notification::create([
            'type' => 'warning',
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id,
            'category' => 'security',
            'priority' => 'high',
            'data' => ['message' => 'Unrecognized login attempt', 'action_text' => 'Review Account', 'action_url' => '/security'],
            'created_at' => now()->subDay(),
        ]);

        // Notification 3: Payment Read
        Notification::create([
            'type' => 'success',
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id,
            'category' => 'payment',
            'priority' => 'medium',
            'data' => ['message' => 'Payment received'],
            'read_at' => now(),
            'created_at' => now(),
        ]);

        // Filter unread
        $unreadRes = $this->getJson("/api/user-notifications/{$this->user->id}?filter=unread");
        $unreadRes->assertStatus(200);
        $this->assertCount(2, $unreadRes->json('notifications'));

        // Filter high priority
        $highRes = $this->getJson("/api/user-notifications/{$this->user->id}?filter=high_priority");
        $highRes->assertStatus(200);
        $this->assertCount(1, $highRes->json('notifications'));
        $this->assertEquals('security', $highRes->json('notifications.0.category'));
        $this->assertEquals('Review Account', $highRes->json('notifications.0.action_text'));

        // Filter category security
        $secRes = $this->getJson("/api/user-notifications/{$this->user->id}?filter=security");
        $secRes->assertStatus(200);
        $this->assertCount(1, $secRes->json('notifications'));

        // Search
        $searchRes = $this->getJson("/api/user-notifications/{$this->user->id}?search=invoice_none");
        $searchRes->assertStatus(200);
        $this->assertCount(0, $searchRes->json('notifications'));
    }

    public function test_mark_all_notifications_as_read(): void
    {
        Notification::create([
            'type' => 'info',
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id,
            'category' => 'general',
            'priority' => 'low',
            'data' => ['message' => 'Unread 1'],
        ]);

        Notification::create([
            'type' => 'info',
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user->id,
            'category' => 'general',
            'priority' => 'low',
            'data' => ['message' => 'Unread 2'],
        ]);

        $this->assertEquals(2, Notification::whereNull('read_at')->count());

        $response = $this->postJson("/api/mark-all-read/{$this->user->id}");
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'affected_count' => 2,
            ]);

        $this->assertEquals(0, Notification::whereNull('read_at')->count());
    }

    public function test_user_notification_preferences_and_dnd_mode(): void
    {
        // 1. Get preferences
        $getRes = $this->getJson("/api/user-preferences/{$this->user->id}");
        $getRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'preferences' => [
                    'email_notifications' => true,
                    'sound_notifications' => true,
                    'dnd_enabled' => false,
                ],
            ]);

        // 2. Update preferences
        $updatePayload = [
            'email_notifications' => false,
            'sound_notifications' => false,
            'dnd_enabled' => true,
            'dnd_start_time' => '00:00',
            'dnd_end_time' => '23:59', // Active 24h for test
        ];

        $postRes = $this->postJson("/api/user-preferences/{$this->user->id}", $updatePayload);
        $postRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'preferences' => [
                    'email_notifications' => false,
                    'sound_notifications' => false,
                    'dnd_enabled' => true,
                    'is_dnd_active' => true,
                ],
            ]);

        $this->user->refresh();
        $this->assertTrue($this->user->isDndActive());
        $this->assertFalse($this->user->email_notifications);

        // 3. Email is skipped when email_notifications is false or DND is active
        Mail::fake();

        $this->postJson('/api/send-notification', [
            'user_id' => $this->user->id,
            'message' => 'Should not email',
            'type' => 'info',
            'category' => 'general',
            'priority' => 'low',
        ]);

        Mail::assertNothingSent();
    }
}
